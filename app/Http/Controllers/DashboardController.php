<?php

namespace App\Http\Controllers;

use App\Models\JobPackage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar untuk paket yang tidak dibatalkan
        $activeQuery = JobPackage::where(function ($q) {
            $q->where('status', '!=', 'batal')
                ->orWhereNull('status');
        });

        $stats = [
            'total'       => JobPackage::count(),
            'running'     => (clone $activeQuery)->where('hasil_progres', '>', 0)->where('hasil_progres', '<', 100)->count(),
            'completed'   => (clone $activeQuery)->where('hasil_progres', '>=', 100)->count(),
            'not_started' => (clone $activeQuery)->where('hasil_progres', 0)->count(),
            'cancelled'   => JobPackage::where('status', 'batal')->count(),
        ];

        $latestJobs = JobPackage::with(['suratBakDocs', 'pos', 'permintaanDaris'])
            ->latest()
            ->paginate(5);

        // Distribusi progres untuk donut chart (hanya proyek aktif)
        $distribution = [
            'selesai'         => (clone $activeQuery)->where('hasil_progres', '>=', 90)->count(),
            'baik'            => (clone $activeQuery)->whereBetween('hasil_progres', [50, 89.99])->count(),
            'perlu_perhatian' => (clone $activeQuery)->whereBetween('hasil_progres', [0.01, 49.99])->count(),
            'belum_mulai'     => (clone $activeQuery)->where('hasil_progres', 0)->count(),
        ];

        // Indikator Kontrol Target & Risiko Delay (hanya proyek aktif)
        $targetStats = [
            'terlambat' => (clone $activeQuery)
                ->where('hasil_progres', '<', 100)
                ->whereNotNull('tanggal_selesai_pekerjaan')
                ->where('tanggal_selesai_pekerjaan', '<', Carbon::today())
                ->count(),
            'selesai_bulan_ini' => (clone $activeQuery)
                ->whereMonth('tanggal_selesai_pekerjaan', Carbon::now()->month)
                ->whereYear('tanggal_selesai_pekerjaan', Carbon::now()->year)
                ->count(),
        ];

        // --- BAGIAN TREN FILTER DINAMIS ---
        $startDate = $request->input('start_date', Carbon::now()->subWeeks(8)->startOfWeek()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));
        $groupBy   = $request->input('group_by', 'weekly');

        $trendQuery = JobPackage::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);

        $labels = [];
        $dataValues = [];

        if ($groupBy === 'daily') {
            $rawDatas = (clone $trendQuery)->select(
                DB::raw("DATE(created_at) as date_key"),
                DB::raw("COUNT(*) as total")
            )->groupBy('date_key')->pluck('total', 'date_key');

            $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $labels[] = $date->translatedFormat('d M Y');
                $dataValues[] = $rawDatas[$key] ?? 0;
            }
        } elseif ($groupBy === 'monthly') {
            $rawDatas = (clone $trendQuery)->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"),
                DB::raw("COUNT(*) as total")
            )->groupBy('month_key')->pluck('total', 'month_key');

            $period = Carbon::parse($startDate)->startOfMonth()->monthsUntil(Carbon::parse($endDate)->endOfMonth());
            foreach ($period as $date) {
                $key = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');
                $dataValues[] = $rawDatas[$key] ?? 0;
            }
        } else { // weekly (default)
            $rawDatas = (clone $trendQuery)->get()->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->startOfWeek()->format('Y-m-d');
            });

            $period = Carbon::parse($startDate)->startOfWeek()->weeksUntil(Carbon::parse($endDate)->endOfWeek());
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $endOfWeek = (clone $date)->endOfWeek();
                $labels[] = $date->format('d M') . ' - ' . $endOfWeek->format('d M');
                $dataValues[] = isset($rawDatas[$key]) ? $rawDatas[$key]->count() : 0;
            }
        }

        return view('admin.dashboard', compact(
            'stats', 'latestJobs', 'distribution', 'targetStats', 'startDate', 'endDate', 'labels', 'dataValues'
        ));
    }

    public function publicHome(Request $request)
    {
        $query = JobPackage::query();

        // 1. Fitur Pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('job_package', 'like', "%{$search}%")
                    ->orWhere('no_surat_bak_doc', 'like', "%{$search}%")
                    ->orWhere('no_service_order', 'like', "%{$search}%")
                    ->orWhere('no_po', 'like', "%{$search}%");
                });
            }

        // 2. Fitur Pengurutan (Sorting)
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'oldest'        => $query->oldest(),
            'progress_desc' => $query->orderBy('hasil_progres', 'desc'),
            'progress_asc'  => $query->orderBy('hasil_progres', 'asc'),
            'oe_desc'       => $query->orderBy('owner_estimate', 'desc'),
            'oe_asc'        => $query->orderBy('owner_estimate', 'asc'),
            'title_asc'     => $query->orderBy('job_package', 'asc'),
            default         => $query->latest(),
        };

        $jobPackages = $query->get();

        // 3. Ringkasan Statistik
        $stats = [
            'total'     => JobPackage::count(),
            'running'   => JobPackage::where('status', '!=', 'batal')->where('hasil_progres', '>', 0)->where('hasil_progres', '<', 100)->count(),
            'completed' => JobPackage::where('status', '!=', 'batal')->where('hasil_progres', '>=', 100)->count(),
        ];

        return view(view()->exists('public.home') ? 'public.home' : 'home', compact('jobPackages', 'stats'));
    }
}