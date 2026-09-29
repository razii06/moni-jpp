<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Dokumentasi;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DokumentasiController extends Controller
{
    public function index()
    {
        $dokumentasi = Dokumentasi::latest()->paginate(10);
        return view('admin.dokumentasi.index', compact('dokumentasi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', Rule::in(['Foto Kegiatan', 'Dokumen PDF', 'Tautan Video'])],
            'visibilitas' => ['required', Rule::in(['publik', 'privat'])],
            'file_upload' => [
                Rule::requiredIf(fn () => $request->input('kategori') !== 'Tautan Video'),
                'nullable',
                'file',
                'max:10240',
                'mimetypes:image/jpeg,image/png,image/webp,application/pdf',
            ],
            'tautan_video' => [
                Rule::requiredIf(fn () => $request->input('kategori') === 'Tautan Video'),
                'nullable',
                'url',
                'max:2048',
            ],
        ]);

        $data = [
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'visibilitas' => $validated['visibilitas'],
        ];

        if ($validated['kategori'] === 'Tautan Video') {
            $data['file_path'] = $validated['tautan_video'];
            $data['ukuran_file'] = 'Tautan';
        } else {
            if ($request->hasFile('file_upload') && $request->file('file_upload')->isValid()) {
                $file = $request->file('file_upload');
                
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;
                $realPath = $file->getRealPath() ?: $file->getPathname();

                if (config('filesystems.default') === 'google') {
                    $drive = new GoogleDriveService();
                    $folderBerandaId = config('filesystems.disks.google.folder_beranda');
                    
                    $uploadedFile = $drive->uploadFile(
                        $filename,
                        $file->getClientMimeType(),
                        $realPath,
                        $file->getSize(),
                        $folderBerandaId
                    );

                    $fileUrl = $uploadedFile->webViewLink ?? null;
                } else {
                    $path = $file->storeAs('dokumentasi', $filename, 'public');
                    $fileUrl = asset('storage/' . $path);
                }

                $data['file_path'] = $fileUrl ?: $filename; 
                $data['ukuran_file'] = $this->formatFileSize($file->getSize());
            } else {
                return back()->withErrors(['file_upload' => 'File tidak ditemukan atau tidak valid.'])->withInput();
            }
        }

        Dokumentasi::create($data);

        return back()->with('success', 'Dokumentasi berhasil diunggah.');
    }

    public function destroy(Dokumentasi $dokumentasi)
    {
        // Memproteksi aksi hapus di tingkat backend server
        Gate::authorize('delete-data');

        if ($dokumentasi->kategori !== 'Tautan Video' && !empty($dokumentasi->file_path)) {
            $path = $dokumentasi->file_path;

            // Jika URL file mengarah ke Google Drive
            if (Str::contains($path, ['drive.google.com', 'googleusercontent.com'])) {
                // Ekstrak File ID dari URL Google Drive menggunakan Regex
                preg_match('/[-\w]{25,}/', $path, $matches);
                $fileId = $matches[0] ?? null;

                if ($fileId) {
                    $drive = new GoogleDriveService();
                    $drive->deleteFile($fileId); // Hapus permanen dari Drive
                }
            } else {
                // Hapus dari disk lokal (public) jika file disimpan di server lokal
                $filename = basename(parse_url($path, PHP_URL_PATH));
                if (Storage::disk('public')->exists('dokumentasi/' . $filename)) {
                    Storage::disk('public')->delete('dokumentasi/' . $filename);
                }
            }
        }

        $dokumentasi->delete();

        return back()->with('success', 'Dokumentasi dan file berhasil dihapus.');
    }

    private function formatFileSize(?int $bytes): string
    {
        if (!$bytes) {
            return '0 KB';
        }

        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 2) . ' MB'
            : number_format($bytes / 1024, 2) . ' KB';
    }
}