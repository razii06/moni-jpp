<?php

namespace App\Http\Controllers;

use App\Models\JobPackage;
use App\Models\ActivityLog;
use App\Services\GoogleDriveService;
use App\Services\JobPackageExportService;
use App\Http\Requests\UpdateJobPackageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class JobPackageController extends Controller
{
    private array $docFields = [
        'doc_rab', 'doc_bak', 'doc_surat_permintaan',
        'doc_surat_izin_prinsip', 'doc_tor', 'doc_bast',
    ];

    public function index(Request $request)
    {
        $statusFilter = $request->get('status_filter', 'semua');
        $search = $request->get('search');
        $sort = $request->get('sort', 'latest');

        $baseQuery = JobPackage::with(['creator', 'pos', 'suratBakDocs', 'permintaanDaris']);

        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('job_package', 'like', "%{$search}%")
                    ->orWhere('no_service_notifikasi', 'like', "%{$search}%")
                    ->orWhere('no_service_order', 'like', "%{$search}%")
                    ->orWhere('no_po', 'like', "%{$search}%")
                    ->orWhere('no_surat_bak_doc', 'like', "%{$search}%")
                    ->orWhere('latest_activity', 'like', "%{$search}%")
                    ->orWhereHas('pos', function ($poQuery) use ($search) {
                        $poQuery->where('no_po', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('suratBakDocs', function ($suratQuery) use ($search) {
                        $suratQuery->where('no_surat_bak_doc', 'like', "%{$search}%");
                    })
                    ->orWhereHas('permintaanDaris', function ($permintaanQuery) use ($search) {
                        $permintaanQuery->where('permintaan_dari', 'like', "%{$search}%");
                    });
            });
        }

        $applySort = function ($query) use ($sort) {
            match ($sort) {
                'oldest'        => $query->oldest(),
                'progress_desc' => $query->orderByRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) DESC"),
                'progress_asc'  => $query->orderByRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) ASC"),
                'oe_desc'       => $query->orderBy('owner_estimate', 'desc'),
                'oe_asc'        => $query->orderBy('owner_estimate', 'asc'),
                'title_asc'     => $query->orderBy('job_package', 'asc'),
                default         => $query->latest(),
            };
        };

        $activeQuery = (clone $baseQuery)->where(function ($q) {
            $q->where('status', '!=', 'batal')
                ->orWhereNull('status');
        });

        // 1. Belum Mulai
        $notStartedQuery = (clone $activeQuery)->where(function ($q) {
            $q->whereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) <= 0")
                ->orWhereNull('hasil_progres')
                ->orWhere('hasil_progres', '')
                ->orWhere('hasil_progres', '0');
            });
        $applySort($notStartedQuery);
        $notStartedJobs = $notStartedQuery->paginate(10, ['*'], 'page_not_started')->withQueryString();

        // 2. Sedang Berjalan
        $runningQuery = (clone $activeQuery)
            ->whereNotNull('hasil_progres')
            ->where('hasil_progres', '!=', '')
            ->whereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) > 0")
            ->whereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) < 100");
        $applySort($runningQuery);
        $runningJobs = $runningQuery->paginate(10, ['*'], 'page_running')->withQueryString();

        // 3. Selesai
        $doneQuery = (clone $activeQuery)
            ->whereNotNull('hasil_progres')
            ->where('hasil_progres', '!=', '')
            ->whereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) >= 100");
        $applySort($doneQuery);
        $doneJobs = $doneQuery->paginate(10, ['*'], 'page_done')->withQueryString();

        // 4. Dibatalkan
        $cancelledQuery = (clone $baseQuery)->where('status', 'batal');
        $applySort($cancelledQuery);
        $cancelledJobs = $cancelledQuery->paginate(10, ['*'], 'page_cancelled')->withQueryString();

        return view('admin.job_packages.index', compact(
            'notStartedJobs',
            'runningJobs',
            'doneJobs',
            'cancelledJobs',
            'statusFilter',
            'sort',
            'search'
        ));
    }

    public function show(JobPackage $jobPackage)
    {
        $jobPackage->load(['creator', 'pos', 'activityLogs.user', 'suratBakDocs', 'permintaanDaris']);
        return view('admin.job_packages.show', compact('jobPackage'));
    }

    public function create()
    {
        Gate::authorize('create-data');
        return view('admin.job_packages.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create-data');

        // PERBAIKAN: Gunakan fungsi sanitasi angka yang aman terhadap format ribuan (titik/koma)
        if ($request->filled('owner_estimate')) {
            $request->merge([
                'owner_estimate' => $this->sanitizeNumber($request->owner_estimate)
            ]);
        }
        if ($request->filled('final_harga')) {
            $request->merge([
                'final_harga' => $this->sanitizeNumber($request->final_harga)
            ]);
        }
        if ($request->has('pos') && is_array($request->pos)) {
            $sanitizedPos = collect($request->pos)->map(function ($item) {
                if (isset($item['price'])) {
                    $item['price'] = $this->sanitizeNumber($item['price']);
                } elseif (isset($item['harga'])) {
                    $item['price'] = $this->sanitizeNumber($item['harga']);
                }
                return $item;
            })->toArray();

            $request->merge(['pos' => $sanitizedPos]);
        }

        $docRule = fn($field) => $request->hasFile($field) && is_array($request->file($field))
            ? ['nullable', 'array']
            : ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:10240'];

        $validated = $request->validate([
            'job_package'               => ['nullable'],
            'no_service_notifikasi'     => ['nullable', 'string'],
            'no_service_order'          => ['nullable', 'string'],
            'no_po'                     => ['nullable', 'string'],
            
            // PERBAIKAN: Naikkan max value ke 999 Miliar
            'owner_estimate'            => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'final_harga'               => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            
            'hasil_progres'             => ['nullable', 'string'],
            'progress_pekerjaan'        => ['nullable', 'numeric'],
            'rab_lp002'                 => ['nullable', 'numeric'],
            'pbj_lp002'                 => ['nullable', 'numeric'],
            'proses_adm_keuangan'       => ['nullable', 'numeric'],
            'tanggal_mulai_pekerjaan'   => ['nullable', 'date'],
            'tanggal_selesai_pekerjaan' => ['nullable', 'date'],
            'latest_activity'           => ['nullable', 'string'],
            'status'                    => ['nullable', 'string'],
            'items'                     => ['nullable', 'array'],
            'pos'                       => ['nullable', 'array'],
            'po_items'                  => ['nullable', 'array'],
            'surat_bak_docs'            => ['nullable', 'array'],
            'permintaan_dari'           => ['nullable', 'array'],
            'periode'                   => ['nullable', 'string', 'max:255'],

            'doc_rab'                   => $docRule('doc_rab'),
            'doc_rab.*'                 => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:10240'],
            'doc_bak'                   => $docRule('doc_bak'),
            'doc_bak.*'                 => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:10240'],
            'doc_surat_permintaan'      => $docRule('doc_surat_permintaan'),
            'doc_surat_permintaan.*'    => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:10240'],
            'doc_surat_izin_prinsip'    => $docRule('doc_surat_izin_prinsip'),
            'doc_surat_izin_prinsip.*'  => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:10240'],
            'doc_tor'                   => $docRule('doc_tor'),
            'doc_tor.*'                 => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:10240'],
            'doc_bast'                  => $docRule('doc_bast'),
            'doc_bast.*'                => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip,rar', 'max:10240'],
        ]);

        $jobPackage = null;

        DB::transaction(function () use ($request, &$jobPackage, $validated) {
            $validated['created_by'] = auth()->id();

            $poList = $validated['pos'] ?? [];
            $data = array_diff_key($validated, array_flip($this->docFields));
            
            unset($data['pos'], $data['surat_bak_docs'], $data['permintaan_dari']);

            if ($request->has('items')) {
                $data['items'] = array_values($request->input('items', []));
            }

            $suratList = array_values(array_filter(
                $validated['surat_bak_docs'] ?? [],
                fn ($v) => trim((string) $v) !== ''
            ));

            $permintaanList = array_values(array_filter(
                $validated['permintaan_dari'] ?? [],
                fn ($v) => trim((string) $v) !== ''
            ));

            $data['no_surat_bak_doc'] = $suratList[0] ?? null;

            if (!empty($poList) && is_array($poList)) {
                $firstPo = reset($poList);
                $data['no_po'] = $firstPo['no_po'] ?? $data['no_po'] ?? null;

                $totalHarga = array_sum(array_column($poList, 'price'));
                if ($totalHarga > 0) {
                    $data['final_harga'] = $totalHarga;
                }
            }

            $jobPackage = JobPackage::create($data);

            foreach ($suratList as $surat) {
                $jobPackage->suratBakDocs()->create(['no_surat_bak_doc' => $surat]);
            }

            foreach ($permintaanList as $permintaan) {
                $jobPackage->permintaanDaris()->create(['permintaan_dari' => $permintaan]);
            }

            if (!empty($poList) && is_array($poList)) {
                foreach ($poList as $item) {
                    if (!empty($item['no_po'])) {
                        $jobPackage->pos()->create([
                            'no_po'       => $item['no_po'],
                            'description' => $item['description'] ?? $item['nama_item'] ?? null,
                            'price'       => $item['price'] ?? 0,
                        ]);
                    }
                }
            }

            $this->handleFileUploads($request, $jobPackage);
            $this->logActivity($jobPackage, 'created', 'Job Package baru ditambahkan.');
        });

        return redirect()
            ->route('admin.job-packages.show', $jobPackage)
            ->with('success', 'Job Package berhasil ditambahkan.');
    }

    public function edit(JobPackage $jobPackage)
    {
        Gate::authorize('edit-data');

        $jobPackage->load(['pos', 'suratBakDocs', 'permintaanDaris']);
        return view('admin.job_packages.edit', compact('jobPackage'));
    }

    // PERBAIKAN: Gunakan UpdateJobPackageRequest agar validasi & sanitasi terpusat
    public function update(UpdateJobPackageRequest $request, JobPackage $jobPackage)
    {
        Gate::authorize('edit-data');

        // Ambil data yang sudah disanitasi & divalidasi oleh FormRequest
        $validated = $request->validated();

        DB::transaction(function () use ($request, $jobPackage, $validated) {
            $poList = $validated['pos'] ?? $request->input('pos', $request->input('po_items', []));
            $data = array_diff_key($validated, array_flip($this->docFields));
            
            // Pertahankan 'periode' di $data, hanya hapus array relasi
            unset($data['pos'], $data['po_items'], $data['surat_bak_docs'], $data['permintaan_dari']);

            $jobPackageInput = $validated['job_package'] ?? $request->input('job_package');
            if (is_array($jobPackageInput)) {
                $data['job_package'] = implode(', ', array_filter($jobPackageInput));
            } else {
                $data['job_package'] = $jobPackageInput;
            }

            if ($request->has('items')) {
                $data['items'] = array_values($request->input('items', []));
            }

            $suratList = array_values(array_filter(
                (array) ($validated['surat_bak_docs'] ?? $request->input('surat_bak_docs', [])),
                fn ($v) => trim((string) $v) !== ''
            ));

            $permintaanList = array_values(array_filter(
                (array) ($validated['permintaan_dari'] ?? $request->input('permintaan_dari', [])),
                fn ($v) => trim((string) $v) !== ''
            ));

            $data['no_surat_bak_doc'] = $suratList[0] ?? null;

            if (!empty($poList) && is_array($poList)) {
                $firstPo = reset($poList);
                $data['no_po'] = $firstPo['no_po'] ?? $data['no_po'] ?? null;

                $totalHarga = array_sum(array_column($poList, 'price')) ?: array_sum(array_column($poList, 'harga'));
                if ($totalHarga > 0) {
                    $data['final_harga'] = $totalHarga;
                }
            }

            $originalData = $jobPackage->getOriginal();
            $jobPackage->update($data);

            $changeDescription = $this->describeChanges($originalData, $jobPackage->getAttributes());
            $this->logActivity($jobPackage, 'updated', $changeDescription);

            $jobPackage->suratBakDocs()->delete();
            foreach ($suratList as $surat) {
                $jobPackage->suratBakDocs()->create(['no_surat_bak_doc' => $surat]);
            }

            $jobPackage->permintaanDaris()->delete();
            foreach ($permintaanList as $permintaan) {
                $jobPackage->permintaanDaris()->create(['permintaan_dari' => $permintaan]);
            }

            $jobPackage->pos()->delete();
            if (!empty($poList) && is_array($poList)) {
                foreach ($poList as $item) {
                    if (!empty($item['no_po'])) {
                        $jobPackage->pos()->create([
                            'no_po'       => $item['no_po'],
                            'description' => $item['description'] ?? $item['nama_item'] ?? null,
                            'price'       => $item['price'] ?? $item['harga'] ?? 0,
                        ]);
                    }
                }
            }

            $this->handleFileUploads($request, $jobPackage);
        });

        return redirect()
            ->route('admin.job-packages.show', $jobPackage)
            ->with('success', 'Job Package berhasil diperbarui!');
    }

    public function destroy(JobPackage $jobPackage)
    {
        Gate::authorize('delete-data');

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menghapus Job Package secara permanen.');
        }

        if (env('FILESYSTEM_DISK') === 'google') {
            try {
                $drive = new GoogleDriveService();
                $rootFolderId = env('GOOGLE_DRIVE_FOLDER_JPP', env('GOOGLE_DRIVE_FOLDER_ID'));
                $drive->deleteFolderByJobPackage(
                    $jobPackage->google_drive_folder_id,
                    $rootFolderId,
                    $jobPackage->job_package ?? ('Job_' . $jobPackage->id)
                );
            } catch (\Exception $e) {}
        }

        foreach ($this->docFields as $field) {
            if (!empty($jobPackage->$field)) {
                $files = is_array($jobPackage->$field) ? $jobPackage->$field : [$jobPackage->$field];
                foreach ($files as $filePath) {
                    if (!filter_var($filePath, FILTER_VALIDATE_URL)) {
                        $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $filePath), '/');
                        if (Storage::disk('public')->exists($cleanPath)) {
                            Storage::disk('public')->delete($cleanPath);
                        }
                    }
                }
            }
        }

        $jobPackage->delete();

        return redirect()
            ->route('admin.job-packages.index')
            ->with('success', 'Job Package dan berkas terkait berhasil dihapus.');
    }

    public function cancel(JobPackage $jobPackage)
    {
        Gate::authorize('edit-data');

        $jobPackage->update(['status' => 'batal']);
        $this->logActivity($jobPackage, 'cancelled', 'Job Package dibatalkan.');

        return back()->with('success', 'Job Package berhasil dibatalkan.');
    }

    public function reactivate(JobPackage $jobPackage)
    {
        Gate::authorize('edit-data');

        $jobPackage->update(['status' => 'aktif']);
        $this->logActivity($jobPackage, 'reactivated', 'Job Package diaktifkan kembali.');

        return back()->with('success', 'Job Package berhasil diaktifkan kembali.');
    }

    public function deleteDocument(JobPackage $jobPackage, string $field, Request $request)
    {
        Gate::authorize('edit-data');

        if (!in_array($field, $this->docFields) || empty($jobPackage->$field)) {
            return back()->with('error', 'Dokumen tidak ditemukan atau tidak valid.');
        }

        $files = is_array($jobPackage->$field) ? $jobPackage->$field : [$jobPackage->$field];
        
        foreach ($files as $filePathOrUrl) {
            $isUrl = filter_var($filePathOrUrl, FILTER_VALIDATE_URL);

            if ($isUrl) {
                if (env('FILESYSTEM_DISK') === 'google') {
                    try {
                        preg_match('/[-\w]{25,}/', $filePathOrUrl, $matches);
                        $fileId = $matches[0] ?? null;

                        if ($fileId) {
                            $drive = new GoogleDriveService();
                            $drive->deleteFile($fileId);
                        }
                    } catch (\Exception $e) {}
                }
            } else {
                $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $filePathOrUrl), '/');
                if (Storage::disk('public')->exists($cleanPath)) {
                    Storage::disk('public')->delete($cleanPath);
                }
            }
        }

        $jobPackage->update([$field => null]);
        $this->logActivity($jobPackage, 'document_deleted', "Seluruh Dokumen pada “{$field}” dihapus.");

        return back()->with('success', 'Seluruh dokumen berhasil dihapus.');
    }

    public function downloadDocument(Request $request, JobPackage $jobPackage, string $field)
    {
        if (!in_array($field, $this->docFields) || empty($jobPackage->$field)) {
            abort(404, 'Dokumen belum diunggah.');
        }

        $files = is_array($jobPackage->$field) ? $jobPackage->$field : [$jobPackage->$field];
        $index = $request->query('index', 0);
        $targetFile = $files[$index] ?? $files[0] ?? null;

        if (!$targetFile) {
            abort(404, 'File spesifik tidak ditemukan.');
        }

        if (filter_var($targetFile, FILTER_VALIDATE_URL)) {
            return redirect()->away($targetFile);
        }

        $rawPath = $targetFile;
        $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $rawPath), '/');

        if (!Storage::disk('public')->exists($cleanPath)) {
            abort(404, "Berkas fisik tidak ditemukan: storage/app/public/{$cleanPath}");
        }

        $fullPath = Storage::disk('public')->path($cleanPath);
        $fileName = basename($cleanPath);
        $downloadName = preg_replace('/^\d+_/', '', $fileName);

        if ($request->query('mode') === 'view') {
            return response()->file($fullPath, [
                'Content-Disposition' => 'inline; filename="' . $downloadName . '"'
            ]);
        }

        return response()->download($fullPath, $downloadName);
    }

    public function export(JobPackageExportService $exportService)
    {
        $writer = $exportService->generate();
        $filename = 'Laporan_Pekerjaan_JPP_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function handleFileUploads(Request $request, JobPackage $jobPackage): void
    {
        set_time_limit(300);

        $hasFilesToUpload = false;
        foreach ($this->docFields as $field) {
            if ($request->hasFile($field)) {
                $hasFilesToUpload = true;
                break;
            }
        }

        if (!$hasFilesToUpload) {
            return;
        }

        $updates = [];
        $isGoogleDrive = config('filesystems.default') === 'google';
        $drive = null;
        $parentFolderId = config('filesystems.disks.google.folder_jpp');
        $targetFolderId = null;

        if ($isGoogleDrive) {
            $drive = new GoogleDriveService();

            if ($jobPackage->google_drive_folder_id) {
                $targetFolderId = $jobPackage->google_drive_folder_id;
            } else {
                $jobFolderName = $jobPackage->job_package ?? ('Job_' . $jobPackage->id);
                $targetFolderId = $drive->getOrCreateJobFolder($parentFolderId, $jobFolderName);
                $updates['google_drive_folder_id'] = $targetFolderId;
            }
        }

        foreach ($this->docFields as $field) {
            if ($request->hasFile($field)) {
                $files = $request->file($field);

                if (!is_array($files)) {
                    $files = [$files];
                }

                $currentFiles = is_array($jobPackage->$field) ? $jobPackage->$field : [];

                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $realPath = $file->getRealPath() ?: $file->getPathname();

                        if (empty($realPath) || !file_exists($realPath)) {
                            continue;
                        }

                        $originalName = preg_replace('/\s+/', '_', $file->getClientOriginalName());
                        $filename = time() . '_' . uniqid() . '_' . $originalName;

                        if ($isGoogleDrive && $drive) {
                            $uploadedFile = $drive->uploadFile(
                                $filename,
                                $file->getClientMimeType(),
                                $realPath,
                                $file->getSize(),
                                $targetFolderId
                            );

                            if ($uploadedFile && isset($uploadedFile->id)) {
                                $currentFiles[] = $uploadedFile->webViewLink;
                            }
                        } else {
                            $currentFiles[] = $file->storeAs('job_documents', $filename, 'public');
                        }
                    }
                }

                $updates[$field] = $currentFiles;
            }
        }

        if (!empty($updates)) {
            $jobPackage->update($updates);

            $uploadedDocs = array_intersect(array_keys($updates), $this->docFields);
            if (!empty($uploadedDocs)) {
                $this->logActivity(
                    $jobPackage,
                    'document_uploaded',
                    'Dokumen diunggah secara Multiple: ' . implode(', ', $uploadedDocs) . '.'
                );
            }
        }
    }

    private function logActivity(JobPackage $jobPackage, string $action, string $description): void
    {
        ActivityLog::create([
            'job_package_id' => $jobPackage->id,
            'user_id'        => auth()->id(),
            'action'         => $action,
            'description'    => $description,
        ]);
    }

    private function describeChanges(array $original, array $updated): string
    {
        $labels = [
            'progress_pekerjaan'        => 'Fisik Pekerjaan',
            'rab_lp002'                 => 'RAB LP-002',
            'pbj_lp002'                 => 'PB/J LP-002',
            'proses_adm_keuangan'       => 'ADM Keuangan',
            'final_harga'               => 'Final Harga',
            'owner_estimate'            => 'Owner Estimate',
            'tanggal_mulai_pekerjaan'   => 'Tanggal Mulai',
            'tanggal_selesai_pekerjaan' => 'Tanggal Selesai',
            'latest_activity'           => 'Aktivitas Terkini',
        ];

        $numericFields = [
            'final_harga', 'owner_estimate',
            'progress_pekerjaan', 'rab_lp002', 'pbj_lp002', 'proses_adm_keuangan',
        ];

        $changes = [];

        foreach ($labels as $field => $label) {
            $old = $original[$field] ?? null;
            $new = $updated[$field] ?? null;

            if (in_array($field, $numericFields)) {
                $oldNum = (float) ($old ?? 0);
                $newNum = (float) ($new ?? 0);
                $isChanged = abs($oldNum - $newNum) > 0.01;
            } else {
                $isChanged = (string) $old !== (string) $new;
            }

            if ($isChanged) {
                if (in_array($field, ['final_harga', 'owner_estimate'])) {
                    $oldFmt = $old ? 'Rp ' . number_format((float) $old, 0, ',', '.') : '-';
                    $newFmt = $new ? 'Rp ' . number_format((float) $new, 0, ',', '.') : '-';
                    $changes[] = "{$label}: {$oldFmt} → {$newFmt}";
                } elseif (in_array($field, ['progress_pekerjaan', 'rab_lp002', 'pbj_lp002', 'proses_adm_keuangan'])) {
                    $changes[] = "{$label}: " . number_format((float) ($old ?? 0), 0) . "% → " . number_format((float) ($new ?? 0), 0) . "%";
                } else {
                    $changes[] = "{$label} diperbarui";
                }
            }
        }

        return empty($changes)
            ? 'Data diperbarui (tidak ada perubahan nilai signifikan).'
            : implode('; ', $changes);
    }

    private function sanitizeDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = str_replace(',', '.', trim((string) $value));
        $clean = preg_replace('/[^\d.]/', '', $clean);

        return is_numeric($clean) ? (float) $clean : null;
    }

    private function sanitizeNumber(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^\d.,]/', '', (string) $value);

        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            if (strrpos($clean, ',') > strrpos($clean, '.')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        }
        elseif (strpos($clean, '.') !== false) {
            if (substr_count($clean, '.') > 1 || preg_match('/\.\d{3}$/', $clean)) {
                $clean = str_replace('.', '', $clean);
            }
        }
        elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return is_numeric($clean) ? (float) $clean : null;
    }
}