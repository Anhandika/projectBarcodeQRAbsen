@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#0f1e3d]/5 to-white" x-data="attendanceScanner({ scanUrl: '{{ route('attendance.scan.store') }}', demoToken: '{{ $demoQrToken }}' })">
    
    {{-- Mobile Header --}}
    <div class="sticky top-0 z-40 bg-gradient-to-r from-[#1a3a7a] via-[#2c68f5] to-[#623ed8] shadow-lg">
        <div class="mx-auto max-w-md px-4 py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/70">SMK Bina Utama</p>
                    <p class="text-sm font-bold text-white">Layar Absen</p>
                </div>
                <div class="h-10 w-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-sm">
                    {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-md px-4 py-4 space-y-4 pb-24">
        
        {{-- User Info Card --}}
        <div class="relative overflow-hidden rounded-2xl bg-white border border-[#e3e8f0] shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs text-[#8a95a8] font-semibold">Pemindai sebagai</p>
                    <h2 class="text-lg font-bold text-[#0f1e3d] mt-1">{{ $activeUser->name }}</h2>
                    <p class="text-xs text-[#68748b] mt-1">{{ $activeUser->role?->label() }} • {{ $activeUser->identifier }}</p>
                </div>
                @if($activeUser->class_name)
                <span class="inline-flex items-center rounded-lg bg-[#f2f5fa] px-3 py-1.5 text-xs font-semibold text-[#623ed8]">
                    {{ $activeUser->class_name }}
                </span>
                @endif
            </div>
        </div>

        {{-- Status Messages --}}
        @if(session('error'))
        <div class="animate-slideIn rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 flex items-center gap-3">
            <i class="ti ti-alert-circle text-lg"></i>
            <span class="flex-1">{{ session('error') }}</span>
        </div>
        @endif

        {{-- Location Status Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white border border-[#e3e8f0] shadow-sm">
            <div class="p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-[#623ed8]">Lokasi GPS</p>
                        <h3 class="text-lg font-bold text-[#0f1e3d] mt-1">
                            <span x-text="locationState === 'ready' ? '✓ Siap' : locationState === 'loading' ? '⏳ Mencari...' : '✗ Belum'"></span>
                        </h3>
                    </div>
                    <div class="text-right text-xs text-[#8a95a8]" x-show="accuracy !== null">
                        <div><strong x-text="accuracy ? Math.round(accuracy) + 'm' : '-'"></strong></div>
                        <div>akurasi</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-xl bg-[#f2f5fa] p-3 text-center">
                        <div class="text-[#8a95a8] mb-1">Latitude</div>
                        <div class="font-mono font-bold text-[#172033]" x-text="latitude ? latitude.toFixed(6) : '--'"></div>
                    </div>
                    <div class="rounded-xl bg-[#f2f5fa] p-3 text-center">
                        <div class="text-[#8a95a8] mb-1">Longitude</div>
                        <div class="font-mono font-bold text-[#172033]" x-text="longitude ? longitude.toFixed(6) : '--'"></div>
                    </div>
                </div>

                <button 
                    type="button" 
                    @click="locate" 
                    :disabled="locationState === 'loading'"
                    class="w-full bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] text-white rounded-xl py-2.5 font-semibold text-sm hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <i class="ti" :class="locationState === 'loading' ? 'ti-loader animate-spin' : 'ti-map-pin'"></i>
                    <span x-text="locationState === 'loading' ? 'Mencari Lokasi...' : 'Periksa Lokasi GPS'"></span>
                </button>
            </div>
        </div>

        {{-- QR Scanner Section --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a3a7a] to-[#2c68f5] shadow-lg">
            <div class="p-4">
                <div class="mb-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-white/70">Pemindai QR</p>
                    <h3 class="text-lg font-bold text-white mt-1">Pindai QR Dinamis</h3>
                    <p class="text-xs text-white/60 mt-1">Posisikan QR dalam bingkai untuk memulai absensi</p>
                </div>

                {{-- Camera Frame --}}
                <div class="relative overflow-hidden rounded-xl bg-[#0f1e3d] mb-4">
                    <div id="qr-reader" class="w-full aspect-square bg-[#0f1e3d]"></div>
                    <div class="absolute inset-0 border-2 border-white/30 rounded-xl pointer-events-none">
                        <div class="absolute top-4 left-4 w-8 h-8 border-t-2 border-l-2 border-white"></div>
                        <div class="absolute top-4 right-4 w-8 h-8 border-t-2 border-r-2 border-white"></div>
                        <div class="absolute bottom-4 left-4 w-8 h-8 border-b-2 border-l-2 border-white"></div>
                        <div class="absolute bottom-4 right-4 w-8 h-8 border-b-2 border-r-2 border-white"></div>
                    </div>
                </div>

                {{-- Camera Controls --}}
                <div class="space-y-2">
                    <button 
                        type="button" 
                        @click="startCamera" 
                        :disabled="scannerState === 'submitting' || scannerState === 'scanning' || locationState !== 'ready'"
                        class="w-full bg-white text-[#1a3a7a] rounded-xl py-2.5 font-semibold text-sm hover:bg-gray-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <i class="ti" :class="scannerState === 'scanning' ? 'ti-loader animate-spin' : 'ti-camera'"></i>
                        <span x-text="scannerState === 'scanning' ? 'Kamera Aktif...' : 'Buka Kamera'"></span>
                    </button>

                    <button 
                        type="button" 
                        @click="useDemoToken"
                        class="w-full bg-white/20 text-white border border-white/30 rounded-xl py-2.5 font-semibold text-sm hover:bg-white/30 transition-all flex items-center justify-center gap-2"
                    >
                        <i class="ti ti-qrcode"></i>
                        <span>Gunakan Token Demo</span>
                    </button>
                </div>

                {{-- Message --}}
                <div class="mt-4 text-xs text-white/70 text-center">
                    <span x-text="locationState !== 'ready' ? '⚠ Aktifkan GPS terlebih dahulu' : 'Pastikan pencahayaan cukup'"></span>
                </div>
            </div>
        </div>

        {{-- Response Card --}}
        <div x-show="scannerState === 'result'" class="animate-slideIn group relative overflow-hidden rounded-2xl shadow-lg" :class="{
            'bg-green-50 border border-green-200': result === 'success',
            'bg-yellow-50 border border-yellow-200': result === 'late',
            'bg-red-50 border border-red-200': result === 'outside',
            'bg-gray-50 border border-gray-200': result === 'expired' || result === 'duplicate'
        }">
            <div class="p-6 space-y-4 text-center">
                {{-- Result Icon --}}
                <div class="flex justify-center">
                    <div class="h-16 w-16 rounded-full flex items-center justify-center text-3xl" :class="{
                        'bg-green-100': result === 'success',
                        'bg-yellow-100': result === 'late',
                        'bg-red-100': result === 'outside',
                        'bg-gray-100': result === 'expired' || result === 'duplicate'
                    }">
                        <i class="ti" :class="{
                            'ti-circle-check text-green-700': result === 'success',
                            'ti-clock-exclamation text-yellow-700': result === 'late',
                            'ti-map-pin-off text-red-700': result === 'outside',
                            'ti-alert-circle text-gray-700': result === 'expired' || result === 'duplicate'
                        }"></i>
                    </div>
                </div>

                {{-- Result Message --}}
                <div>
                    <h2 class="text-xl font-bold mb-1" :class="{
                        'text-green-700': result === 'success',
                        'text-yellow-700': result === 'late',
                        'text-red-700': result === 'outside',
                        'text-gray-700': result === 'expired' || result === 'duplicate'
                    }" x-text="
                        result === 'success' ? 'Berhasil Tercatat!' :
                        result === 'late' ? 'Terlambat' :
                        result === 'outside' ? 'Di Luar Area' :
                        result === 'expired' ? 'QR Kadaluarsa' :
                        'Sudah Tercatat'
                    "></h2>
                    <p class="text-sm mt-2" x-text="responseMessage"></p>
                </div>

                {{-- Validation Steps --}}
                <div class="mt-4 space-y-2" x-show="Object.keys(steps).length > 0">
                    <template x-for="(step, key) in steps" :key="key">
                        <div class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg" :class="{
                            'bg-green-100 text-green-700': step.success,
                            'bg-red-100 text-red-700': !step.success
                        }">
                            <i class="ti text-lg" :class="step.success ? 'ti-circle-check' : 'ti-x'"></i>
                            <span class="text-xs" x-text="step.message"></span>
                        </div>
                    </template>
                </div>

                {{-- Action Button --}}
                <button 
                    @click="resetForScan"
                    class="w-full bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] text-white rounded-xl py-2.5 font-semibold text-sm hover:shadow-lg transition-all"
                >
                    Pindai Lagi
                </button>
            </div>
        </div>

        {{-- Info Section --}}
        <div class="rounded-2xl bg-white border border-[#e3e8f0] shadow-sm p-4">
            <div class="flex items-start gap-3">
                <i class="ti ti-info-circle text-lg text-[#623ed8] flex-shrink-0 mt-1"></i>
                <div class="text-xs text-[#68748b] space-y-1">
                    <p><strong>Persyaratan Absensi:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-1">
                        <li>GPS aktif dan akurasi < 150m</li>
                        <li>Berada dalam radius 80m dari sekolah</li>
                        <li>Belum absen pada hari yang sama</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <div class="fixed bottom-0 left-0 right-0 mx-auto max-w-md bg-white border-t border-[#e3e8f0] shadow-2xl">
        <nav class="flex items-center justify-around">
            <a href="{{ route('student.dashboard') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition text-[#8a95a8] hover:text-[#172033]">
                <i class="ti ti-calendar-event text-lg"></i>
                <span class="text-xs font-semibold">Absensi</span>
            </a>
            <a href="{{ route('attendance.scan') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition text-[#623ed8]">
                <i class="ti ti-qrcode text-lg"></i>
                <span class="text-xs font-semibold">Scan</span>
            </a>
            <a href="{{ route('student.profile') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition text-[#8a95a8] hover:text-[#172033]">
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

#qr-reader {
    background-color: #0f1e3d !important;
}

#qr-reader__dashboard {
    display: none !important;
}

#qr-reader__scan_region {
    border: none !important;
}
</style>

