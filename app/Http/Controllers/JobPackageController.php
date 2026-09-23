<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPackageRequest;
use App\Http\Requests\UpdateJobPackageRequest;
use App\Models\JobPackage;
use App\Models\ActivityLog;
use App\Services\GoogleDriveService;
use App\Services\JobPackageExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobPackageController extends Controller
{
    private array $docFields = [
        'doc_rab', 'doc_bak', 'doc_surat_permintaan',
        'doc_surat_izin_prinsip', 'doc_tor', 'doc_bast',
    ];

    public function index(Request $request)
    {
        $baseQuery = JobPackage::with(['creator', 'pos', 'suratBakDocs', 'permintaanDaris']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('job_package', 'like', "%{$search}%")
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

        $sort = $request->get('sort', 'latest');
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

        // 1. Query Job Package Sedang Berjalan (Progres > 0% dan < 100%, Status bukan 'batal')
        $runningQuery = (clone $baseQuery)
            ->where('status', '!=', 'batal')
            ->whereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) > 0")
            ->whereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) < 100");
        $applySort($runningQuery);
        $runningJobs = $runningQuery->paginate(5, ['*'], 'page_running')->withQueryString();

        // 2. Query Job Package Selesai 100% (Progres >= 100%, Status bukan 'batal')
        $doneQuery = (clone $baseQuery)
            ->where('status', '!=', 'batal')
            ->whereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) >= 100");
        $applySort($doneQuery);
        $doneJobs = $doneQuery->paginate(5, ['*'], 'page_done')->withQueryString();

        // 3. Query Job Package Tidak Dilanjutkan (Status 'batal' atau Progres <= 0)
        $cancelledQuery = (clone $baseQuery)
            ->where(function ($q) {
                $q->where('status', 'batal')
                  ->orWhereRaw("CAST(REPLACE(NULLIF(hasil_progres, ''), ',', '.') AS DECIMAL(10,2)) <= 0")
                  ->orWhereNull('hasil_progres');
            });
        $applySort($cancelledQuery);
        $cancelledJobs = $cancelledQuery->paginate(5, ['*'], 'page_cancelled')->withQueryString();

        return view('admin.job_packages.index', compact('runningJobs', 'doneJobs', 'cancelledJobs'));
    }

    public function show(JobPackage $jobPackage)
    {
        $jobPackage->load(['creator', 'pos', 'activityLogs.user', 'suratBakDocs', 'permintaanDaris']);
        return view('admin.job_packages.show', compact('jobPackage'));
    }

    public function create()
    {
        return view('admin.job_packages.create');
    }

    public function store(StoreJobPackageRequest $request)
    {
        DB::transaction(function () use ($request, &$jobPackage) {
            $validated = $request->validated();
            $validated['created_by'] = auth()->id();

            $poList = $request->input('pos', $request->input('po_items', []));

            $data = array_diff_key($validated, array_flip($this->docFields));
            unset($data['pos'], $data['po_items'], $data['surat_bak_docs'], $data['permintaan_dari']);

            $suratList = array_values(array_filter(
                $request->input('surat_bak_docs', []),
                fn ($v) => trim((string) $v) !== ''
            ));

            $permintaanList = array_values(array_filter(
                $request->input('permintaan_dari', []),
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
                            'price'       => $item['price'] ?? $item['harga'] ?? 0,
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
        $jobPackage->load(['pos', 'suratBakDocs', 'permintaanDaris']);
        return view('admin.job_packages.edit', compact('jobPackage'));
    }

    public function update(UpdateJobPackageRequest $request, JobPackage $jobPackage)
    {
        DB::transaction(function () use ($request, $jobPackage) {
            $validated = $request->validated();

            $poList = $request->input('pos', $request->input('po_items', []));

            $data = array_diff_key($validated, array_flip($this->docFields));
            unset($data['pos'], $data['po_items'], $data['surat_bak_docs'], $data['permintaan_dari']);

            $suratList = array_values(array_filter(
                $request->input('surat_bak_docs', []),
                fn ($v) => trim((string) $v) !== ''
            ));

            $permintaanList = array_values(array_filter(
                $request->input('permintaan_dari', []),
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
            ->with('success', 'Job Package berhasil diperbarui.');
    }

    public function destroy(JobPackage $jobPackage)
    {
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
            if ($jobPackage->$field && !filter_var($jobPackage->$field, FILTER_VALIDATE_URL)) {
                $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $jobPackage->$field), '/');
                if (Storage::disk('public')->exists($cleanPath)) {
                    Storage::disk('public')->delete($cleanPath);
                }
            }
        }

        $jobPackage->delete();

        return redirect()
            ->route('admin.job-packages.index')
            ->with('success', 'Job Package dan folder Google Drive terkait berhasil dihapus.');
    }

    public function cancel(JobPackage $jobPackage)
    {
        $jobPackage->update(['status' => 'batal']);

        $this->logActivity($jobPackage, 'cancelled', 'Job Package dibatalkan.');

        return back()->with('success', 'Job Package berhasil dibatalkan. Data tetap tersimpan dan bisa diaktifkan kembali kapan saja.');
    }

    public function reactivate(JobPackage $jobPackage)
    {
        $jobPackage->update(['status' => 'aktif']);

        $this->logActivity($jobPackage, 'reactivated', 'Job Package diaktifkan kembali.');

        return back()->with('success', 'Job Package berhasil diaktifkan kembali.');
    }

    public function deleteDocument(JobPackage $jobPackage, string $field)
    {
        if (!in_array($field, $this->docFields) || empty($jobPackage->$field)) {
            return back()->with('error', 'Dokumen tidak ditemukan atau tidak valid.');
        }

        $filePathOrUrl = $jobPackage->$field;
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

        $jobPackage->update([$field => null]);

        $this->logActivity($jobPackage, 'document_deleted', "Dokumen \"{$field}\" dihapus.");

        return back()->with('success', 'Dokumen berhasil dihapus.');
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
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
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
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $file = $request->file($field);
                $realPath = $file->getRealPath() ?: $file->getPathname();

                if (empty($realPath) || !file_exists($realPath)) {
                    continue;
                }

                if ($jobPackage->$field && !filter_var($jobPackage->$field, FILTER_VALIDATE_URL)) {
                    $cleanOldPath = ltrim(str_replace(['public/', 'storage/'], '', $jobPackage->$field), '/');
                    if (Storage::disk('public')->exists($cleanOldPath)) {
                        Storage::disk('public')->delete($cleanOldPath);
                    }
                }

                $originalName = preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $filename = time() . '_' . $originalName;

                if ($isGoogleDrive && $drive) {
                    $uploadedFile = $drive->uploadFile(
                        $filename,
                        $file->getClientMimeType(),
                        $realPath,
                        $file->getSize(),
                        $targetFolderId
                    );

                    if ($uploadedFile && isset($uploadedFile->id)) {
                        $updates[$field] = $uploadedFile->webViewLink;
                    }
                } else {
                    $updates[$field] = $file->storeAs('job_documents', $filename, 'public');
                }
            }
        }

        if (!empty($updates)) {
            $jobPackage->update($updates);

            $uploadedDocs = array_intersect(array_keys($updates), $this->docFields);
            if (!empty($uploadedDocs)) {
                $this->logActivity(
                    $jobPackage,
                    'document_uploaded',
                    'Dokumen diunggah: ' . implode(', ', $uploadedDocs) . '.'
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

    public function downloadDocument(Request $request, JobPackage $jobPackage, string $field)
    {
        if (!in_array($field, $this->docFields) || empty($jobPackage->$field)) {
            abort(404, 'Dokumen belum diunggah.');
        }

        if (filter_var($jobPackage->$field, FILTER_VALIDATE_URL)) {
            return redirect()->away($jobPackage->$field);
        }

        $rawPath = $jobPackage->$field;
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
}