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
            // Query dasar untuk paket yang tidak dibatalkan (Paket Aktif)
            $activeQuery = JobPackage::where(function ($q) {
                $q->where('status', '!=', 'batal')
                ->orWhereNull('status');
            });

            // 1. Integrasi statistik utama (TOTAL PAKET hanya menghitung pekerjaan aktif)
            $stats = [
                'total'       => (clone $activeQuery)->count(), // Hanya paket aktif (Batal tidak dihitung)
                'not_started' => (clone $activeQuery)->where(function ($q) {
                                    $q->where('hasil_progres', '<=', 0)
                                    ->orWhereNull('hasil_progres');
                                })->count(),
                'running'     => (clone $activeQuery)->where('hasil_progres', '>', 0)
                                                    ->where('hasil_progres', '<', 100)->count(),
                'completed'   => (clone $activeQuery)->where('hasil_progres', '>=', 100)->count(),
                'cancelled'   => JobPackage::where('status', 'batal')->count(),
            ];

            $latestJobs = JobPackage::with(['suratBakDocs', 'pos', 'permintaanDaris'])
                ->latest()
                ->paginate(5);

            // Distribusi progres untuk doughnut chart (hanya proyek aktif)
            $distribution = [
                'selesai'         => (clone $activeQuery)->where('hasil_progres', '>=', 90)->count(),
                'baik'            => (clone $activeQuery)->whereBetween('hasil_progres', [50, 89.99])->count(),
                'perlu_perhatian' => (clone $activeQuery)->whereBetween('hasil_progres', [0.01, 49.99])->count(),
                'belum_mulai'     => (clone $activeQuery)->where(function ($q) {
                                        $q->where('hasil_progres', '<=', 0)
                                        ->orWhereNull('hasil_progres');
                                    })->count(),
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

            // --- BAGIAN TREN BEBAN KERJA PROYEK AKTIF (OPSI 1) ---
            try {
                $parsedStart = Carbon::parse($request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d')));
            } catch (\Throwable) {
                $parsedStart = Carbon::now()->startOfYear();
            }

            try {
                $parsedEnd = Carbon::parse($request->input('end_date', Carbon::now()->endOfYear()->format('Y-m-d')));
            } catch (\Throwable) {
                $parsedEnd = Carbon::now()->endOfYear();
            }

            if ($parsedStart->gt($parsedEnd)) {
                [$parsedStart, $parsedEnd] = [$parsedEnd, $parsedStart];
            }

            $startDate = $parsedStart->format('Y-m-d');
            $endDate   = $parsedEnd->format('Y-m-d');
            $groupBy   = in_array($request->input('group_by'), ['daily', 'weekly', 'monthly']) ? $request->input('group_by') : 'monthly';

            // Ambil seluruh paket pekerjaan aktif
            $allJobs = JobPackage::where('status', '!=', 'batal')->get();

            $labels = [];
            $dataInProgress = [];
            $dataCompleted = [];
            $dataTotal = [];

            if ($groupBy === 'daily') {
                $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));
                foreach ($period as $date) {
                    $labels[] = $date->translatedFormat('d M Y');

                    $inProgressCount = 0;
                    $completedCount  = 0;

                    foreach ($allJobs as $job) {
                        $start = $job->tanggal_mulai_pekerjaan ? Carbon::parse($job->tanggal_mulai_pekerjaan)->startOfDay() : Carbon::parse($job->created_at)->startOfDay();
                        $end   = $job->tanggal_selesai_pekerjaan ? Carbon::parse($job->tanggal_selesai_pekerjaan)->endOfDay() : (clone $start)->endOfDay();

                        if ($start->lte($date->endOfDay()) && $end->gte($date->startOfDay())) {
                            if ((float)$job->hasil_progres >= 100) {
                                $completedCount++;
                            } else {
                                $inProgressCount++;
                            }
                        }
                    }

                    $dataInProgress[] = $inProgressCount;
                    $dataCompleted[]  = $completedCount;
                    $dataTotal[]      = $inProgressCount + $completedCount;
                }
            } elseif ($groupBy === 'weekly') {
                $period = Carbon::parse($startDate)->startOfWeek()->weeksUntil(Carbon::parse($endDate)->endOfWeek());
                foreach ($period as $date) {
                    $startOfWeek = (clone $date)->startOfWeek();
                    $endOfWeek   = (clone $date)->endOfWeek();
                    $labels[]    = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');

                    $inProgressCount = 0;
                    $completedCount  = 0;

                    foreach ($allJobs as $job) {
                        $start = $job->tanggal_mulai_pekerjaan ? Carbon::parse($job->tanggal_mulai_pekerjaan)->startOfDay() : Carbon::parse($job->created_at)->startOfDay();
                        $end   = $job->tanggal_selesai_pekerjaan ? Carbon::parse($job->tanggal_selesai_pekerjaan)->endOfDay() : (clone $start)->endOfDay();

                        if ($start->lte($endOfWeek) && $end->gte($startOfWeek)) {
                            if ((float)$job->hasil_progres >= 100) {
                                $completedCount++;
                            } else {
                                $inProgressCount++;
                            }
                        }
                    }

                    $dataInProgress[] = $inProgressCount;
                    $dataCompleted[]  = $completedCount;
                    $dataTotal[]      = $inProgressCount + $completedCount;
                }
            } else { // monthly (default)
                $period = Carbon::parse($startDate)->startOfMonth()->monthsUntil(Carbon::parse($endDate)->endOfMonth());
                foreach ($period as $date) {
                    $startOfMonth = (clone $date)->startOfMonth();
                    $endOfMonth   = (clone $date)->endOfMonth();
                    $labels[]     = $date->translatedFormat('M Y');

                    $inProgressCount = 0;
                    $completedCount  = 0;

                    foreach ($allJobs as $job) {
                        $start = $job->tanggal_mulai_pekerjaan ? Carbon::parse($job->tanggal_mulai_pekerjaan)->startOfDay() : Carbon::parse($job->created_at)->startOfDay();
                        $end   = $job->tanggal_selesai_pekerjaan ? Carbon::parse($job->tanggal_selesai_pekerjaan)->endOfDay() : (clone $start)->endOfDay();

                        if ($start->lte($endOfMonth) && $end->gte($startOfMonth)) {
                            if ((float)$job->hasil_progres >= 100) {
                                $completedCount++;
                            } else {
                                $inProgressCount++;
                            }
                        }
                    }

                    $dataInProgress[] = $inProgressCount;
                    $dataCompleted[]  = $completedCount;
                    $dataTotal[]      = $inProgressCount + $completedCount;
                }
            }

            $dataValues = $dataTotal; // Kompatibilitas mundur

            return view('admin.dashboard', compact(
                'stats', 'latestJobs', 'distribution', 'targetStats', 'startDate', 'endDate', 'labels', 'dataInProgress', 'dataCompleted', 'dataTotal', 'dataValues'
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

            // Base query untuk statistik publicHome agar rapi (hanya paket aktif)
            $basePublicStats = JobPackage::where(function ($q) {
                $q->where('status', '!=', 'batal')
                ->orWhereNull('status');
            });

            // 3. Ringkasan Statistik Public
            $stats = [
                'total'       => (clone $basePublicStats)->count(),
                'not_started' => (clone $basePublicStats)->where(function ($q) {
                                    $q->where('hasil_progres', '<=', 0)
                                    ->orWhereNull('hasil_progres');
                                })->count(),
                'running'     => (clone $basePublicStats)->where('hasil_progres', '>', 0)
                                                        ->where('hasil_progres', '<', 100)->count(),
                'completed'   => (clone $basePublicStats)->where('hasil_progres', '>=', 100)->count(),
            ];

            return view(view()->exists('public.home') ? 'public.home' : 'home', compact('jobPackages', 'stats'));
        }
    }