@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-[1280px]">
        {{-- Header Section --}}
        <div class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-[#623ed8]">
                    <span class="h-2 w-2 rounded-full bg-[#623ed8] animate-pulse"></span>
                    Sistem Absensi Digital · Pusat Kendali
                </div>
                <h1 class="school-display text-3xl font-bold tracking-tight text-[#0f1e3d] sm:text-4xl">Dashboard Ringkasan</h1>
                <p class="mt-2 text-sm text-[#68748b]">Selamat datang kembali, <span class="font-semibold text-[#0f1e3d]">{{ $activeUser->name }}</span>. Berikut adalah aktivitas kehadiran hari ini.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="rounded-2xl border border-school-line bg-white px-4 py-2.5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-[#9aa4b5] mb-0.5">Waktu Server</div>
                    <div class="text-sm font-bold text-[#0f1e3d] tabular-nums flex items-center gap-2">
                        <i class="ti ti-clock text-[#623ed8]"></i>
                        {{ $todayLabel }}
                    </div>
                </div>
                <a href="{{ route('monitor') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#0f1e3d] px-6 text-sm font-bold text-white shadow-lg shadow-[#0f1e3d]/20 transition hover:bg-[#1a2d52] active:scale-[0.98]">
                    <i class="ti ti-device-tv text-lg"></i>
                    Buka Layar Monitor
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <section class="grid gap-4 grid-cols-2 md:grid-cols-4 lg:gap-6" aria-labelledby="stats-heading">
            <h2 id="stats-heading" class="sr-only">Statistik Kehadiran</h2>
            @foreach($stats as $stat)
                <div class="group relative overflow-hidden rounded-3xl border border-school-line bg-white p-5 shadow-sm transition-all hover:shadow-md lg:p-6">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-{{ $stat['tone'] === 'success' ? 'green' : ($stat['tone'] === 'warning' ? 'orange' : ($stat['tone'] === 'blue' ? 'blue' : 'purple')) }}-50 opacity-0 transition-opacity group-hover:opacity-100"></div>

                    <div class="relative z-10 flex items-start justify-between">
                        <div class="h-10 w-10 rounded-2xl bg-{{ $stat['tone'] === 'success' ? 'green' : ($stat['tone'] === 'warning' ? 'orange' : ($stat['tone'] === 'blue' ? 'blue' : 'purple')) }}-50 flex items-center justify-center text-{{ $stat['tone'] === 'success' ? 'green' : ($stat['tone'] === 'warning' ? 'orange' : ($stat['tone'] === 'blue' ? 'blue' : 'purple')) }}-600">
                            <i class="ti {{ $stat['icon'] }} text-xl"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#9aa4b5]">{{ $stat['label'] }}</span>
                    </div>

                    <div class="relative z-10 mt-5">
                        <div class="school-display text-3xl font-bold tracking-tighter text-[#0f1e3d] lg:text-4xl">{{ number_format($stat['value']) }}</div>
                        <p class="mt-1 text-xs font-medium text-[#68748b]">{{ $stat['caption'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Main Content Grid --}}
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            {{-- Recent Scans Table --}}
            <div class="lg:col-span-2">
                <div class="rounded-3xl border border-school-line bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-school-line bg-white p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="school-display text-lg font-bold text-[#0f1e3d]">Pemindaian Terkini</h3>
                                <p class="text-xs text-[#8a95a8] mt-1">Aktivitas real-time absensi siswa</p>
                            </div>
                            <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-[#623ed8] hover:underline">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="p-0">
                        <x-dashboard.recent-scans :scans="$recentScans" :school="$school" />
                    </div>
                </div>
            </div>

            {{-- Validation Status & Info --}}
            <div class="space-y-6">
                {{-- Quick Validation Panel --}}
                <div class="rounded-3xl border border-school-line bg-[#0f1e3d] p-6 text-white shadow-xl shadow-[#0f1e3d]/10">
                    <h3 class="school-display text-lg font-bold mb-4">Status Validasi</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-white/10 flex items-center justify-center text-green-400">
                                <i class="ti ti-map-pin-check text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-white/50">Radius Lokasi</div>
                                <div class="text-sm font-semibold">{{ $school->radius_meters }} Meter (Aktif)</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-white/10 flex items-center justify-center text-blue-400">
                                <i class="ti ti-qrcode text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-white/50">QR Dynamic</div>
                                <div class="text-sm font-semibold">Aktif (Auto-Rotate)</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-white/10 flex items-center justify-center text-purple-400">
                                <i class="ti ti-database-share text-lg"></i>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-white/50">Firebase Integration</div>
                                <div class="text-sm font-semibold">{{ config('firebase_integration.enabled') ? 'Terhubung (Async)' : 'Nonaktif' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-white/60">Persentase kehadiran</span>
                            <span class="text-xs font-bold">{{ $summary['percentage'] }}%</span>
                        </div>
                        <div class="h-2 w-full bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-400 to-green-400" style="width: {{ $summary['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- School Info --}}
                <div class="rounded-3xl border border-school-line bg-white p-6 shadow-sm">
                    <h3 class="school-display text-base font-bold text-[#0f1e3d] mb-4">Profil Sekolah</h3>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-school-canvas border border-school-line mb-4">
                        <img src="{{ asset('images/logo-smk.png') }}" class="h-12 w-12 rounded-xl bg-white p-1" alt="Logo">
                        <div>
                            <div class="text-sm font-bold text-[#0f1e3d]">{{ $school->name }}</div>
                            <div class="text-[10px] text-[#8a95a8]">{{ $school->address }}</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.location.edit') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-school-line py-3 text-xs font-bold text-[#0f1e3d] hover:bg-school-canvas transition">
                        <i class="ti ti-settings"></i>
                        Kelola Pengaturan Lokasi
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="mt-12 mb-8 flex flex-col items-center justify-between gap-4 border-t border-school-line pt-8 text-[10px] font-bold uppercase tracking-widest text-[#9aa4b5] sm:flex-row">
            <div class="flex items-center gap-4">
                <span>© 2026 {{ $school->name }}</span>
                <span class="h-1 w-1 rounded-full bg-[#9aa4b5]"></span>
                <span>Absensi Digital V2.0</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                Sistem Stabil
            </div>
        </footer>
    </div>
@endsection
