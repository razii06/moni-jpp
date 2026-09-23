<x-admin-layout title="Dashboard Utama">
    <!-- Style Animasi Pop-Up -->
    <style>
        .animate-pop-in {
            animation: dashboardPopIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes dashboardPopIn {
            0% {
                opacity: 0;
                transform: scale(0.97) translateY(12px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>

    <div class="animate-pop-in">
        <div class="mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Dashboard Monitoring</h2>
            <p class="text-sm text-slate-400 mt-0.5">Ringkasan status proyek dan perkembangan pekerjaan JPP.</p>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div
                class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-[#0f2b5c]"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Job Package</span>
                        <h3 class="text-4xl font-black text-slate-900 mt-2">{{ $stats['total'] }}</h3>
                    </div>
                    <div
                        class="p-3 bg-blue-50 text-[#0f2b5c] rounded-2xl border border-blue-100 group-hover:scale-110 group-hover:bg-[#0f2b5c] group-hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-bold text-amber-500 uppercase tracking-wider">Sedang Berjalan</span>
                        <h3 class="text-4xl font-black text-amber-500 mt-2">{{ $stats['running'] }}</h3>
                    </div>
                    <div
                        class="p-3 bg-amber-50 text-amber-600 rounded-2xl border border-amber-100 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Selesai 100%</span>
                        <h3 class="text-4xl font-black text-emerald-600 mt-2">{{ $stats['completed'] }}</h3>
                    </div>
                    <div
                        class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100 group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Rata-rata Progres</span>
                        <h3 class="text-4xl font-black text-blue-600 mt-2">{{ $stats['avg_progress'] }}<span
                                class="text-lg">%</span></h3>
                    </div>
                    <div
                        class="p-3 bg-blue-50 text-blue-600 rounded-2xl border border-blue-100 group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-1 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-[#0f2b5c] rounded-full"></span>
                    Distribusi Progres
                </h4>
                <canvas id="distributionChart" height="220"></canvas>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                    Owner Estimate vs Final Harga
                </h4>
                <canvas id="financialChart" height="220"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-6">
            <h4 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                Tren Job Package Baru (8 Minggu Terakhir)
            </h4>
            <canvas id="trendChart" height="80"></canvas>
        </div>

        <!-- Tabel Job Package Terbaru -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 flex items-center justify-between border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-[#0f2b5c] rounded-full"></span>
                    5 Job Package Terbaru
                </h4>
                <a href="{{ route('admin.job-packages.index') }}"
                    class="text-xs font-bold text-[#0f2b5c] hover:text-amber-600 transition-colors flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#0f2b5c] text-white text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Job Package</th>
                            <th class="py-3.5 px-4">No. SO</th>
                            <th class="py-3.5 px-4">Progres</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse ($latestJobs as $job)
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-4 px-6 font-bold text-[#0f2b5c]">{{ $job->job_package }}</td>
                            <td class="py-4 px-4 font-mono font-semibold text-slate-600">
                                <span
                                    class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200/60">{{ $job->no_service_order }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                $progressColor = match(true) {
                                $job->hasil_progres >= 90 => 'bg-emerald-500',
                                $job->hasil_progres >= 50 => 'bg-blue-500',
                                $job->hasil_progres > 0 => 'bg-amber-500',
                                default => 'bg-red-500',
                                };
                                @endphp
                                <div class="flex items-center gap-2.5 w-32">
                                    <div
                                        class="w-full bg-slate-100 h-2 rounded-full overflow-hidden p-0.5 border border-slate-200/50">
                                        <div class="{{ $progressColor }} h-full rounded-full transition-all duration-500"
                                            style="width: {{ $job->hasil_progres }}%;"></div>
                                    </div>
                                    <span
                                        class="text-xs font-extrabold text-slate-700 min-w-[32px] text-right">{{ $job->hasil_progres }}%</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('admin.job-packages.show', $job) }}"
                                    class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 text-xs">
                                <div
                                    class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                Belum ada data pekerjaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
    <script>
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

    // Distribusi Progres (Doughnut Chart)
    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Selesai (≥90%)', 'Baik (50-89%)', 'Perlu Perhatian (1-49%)', 'Belum Mulai (0%)'],
            datasets: [{
                data: [
                    {{ $distribution['selesai'] ?? 0 }},
                    {{ $distribution['baik'] ?? 0 }},
                    {{ $distribution['perlu_perhatian'] ?? 0 }},
                    {{ $distribution['belum_mulai'] ?? 0 }}
                ],
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        padding: 14,
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });

    // Owner Estimate vs Final Harga (Bar Chart)
    new Chart(document.getElementById('financialChart'), {
        type: 'bar',
        data: {
            labels: ['Owner Estimate (OE)', 'Final Harga'],
            datasets: [{
                label: 'Total (Rp)',
                data: [
                    {{ $financial['total_oe'] ?? 0 }},
                    {{ $financial['total_final'] ?? 0 }}
                ],
                backgroundColor: ['#0f2b5c', '#f59e0b'],
                borderRadius: 10,
                barThickness: 42,
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' Jt';
                        },
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });

    // Tren Mingguan (Line Chart)
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($weeklyLabels ?? []) !!},
            datasets: [{
                label: 'Job Package Baru',
                data: {!! json_encode($weeklyData ?? []) !!},
                borderColor: '#0f2b5c',
                backgroundColor: 'rgba(15, 43, 92, 0.08)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
    </script>
    @endpush
</x-admin-layout>