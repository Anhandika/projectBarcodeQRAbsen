@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5]/20 p-2 sm:p-6 text-white" x-data="attendanceDashboard()">

    {{-- Decorative Background Glows --}}
    <div class="absolute top-20 left-10 h-72 w-72 rounded-full bg-[#623ed8]/20 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-20 right-10 h-96 w-96 rounded-full bg-[#ffd500]/10 blur-[130px] pointer-events-none"></div>

    <div class="mx-auto max-w-5xl space-y-6 pb-24 relative z-10">

        {{-- Professional Glassmorphic Header --}}
        <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-6 shadow-[inset_0_1px_1px_rgba(255,255,255,0.2),0_15px_35px_rgba(0,0,0,0.3)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#ffd500]">SMK BINA UTAMA KENDAL</p>
                <h1 class="text-2xl sm:text-3xl font-black font-display tracking-tight text-white mt-1">{{ auth()->user()->name }}</h1>
                <p class="text-xs text-white/70 mt-1 uppercase tracking-wider font-semibold bg-white/10 px-2.5 py-1 rounded-xl inline-block border border-white/10">
                    {{ auth()->user()->role?->value ?? 'User' }} · {{ auth()->user()->identifier ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest">Waktu Server</p>
                    <p class="text-sm font-mono font-bold text-[#ffd500]" id="liveClock">{{ now(config('attendance.timezone'))->format('H:i:s') }}</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] p-0.5 shadow-xl">
                    <div class="h-full w-full rounded-2xl bg-[#0f1e3d] flex items-center justify-center font-display font-black text-xl text-white border border-white/20">
                        {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Notification --}}
        @if(session('ok'))
        <div class="animate-slideIn rounded-2xl bg-green-500/20 backdrop-blur-md border border-green-500/30 px-5 py-4 text-sm text-green-200 flex items-center gap-3 shadow-lg">
            <i class="ti ti-circle-check text-xl text-green-400"></i>
            <span class="flex-1 font-medium">{{ session('ok') }}</span>
            <button @click="$el.parentElement.remove()" class="text-green-300 hover:text-white transition">
                <i class="ti ti-x"></i>
            </button>
        </div>
        @endif

        {{-- Responsive Layout Grid: Main Actions & Analysis --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Column 1 & 2: Attendance Information and Retrieval --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 3D Floating Main Status Card --}}
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#2c68f5] to-[#623ed8] rounded-3xl blur-xl opacity-30 group-hover:opacity-40 transition duration-500"></div>
                    <div class="relative overflow-hidden rounded-3xl bg-white/10 backdrop-blur-2xl border border-white/20 p-6 shadow-2xl">
                        <div class="absolute top-0 right-0 p-8 text-white/5 text-9xl pointer-events-none font-black font-display select-none">
                            BU
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-[#ffd500] border border-white/10">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                    Ambil Data Absen Hari Ini
                                </span>
                                <h2 class="mt-3 text-3xl font-black font-display tracking-tight text-white">
                                    @switch($stats['today']['status'])
                                        @case('success') Hadir @break
                                        @case('late') Terlambat @break
                                        @case('outside_area') Di Luar Area @break
                                        @case('expired') Kadaluarsa @break
                                        @case('duplicate') Duplikat @break
                                        @default Belum Absen
                                    @endswitch
                                </h2>
                                <p class="text-xs text-white/60 mt-1 font-medium">
                                    {{ now(config('attendance.timezone'))->locale('id')->translatedFormat('l, d F Y') }}
                                </p>
                            </div>

                            @php
                                $statusConfig = [
                                    'success' => ['icon' => 'ti-circle-check', 'bg' => 'bg-green-500/20 border-green-500/30 text-green-400', 'glow' => 'shadow-green-500/20'],
                                    'late' => ['icon' => 'ti-clock-exclamation', 'bg' => 'bg-yellow-500/20 border-yellow-500/30 text-yellow-400', 'glow' => 'shadow-yellow-500/20'],
                                    'outside_area' => ['icon' => 'ti-map-pin-off', 'bg' => 'bg-red-500/20 border-red-500/30 text-red-400', 'glow' => 'shadow-red-500/20'],
                                    'belum_absen' => ['icon' => 'ti-help', 'bg' => 'bg-white/10 border-white/10 text-white/60', 'glow' => 'shadow-white/5'],
                                ];
                                $config = $statusConfig[$stats['today']['status']] ?? $statusConfig['belum_absen'];
                            @endphp

                            <div class="h-16 w-16 rounded-2xl {{ $config['bg'] }} border flex items-center justify-center text-3xl shadow-2xl {{ $config['glow'] }} self-start sm:self-center">
                                <i class="ti {{ $config['icon'] }}"></i>
                            </div>
                        </div>

                        {{-- Metadata Grid --}}
                        <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-white/10">
                            <div class="bg-black/10 rounded-2xl p-4 border border-white/5">
                                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Jam Pemindaian</p>
                                <p class="font-mono font-bold text-xl text-white mt-1">{{ $stats['today']['time'] ?? '--:--' }}</p>
                            </div>
                            <div class="bg-black/10 rounded-2xl p-4 border border-white/5">
                                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Estimasi Jarak</p>
                                <p class="font-mono font-bold text-xl text-white mt-1">{{ $stats['today']['distance'] ?? '-' }}</p>
                            </div>
                        </div>

                        @if($stats['today']['status'] === 'belum_absen')
                        <a href="{{ route('attendance.scan') }}" class="mt-6 block w-full bg-gradient-to-r from-[#2c68f5] to-[#623ed8] text-white font-bold text-sm text-center py-3.5 rounded-2xl shadow-xl shadow-[#2c68f5]/30 hover:opacity-95 transform hover:-translate-y-0.5 transition duration-300">
                            <i class="ti ti-qrcode mr-2 text-base align-middle"></i>Buka Scanner QR Absen Sekarang
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Interactive Analytics & Insight Section --}}
                <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-6 shadow-2xl space-y-4">
                    <div>
                        <p class="text-xs font-bold text-[#ffd500] uppercase tracking-wider">Analisis Performa</p>
                        <h3 class="text-lg font-bold text-white mt-0.5">Analisis Ringkasan Kehadiran Anda</h3>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-green-500/10 to-green-600/5 border border-green-500/20 rounded-2xl p-4 text-center">
                            <div class="text-green-400 text-lg mb-1"><i class="ti ti-circle-check"></i></div>
                            <div class="text-2xl font-black text-white">{{ $stats['month']['hadir'] }}</div>
                            <div class="text-[10px] font-bold text-white/50 uppercase tracking-wider mt-1">Hadir</div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/5 border border-yellow-500/20 rounded-2xl p-4 text-center">
                            <div class="text-yellow-400 text-lg mb-1"><i class="ti ti-clock"></i></div>
                            <div class="text-2xl font-black text-white">{{ $stats['month']['terlambat'] }}</div>
                            <div class="text-[10px] font-bold text-white/50 uppercase tracking-wider mt-1">Terlambat</div>
                        </div>
                        <div class="bg-gradient-to-br from-red-500/10 to-red-600/5 border border-red-500/20 rounded-2xl p-4 text-center">
                            <div class="text-red-400 text-lg mb-1"><i class="ti ti-map-pin-off"></i></div>
                            <div class="text-2xl font-black text-white">{{ $stats['month']['di_luar'] }}</div>
                            <div class="text-[10px] font-bold text-white/50 uppercase tracking-wider mt-1">Luar Area</div>
                        </div>
                    </div>

                    <div class="bg-black/10 rounded-2xl p-5 border border-white/5">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-xs font-bold text-white/80">Rasio Kehadiran Bulan Ini</p>
                                @php $pct = $stats['month']['total'] > 0 ? round(($stats['month']['hadir'] / $stats['month']['total']) * 100) : 0; @endphp
                                <p class="text-xs text-white/40 mt-0.5">{{ $stats['month']['hadir'] }} hari terisi dari {{ $stats['month']['total'] }} total hari efektif</p>
                            </div>
                            <div class="text-2xl font-black text-[#ffd500] font-mono">{{ $pct }}%</div>
                        </div>
                        <div class="h-3 bg-white/10 rounded-full overflow-hidden p-0.5 border border-white/5">
                            <div class="h-full bg-gradient-to-r from-[#2c68f5] to-green-400 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(44,104,245,0.5)]" style="width: {{ $pct }}%"></div>
                        </div>

                        {{-- Professional Advice Box based on performance --}}
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-start gap-2.5 text-xs text-white/70">
                            <i class="ti ti-bulb text-base text-[#ffd500] mt-0.5"></i>
                            <p>
                                @if($pct >= 90)
                                    Luar biasa! Pertahankan konsistensi kedisiplinan Anda yang tinggi. Prestasi yang sangat membanggakan sekolah.
                                @elseif($pct >= 75)
                                    Cukup baik, namun usahakan untuk mengurangi keterlambatan di hari berikutnya agar rasio tetap prima.
                                @else
                                    Perhatian: Tingkat kehadiran Anda di bawah standar. Mohon koordinasikan dengan wali kelas atau pihak kesiswaan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Teacher Discipline Leaderboard - visible only to Guru --}}
                @if(auth()->user()->role === \App\Enums\UserRole::GURU && $teacherRankings->isNotEmpty())
                <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-6 shadow-2xl space-y-4">
                    <div>
                        <p class="text-xs font-bold text-[#ffd500] uppercase tracking-wider">Papan Peringkat internal</p>
                        <h3 class="text-lg font-bold text-white mt-0.5">Peringkat Kedisiplinan Guru & Staf</h3>
                    </div>
                    <div class="divide-y divide-white/10 text-xs">
                        @foreach($teacherRankings as $index => $teacher)
                        <div class="py-2.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="font-mono font-bold text-[#ffd500] bg-white/5 h-6 w-6 rounded flex items-center justify-center">#{{ $index + 1 }}</span>
                                <span class="font-semibold text-white truncate">{{ $teacher->name }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold shrink-0">
                                {{ $teacher->attendances_count }} Hari Hadir
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Column 3: Recent Activity Section --}}
            <div class="space-y-6">
                <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-6 shadow-2xl flex flex-col h-full justify-between">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold text-[#ffd500] uppercase tracking-wider">Aktivitas Realtime</p>
                            <h3 class="text-lg font-bold text-white mt-0.5">Riwayat Pemindaian Anda</h3>
                        </div>

                        @if($recentScans->isEmpty())
                            <div class="rounded-2xl border border-white/10 bg-black/10 p-8 text-center flex flex-col items-center justify-center">
                                <div class="text-4xl mb-3">📋</div>
                                <p class="text-xs text-white/50">Belum ditemukan riwayat absensi pada log server.</p>
                            </div>
                        @else
                            <div class="space-y-3 max-h-[320px] overflow-y-auto pr-1">
                                @foreach($recentScans as $scan)
                                <div class="relative overflow-hidden rounded-2xl bg-white/5 border border-white/10 p-3.5 hover:bg-white/10 transition duration-300">
                                    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b {{
                                        $scan->result->value === 'success' ? 'from-green-400 to-green-500' :
                                        ($scan->result->value === 'late' ? 'from-yellow-400 to-yellow-500' : 'from-red-400 to-red-500')
                                    }}"></div>

                                    <div class="flex items-center justify-between gap-2 pl-2">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-white truncate">{{ $scan->attendance_date }}</p>
                                            <p class="text-[10px] text-white/60 mt-0.5">Pukul {{ $scan->scanned_at?->format('H:i') ?? '-' }} WIB</p>
                                        </div>
                                        @php
                                            $resultBadge = [
                                                'success' => ['bg' => 'bg-green-500/20 text-green-300', 'label' => 'Hadir'],
                                                'late' => ['bg' => 'bg-yellow-500/20 text-yellow-300', 'label' => 'Telat'],
                                                'outside_area' => ['bg' => 'bg-red-500/20 text-red-300', 'label' => 'Luar'],
                                                'duplicate' => ['bg' => 'bg-blue-500/20 text-blue-300', 'label' => 'Duplikat'],
                                            ];
                                            $badge = $resultBadge[$scan->result->value] ?? ['bg' => 'bg-white/10 text-white', 'label' => 'QR'];
                                        @endphp
                                        <span class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider {{ $badge['bg'] }} {{ $badge['text'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Quick LogoutAffordance --}}
                    <div class="pt-6 mt-6 border-t border-white/10">
                        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                            @csrf
                            <button type="submit" class="w-full h-11 inline-flex items-center justify-center gap-2 rounded-xl bg-red-500/20 border border-red-500/30 text-xs font-bold text-red-200 hover:bg-red-500/30 transition">
                                <i class="ti ti-logout text-sm"></i>
                                Keluar Sistem Absensi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Universal Mobile & Desktop Bottom Glass Navigation --}}
    <div class="fixed bottom-4 left-4 right-4 mx-auto max-w-md bg-white/10 backdrop-blur-2xl border border-white/20 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.4)] overflow-hidden">
        <nav class="flex items-center justify-around">
            <a href="{{ route('student.dashboard') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 py-3 px-2 text-center transition text-[#ffd500]">
                <i class="ti ti-layout-dashboard text-lg"></i>
                <span class="text-[10px] font-bold uppercase tracking-wider">Dashboard</span>
            </a>
            <a href="{{ route('attendance.scan') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 py-3 px-2 text-center transition text-white/60 hover:text-white">
                <i class="ti ti-qrcode text-lg"></i>
                <span class="text-[10px] font-bold uppercase tracking-wider">Scan QR</span>
            </a>
            <a href="{{ route('student.profile') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 py-3 px-2 text-center transition text-white/60 hover:text-white">
                <i class="ti ti-user text-lg"></i>
                <span class="text-[10px] font-bold uppercase tracking-wider">Profil Anda</span>
            </a>
        </nav>
    </div>
</div>

<script>
    // Live clock function
    setInterval(() => {
        const el = document.getElementById('liveClock');
        if (el) {
            const now = new Date();
            el.innerText = now.toTimeString().split(' ')[0];
        }
    }, 1000);

    function attendanceDashboard() {
        return {}
    }
</script>
@endsection
