<?php

namespace App\Http\Controllers;

use App\Models\JobPackage;
use App\Models\Dokumentasi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show(JobPackage $jobPackage)
    {
        // Memastikan detail pekerjaan tidak bisa diakses langsung via URL jika bukan public
        abort_if($jobPackage->status !== 'aktif' || $jobPackage->visibility !== 'public', 404);

        $jobPackage->load(['pos', 'suratBakDocs', 'permintaanDaris']);

        return view('public.job-package-show', compact('jobPackage'));
    }

    public function index(Request $request)
    {
        // 1. Filter statistik hanya untuk data aktif & public
        $stats = [
            'total'     => JobPackage::where('status', 'aktif')->where('visibility', 'public')->count(),
            'running'   => JobPackage::where('status', 'aktif')->where('visibility', 'public')->where('hasil_progres', '<', 100)->count(),
            'completed' => JobPackage::where('status', 'aktif')->where('visibility', 'public')->where('hasil_progres', '>=', 100)->count(),
        ];

        // 2. Query utama daftar pekerjaan dengan filter public
        $query = JobPackage::with(['pos', 'suratBakDocs', 'permintaanDaris'])
            ->where('status', 'aktif')
            ->where('visibility', 'public');

        // 3. Filter pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('job_package', 'like', "%{$search}%")
                    ->orWhere('no_service_order', 'like', "%{$search}%")
                    ->orWhere('no_po', 'like', "%{$search}%")
                    ->orWhere('no_surat_bak_doc', 'like', "%{$search}%")
                    ->orWhereHas('pos', function ($poQuery) use ($search) {
                        $poQuery->where('no_po', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('permintaanDaris', function ($permintaanQuery) use ($search) {
                        $permintaanQuery->where('permintaan_dari', 'like', "%{$search}%");
                    });
            });
        }

        // 4. Pengurutan data
        match ($request->input('sort')) {
            'oldest'        => $query->oldest(),
            'progress_asc'  => $query->orderBy('hasil_progres', 'asc'),
            'progress_desc' => $query->orderBy('hasil_progres', 'desc'),
            'oe_desc'       => $query->orderBy('owner_estimate', 'desc'),
            'oe_asc'        => $query->orderBy('owner_estimate', 'asc'),
            'title_asc'     => $query->orderBy('job_package', 'asc'),
            default         => $query->latest(),
        };

        $jobPackages = $query->paginate(5)->withQueryString();

        // 5. Galeri dokumentasi
        $galeri = Dokumentasi::where('visibilitas', 'publik')
            ->latest()
            ->get();

        return view('public.home', compact('stats', 'jobPackages', 'galeri'));
    }
}