@push('scripts')
<script>
function attendanceScanner({ scanUrl, demoToken }) {
    return {
        scanUrl,
        demoToken,
        scannerState: 'idle', // idle, scanning, submitting, result, error
        locationState: 'idle', // idle, loading, ready, error
        latitude: null,
        longitude: null,
        accuracy: null,
        qrToken: '',
        camera: null,
        cameraRunning: false,
        result: null,
        responseMessage: 'Menunggu pemindaian...',
        steps: {},

        async locate() {
            if (this.locationState === 'loading') return;
            this.locationState = 'loading';

            if (!navigator.geolocation) {
                this.locationState = 'error';
                this.responseMessage = 'Geolokasi tidak didukung di browser Anda.';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.latitude = position.coords.latitude;
                    this.longitude = position.coords.longitude;
                    this.accuracy = position.coords.accuracy;
                    this.locationState = 'ready';
                },
                (error) => {
                    this.locationState = 'error';
                    this.responseMessage = 'Gagal mendapatkan lokasi. Pastikan izin GPS diberikan.';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        },

        async startCamera() {
            if (this.locationState !== 'ready') {
                await this.locate();
            }

            if (this.locationState !== 'ready' || this.cameraRunning) return;

            this.scannerState = 'scanning';
            this.camera = new Html5Qrcode('qr-reader');

            try {
                await this.camera.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    async (decodedText) => {
                        this.qrToken = decodedText;
                        await this.stopCamera();
                        await this.submit();
                    },
                    () => {}
                );
                this.cameraRunning = true;
            } catch (error) {
                this.scannerState = 'error';
                this.responseMessage = 'Kamera tidak dapat dibuka. Coba gunakan token demo.';
                this.camera = null;
            }
        },

        async stopCamera() {
            if (this.camera && this.cameraRunning) {
                await this.camera.stop();
                this.camera.clear();
            }
            this.cameraRunning = false;
            this.camera = null;
        },

        async submit() {
            if (!this.qrToken || this.latitude === null || this.longitude === null) {
                this.responseMessage = 'Lokasi dan token QR wajib tersedia.';
                return;
            }

            this.scannerState = 'submitting';

            try {
                const response = await fetch(this.scanUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        qr_token: this.qrToken,
                        latitude: this.latitude,
                        longitude: this.longitude,
                        accuracy: this.accuracy,
                    }),
                });

                const payload = await response.json();
                const resultMap = { outside_area: 'outside', duplicate: 'duplicate', expired: 'expired', success: 'success' };
                this.result = resultMap[payload.result] ?? 'expired';
                this.steps = payload.steps ?? {};
                this.responseMessage = payload.message ?? 'Respons server diterima.';
                this.scannerState = 'result';
                
                setTimeout(() => this.resetForScan(), 3000);
            } catch (error) {
                this.scannerState = 'error';
                this.responseMessage = 'Gagal mengirim hasil. Periksa koneksi internet.';
            }
        },

        useDemoToken() {
            this.qrToken = this.demoToken;
            this.scannerState = 'submitting';
            this.submit();
        },

        resetForScan() {
            this.qrToken = '';
            this.scannerState = 'idle';
            this.responseMessage = 'Pindai QR terbaru untuk memulai absensi.';
            this.steps = {};
        }
    }
}
</script>
@endpush
@endsection
