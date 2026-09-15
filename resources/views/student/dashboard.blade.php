@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#0f1e3d]/5 to-white" x-data="attendanceDashboard()">
    
    {{-- Mobile Header with gradient --}}
    <div class="sticky top-0 z-40 bg-gradient-to-r from-[#1a3a7a] via-[#2c68f5] to-[#623ed8] shadow-lg">
        <div class="mx-auto max-w-md px-4 py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/70">SMK Bina Utama</p>
                    <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                </div>
                <div class="relative">
                    <div class="h-12 w-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-white/30">
                        {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                    </div>
                    @if($stats['today']['status'] === 'success')
                    <div class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-green-400 ring-2 ring-white animate-pulse"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-md px-4 py-4 space-y-4 pb-24">
        
        {{-- Status Message --}}
        @if(session('ok'))
        <div class="animate-slideIn rounded-2xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-3">
            <i class="ti ti-circle-check text-lg"></i>
            <span class="flex-1">{{ session('ok') }}</span>
            <button @click="$el.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <i class="ti ti-x"></i>
            </button>
        </div>
        @endif

        {{-- Main Status Card - 3D Effect --}}
        <div class="group relative">
            {{-- Card background with shadow --}}
            <div class="absolute inset-0 bg-gradient-to-br from-[#2c68f5]/20 to-[#623ed8]/20 rounded-3xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
            
            {{-- Main card --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-white to-[#f8f9fc] border border-white/50 shadow-2xl hover:shadow-3xl transition-all duration-500">
                {{-- Decorative elements --}}
                <div class="absolute -top-12 -right-12 h-32 w-32 rounded-full bg-gradient-to-br from-[#2c68f5]/30 to-[#623ed8]/10 blur-2xl"></div>
                <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-gradient-to-tr from-[#ffd500]/20 to-transparent blur-xl"></div>

                <div class="relative p-6 space-y-4">
                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-[#623ed8]">Absensi Hari Ini</p>
                            <h2 class="mt-1 text-3xl font-bold text-[#0f1e3d]">
                                @switch($stats['today']['status'])
                                    @case('success') Hadir @break
                                    @case('late') Terlambat @break
                                    @case('outside_area') Di Luar Area @break
                                    @case('expired') Kadaluarsa @break
                                    @case('duplicate') Duplikat @break
                                    @default Belum Absen
                                @endswitch
                            </h2>
                            <p class="mt-1 text-xs text-[#68748b]">{{ now(config('attendance.timezone'))->locale('id')->translatedFormat('l, d F') }}</p>
                        </div>

                        {{-- Status Icon --}}
                        @php
                            $statusConfig = [
                                'success' => ['icon' => 'ti-circle-check', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'glow' => 'shadow-[0_0_20px_rgba(34,197,94,.3)]'],
                                'late' => ['icon' => 'ti-clock-exclamation', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'glow' => 'shadow-[0_0_20px_rgba(234,179,8,.3)]'],
                                'outside_area' => ['icon' => 'ti-map-pin-off', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'glow' => 'shadow-[0_0_20px_rgba(220,38,38,.3)]'],
                                'belum_absen' => ['icon' => 'ti-help', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'glow' => 'shadow-[0_0_20px_rgba(107,114,128,.2)]'],
                            ];
                            $config = $statusConfig[$stats['today']['status']] ?? $statusConfig['belum_absen'];
                        @endphp
                        <div class="flex flex-col items-end gap-3">
                            <div class="relative">
                                <div class="h-14 w-14 rounded-2xl {{ $config['bg'] }} flex items-center justify-center {{ $config['text'] }} text-2xl {{ $config['glow'] }}">
                                    <i class="ti {{ $config['icon'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Time and Distance Info --}}
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-[#e3e8f0]">
                        <div class="text-center">
                            <div class="text-xs text-[#8a95a8] mb-1">Jam</div>
                            <p class="font-mono font-bold text-lg text-[#172033]">{{ $stats['today']['time'] }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-[#8a95a8] mb-1">Jarak</div>
                            <p class="font-mono font-bold text-lg text-[#172033]">{{ $stats['today']['distance'] }}</p>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    @if($stats['today']['status'] === 'belum_absen')
                    <a href="{{ route('attendance.scan') }}" class="mt-4 block w-full bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] text-white rounded-xl py-3 font-semibold text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                        <i class="ti ti-qrcode mr-2"></i>Mulai Absen
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats Grid - 3 Cards --}}
        <div class="grid grid-cols-3 gap-3">
            {{-- Hadir Card --}}
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-green-600/10 rounded-2xl blur-lg group-hover:blur-xl transition-all"></div>
                <div class="relative rounded-2xl bg-white border border-green-200/50 p-4 text-center hover:shadow-lg transition-all duration-300">
                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-2 text-green-700 text-lg">
                        <i class="ti ti-circle-check"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['month']['hadir'] }}</p>
                    <p class="text-xs text-[#8a95a8] mt-1">Hadir</p>
                </div>
            </div>

            {{-- Terlambat Card --}}
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-400/20 to-yellow-600/10 rounded-2xl blur-lg group-hover:blur-xl transition-all"></div>
                <div class="relative rounded-2xl bg-white border border-yellow-200/50 p-4 text-center hover:shadow-lg transition-all duration-300">
                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-2 text-yellow-700 text-lg">
                        <i class="ti ti-clock"></i>
                    </div>
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['month']['terlambat'] }}</p>
                    <p class="text-xs text-[#8a95a8] mt-1">Terlambat</p>
                </div>
            </div>

            {{-- Di Luar Card --}}
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-red-400/20 to-red-600/10 rounded-2xl blur-lg group-hover:blur-xl transition-all"></div>
                <div class="relative rounded-2xl bg-white border border-red-200/50 p-4 text-center hover:shadow-lg transition-all duration-300">
                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-2 text-red-700 text-lg">
                        <i class="ti ti-map-pin-off"></i>
                    </div>
                    <p class="text-2xl font-bold text-red-700">{{ $stats['month']['di_luar'] }}</p>
                    <p class="text-xs text-[#8a95a8] mt-1">Di Luar</p>
                </div>
            </div>
        </div>

        {{-- Attendance Rate Card --}}
        <div class="group relative">
            <div class="absolute inset-0 bg-gradient-to-br from-[#623ed8]/20 to-[#2c68f5]/10 rounded-2xl blur-lg group-hover:blur-xl transition-all"></div>
            <div class="relative rounded-2xl bg-white border border-[#623ed8]/20 p-5 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold text-[#623ed8] uppercase tracking-wider">Tingkat Kehadiran</p>
                        <p class="text-3xl font-bold text-[#0f1e3d] mt-1">
                            @php $pct = $stats['month']['total'] > 0 ? round(($stats['month']['hadir'] / $stats['month']['total']) * 100) : 0; @endphp
                            {{ $pct }}%
                        </p>
                    </div>
                    <div class="text-right text-xs text-[#8a95a8]">
                        <div>{{ $stats['month']['hadir'] }} dari</div>
                        <div class="font-bold text-[#172033]">{{ $stats['month']['total'] }}</div>
                    </div>
                </div>
                <div class="h-3 bg-[#e3e8f0] rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-[#623ed8] to-[#2c68f5] rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>

        {{-- Recent Activity Section --}}
        <div class="space-y-3">
            <div>
                <p class="text-xs font-bold text-[#623ed8] uppercase tracking-wider px-1">Aktivitas Terbaru</p>
                <h3 class="text-lg font-bold text-[#0f1e3d] mt-0.5 px-1">Riwayat Absensi</h3>
            </div>

            @if($recentScans->isEmpty())
                <div class="rounded-2xl bg-white border border-[#e3e8f0] p-8 text-center">
                    <div class="text-5xl text-[#e3e8f0] mb-2">📋</div>
                    <p class="text-sm text-[#8a95a8]">Belum ada riwayat absensi</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($recentScans as $scan)
                    <div class="group relative overflow-hidden rounded-2xl bg-white border border-[#e3e8f0] hover:border-[#623ed8]/30 transition-all duration-300">
                        <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b {{ 
                            $scan->result->value === 'success' ? 'from-green-400 to-green-600' :
                            ($scan->result->value === 'late' ? 'from-yellow-400 to-yellow-600' :
                            'from-red-400 to-red-600')
                        }}"></div>
                        
                        <div class="p-4 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-[#2c68f5] to-[#623ed8] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ str($scan->user->name)->substr(0,1)->upper() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-[#172033] truncate">{{ $scan->user->name }}</p>
                                <p class="text-xs text-[#8a95a8]">{{ $scan->attendance_date }} • {{ $scan->scanned_at?->format('H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @php
                                    $resultBadge = [
                                        'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => '✓'],
                                        'late' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => '⏰'],
                                        'outside_area' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => '📍'],
                                        'duplicate' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => '◆'],
                                    ];
                                    $badge = $resultBadge[$scan->result->value] ?? $resultBadge['success'];
                                @endphp
                                <div class="h-8 w-8 rounded-full {{ $badge['bg'] }} {{ $badge['text'] }} flex items-center justify-center text-xs font-bold">
                                    {{ $badge['label'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <div class="fixed bottom-0 left-0 right-0 mx-auto max-w-md bg-white border-t border-[#e3e8f0] shadow-2xl">
        <nav class="flex items-center justify-around">
            <a href="{{ route('student.dashboard') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition {{ request()->routeIs('student.dashboard') ? 'text-[#623ed8]' : 'text-[#8a95a8] hover:text-[#172033]' }}">
                <i class="ti ti-calendar-event text-lg"></i>
                <span class="text-xs font-semibold">Absensi</span>
            </a>
            <a href="{{ route('attendance.scan') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition {{ request()->routeIs('attendance.scan*') ? 'text-[#623ed8]' : 'text-[#8a95a8] hover:text-[#172033]' }}">
                <div class="relative">
                    <i class="ti ti-qrcode text-lg"></i>
                    @if($stats['today']['status'] === 'belum_absen')
                    <div class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-red-500"></div>
                    @endif
                </div>
                <span class="text-xs font-semibold">Scan</span>
            </a>
            <a href="{{ route('student.profile') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition {{ request()->routeIs('student.profile') ? 'text-[#623ed8]' : 'text-[#8a95a8] hover:text-[#172033]' }}">
                <i class="ti ti-user text-lg"></i>
                <span class="text-xs font-semibold">Profil</span>
            </a>
        </nav>
    </div>
</div>

<style>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slideIn {
    animation: slideIn 0.3s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 4px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 2px;
}

::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>

@push('scripts')
<script>
function attendanceDashboard() {
    return {
        touchStart: null,
        touchEnd: null,
        handleSwipe() {
            if (!this.touchStart || !this.touchEnd) return;
            const distance = this.touchStart - this.touchEnd;
            const isLeftSwipe = distance > 50;
            const isRightSwipe = distance < -50;
            
            if (isLeftSwipe) {
                // Swipe left - next page
            }
            if (isRightSwipe) {
                // Swipe right - prev page
            }
        }
    }
}
</script>
@endpush
@endsection
