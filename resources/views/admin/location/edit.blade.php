@extends('layouts.app')

@section('content')
<div class="max-w-[1000px] mx-auto">
    <div class="mb-6 animate-[fadeIn_.6s_ease]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-smk.png') }}" alt="Logo" class="h-10 w-10 rounded-xl bg-white p-1.5 shadow-sm border border-school-line">
                <div>
                    <h1 class="school-display text-xl font-bold text-[#0f1e3d]">Lokasi Sekolah</h1>
                    <p class="text-xs text-[#68748b]">Atur koordinat & radius absensi SMK Bina Utama</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('ok'))
    <div class="mb-4 animate-[slideIn_.4s_ease] rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center justify-between">
        <span class="flex items-center gap-2">
            <i class="ti ti-circle-check"></i>
            {{ session('ok') }}
        </span>
        <button @click="$el.parentElement.remove()" class="text-green-500 hover:text-green-700"><i class="ti ti-x"></i></button>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
        {{-- Form --}}
        <div class="relative overflow-hidden rounded-2xl bg-white border border-school-line shadow-sm p-6 sm:p-8 animate-[fadeInUp_.5s_ease]">
            <div class="absolute -top-4 -right-4 h-24 w-24 rounded-full bg-gradient-to-br from-[#2c68f5]/10 to-[#8ea8ff]/10 blur"></div>
            <form method="POST" action="{{ route('admin.location.update') }}" class="space-y-5" id="locationForm">@csrf @method('PUT')
                <div>
                    <label class="text-sm font-semibold text-[#172033] block mb-1.5">Nama Sekolah</label>
                    <input name="name" value="{{ old('name',$school->name) }}" required class="w-full h-11 rounded-xl border border-[#e3e8f0] bg-white px-3.5 text-sm focus:border-[#2c68f5] focus:ring-[3px] focus:ring-[#2c68f5]/10">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#172033] block mb-1.5">Alamat</label>
                    <input name="address" value="{{ old('address',$school->address) }}" class="w-full h-11 rounded-xl border border-[#e3e8f0] bg-white px-3.5 text-sm focus:border-[#2c68f5] focus:ring-[3px] focus:ring-[#2c68f5]/10" placeholder="Jl. Contoh No. 123, Kota">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-[#172033] block mb-1.5">Latitude</label>
                        <input name="latitude" type="number" step="0.0000001" value="{{ old('latitude',$school->latitude) }}" required class="w-full h-11 rounded-xl border border-[#e3e8f0] bg-white px-3.5 text-sm focus:border-[#2c68f5] focus:ring-[3px] focus:ring-[#2c68f5]/10" placeholder="-6.9182000">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#172033] block mb-1.5">Longitude</label>
                        <input name="longitude" type="number" step="0.0000001" value="{{ old('longitude',$school->longitude) }}" required class="w-full h-11 rounded-xl border border-[#e3e8f0] bg-white px-3.5 text-sm focus:border-[#2c68f5] focus:ring-[3px] focus:ring-[#2c68f5]/10" placeholder="110.2056000">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-[#172033] block mb-1.5">Radius Absensi (meter)</label>
                    <input name="radius_meters" type="number" min="10" max="5000" value="{{ old('radius_meters',$school->radius_meters) }}" required class="w-full h-11 rounded-xl border border-[#e3e8f0] bg-white px-3.5 text-sm focus:border-[#2c68f5] focus:ring-[3px] focus:ring-[#2c68f5]/10" placeholder="80">
                    <p class="mt-1 text-xs text-[#8a95a8]">Jarak maksimal dari titik sekolah untuk absensi valid (10-5000m)</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full h-11 rounded-xl bg-gradient-to-r from-[#1a3a7a] to-[#2c68f5] text-white font-bold text-sm shadow-[0_8px_20px_rgba(44,104,245,.35)] hover:shadow-[0_10px_28px_rgba(44,104,245,.45)] active:translate-y-[1px] transition flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy"></i> Simpan Lokasi & Radius
                    </button>
                </div>
            </form>
        </div>

        {{-- Info Panel --}}
        <div class="relative overflow-hidden rounded-2xl bg-white border border-school-line shadow-sm animate-[fadeInUp_.5s_ease_.1s]">
            <div class="absolute -top-4 -right-4 h-24 w-24 rounded-full bg-gradient-to-br from-[#167a67]/10 to-[#2cf0b8]/10 blur"></div>
            <div class="p-5 border-b border-school-line">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#623ed8]">Status Validasi</p>
                    <h3 class="school-display text-lg font-bold text-[#0f1e3d]">Metode Geofencing</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="flex flex-col items-center justify-center text-center py-8 bg-school-canvas rounded-xl border border-dashed border-school-line">
                    <div class="h-16 w-16 rounded-full bg-[#2c68f5]/10 flex items-center justify-center mb-4">
                        <i class="ti ti-map-pin text-3xl text-[#2c68f5]"></i>
                    </div>
                    <h4 class="font-bold text-[#0f1e3d] mb-2">Peta Dinonaktifkan</h4>
                    <p class="text-sm text-[#8a95a8] max-w-[280px]">Lokasi sekolah divalidasi menggunakan koordinat presisi tinggi untuk keamanan data.</p>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#8a95a8]">Validasi GPS</span>
                        <span class="font-bold text-green-600 flex items-center gap-1.5"><i class="ti ti-circle-check"></i> Aktif</span>
                    </div>
                    <div class="flex items-center justify-between text-sm border-t border-school-line pt-4">
                        <span class="text-[#8a95a8]">Radius Aktif</span>
                        <span class="font-bold text-[#0f1e3d]">{{ $school->radius_meters }} meter</span>
                    </div>
                    <div class="flex items-center justify-between text-sm border-t border-school-line pt-4">
                        <span class="text-[#8a95a8]">Zona Waktu</span>
                        <span class="font-bold text-[#0f1e3d]">{{ $school->timezone }}</span>
                    </div>
                </div>

                <div class="mt-8 p-4 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-700 flex gap-3">
                    <i class="ti ti-info-circle text-lg shrink-0"></i>
                    <p class="leading-relaxed">Pastikan koordinat yang dimasukkan sesuai dengan titik pusat sekolah agar siswa dapat melakukan absensi dengan lancar.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
