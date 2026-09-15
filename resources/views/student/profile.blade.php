@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#0f1e3d]/5 to-white" x-data="profilePage()">
    
    {{-- Mobile Header --}}
    <div class="sticky top-0 z-40 bg-gradient-to-r from-[#1a3a7a] via-[#2c68f5] to-[#623ed8] shadow-lg">
        <div class="mx-auto max-w-md px-4 py-4">
            <div class="flex items-center justify-between gap-3">
                <h1 class="text-lg font-bold text-white">Profil & Pengaturan</h1>
                <div class="h-10 w-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-sm">
                    {{ str(auth()->user()->name)->substr(0,1)->upper() }}
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

        {{-- Profile Header Card with 3D effect --}}
        <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#2c68f5]/10 to-[#623ed8]/5 border border-white/50 shadow-2xl">
            <div class="absolute -top-16 -right-16 h-48 w-48 rounded-full bg-gradient-to-br from-[#2c68f5]/20 to-[#623ed8]/10 blur-2xl"></div>
            
            <div class="relative px-6 py-8 text-center space-y-4">
                {{-- Avatar --}}
                <div class="relative inline-block">
                    <div class="h-24 w-24 rounded-3xl bg-gradient-to-br from-[#1a3a7a] to-[#2c68f5] flex items-center justify-center text-white font-bold text-4xl shadow-2xl ring-4 ring-white/30 mx-auto">
                        {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                    </div>
                    <div class="absolute -bottom-2 -right-2 h-8 w-8 rounded-full bg-green-400 ring-4 ring-white shadow-lg animate-pulse"></div>
                </div>

                {{-- User Info --}}
                <div>
                    <h2 class="text-2xl font-bold text-[#0f1e3d]">{{ auth()->user()->name }}</h2>
                    <p class="text-sm text-[#68748b] mt-1">{{ auth()->user()->role->label() }}</p>
                </div>

                {{-- Info Badges --}}
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2f5fa] px-3 py-1.5 text-xs font-semibold text-[#623ed8]">
                        <i class="ti ti-hash"></i> {{ auth()->user()->identifier }}
                    </span>
                    @if(auth()->user()->class_name)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f2f5fa] px-3 py-1.5 text-xs font-semibold text-[#623ed8]">
                        <i class="ti ti-school"></i> {{ auth()->user()->class_name }}
                    </span>
                    @endif
                </div>

                {{-- Meta Info --}}
                <div class="pt-4 border-t border-[#e3e8f0] text-xs text-[#8a95a8] space-y-1">
                    <p>📧 {{ auth()->user()->email }}</p>
                    <p>📅 Bergabung {{ auth()->user()->created_at?->translatedFormat('F Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Edit Profile Section --}}
        <form method="POST" action="{{ route('student.profile.update') }}" @submit="handleSubmit" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="text-xs font-bold uppercase tracking-widest text-[#623ed8] block mb-3">Edit Data Diri</label>
                
                {{-- Full Name Field --}}
                <div class="relative group mb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-blue-600/10 rounded-2xl blur-lg group-focus-within:blur-xl transition-all"></div>
                    <div class="relative">
                        <label class="text-sm font-semibold text-[#172033] block mb-2">Nama Lengkap</label>
                        <div class="relative flex items-center">
                            <i class="ti ti-user absolute left-4 text-[#8a95a8] text-lg"></i>
                            <input 
                                type="text"
                                name="name" 
                                value="{{ old('name', auth()->user()->name) }}" 
                                required 
                                class="w-full h-12 rounded-xl border border-[#e3e8f0] bg-white pl-12 pr-4 text-sm focus:border-[#2c68f5] focus:ring-2 focus:ring-[#2c68f5]/20 transition outline-none"
                                placeholder="Nama lengkap"
                            >
                        </div>
                        @error('name')<p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="ti ti-alert-circle text-sm"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- NISN/NIP Field --}}
                <div class="relative group mb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-purple-600/10 rounded-2xl blur-lg group-focus-within:blur-xl transition-all"></div>
                    <div class="relative">
                        <label class="text-sm font-semibold text-[#172033] block mb-2">NISN / NIP</label>
                        <div class="relative flex items-center">
                            <i class="ti ti-code absolute left-4 text-[#8a95a8] text-lg"></i>
                            <input 
                                type="text"
                                name="identifier" 
                                value="{{ old('identifier', auth()->user()->identifier) }}" 
                                required 
                                class="w-full h-12 rounded-xl border border-[#e3e8f0] bg-white pl-12 pr-4 text-sm focus:border-[#2c68f5] focus:ring-2 focus:ring-[#2c68f5]/20 transition outline-none"
                                placeholder="NISN / NIP"
                            >
                        </div>
                        @error('identifier')<p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="ti ti-alert-circle text-sm"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Email Field --}}
                <div class="relative group mb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-pink-400/20 to-pink-600/10 rounded-2xl blur-lg group-focus-within:blur-xl transition-all"></div>
                    <div class="relative">
                        <label class="text-sm font-semibold text-[#172033] block mb-2">Email</label>
                        <div class="relative flex items-center">
                            <i class="ti ti-mail absolute left-4 text-[#8a95a8] text-lg"></i>
                            <input 
                                type="email"
                                name="email" 
                                value="{{ old('email', auth()->user()->email) }}" 
                                required 
                                class="w-full h-12 rounded-xl border border-[#e3e8f0] bg-white pl-12 pr-4 text-sm focus:border-[#2c68f5] focus:ring-2 focus:ring-[#2c68f5]/20 transition outline-none"
                                placeholder="Email"
                            >
                        </div>
                        @error('email')<p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="ti ti-alert-circle text-sm"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Class Name Field --}}
                <div class="relative group mb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-green-600/10 rounded-2xl blur-lg group-focus-within:blur-xl transition-all"></div>
                    <div class="relative">
                        <label class="text-sm font-semibold text-[#172033] block mb-2">Kelas / Unit</label>
                        <div class="relative flex items-center">
                            <i class="ti ti-building absolute left-4 text-[#8a95a8] text-lg"></i>
                            <input 
                                type="text"
                                name="class_name" 
                                value="{{ old('class_name', auth()->user()->class_name) }}" 
                                class="w-full h-12 rounded-xl border border-[#e3e8f0] bg-white pl-12 pr-4 text-sm focus:border-[#2c68f5] focus:ring-2 focus:ring-[#2c68f5]/20 transition outline-none"
                                placeholder="Kelas / Unit (opsional)"
                            >
                        </div>
                        @error('class_name')<p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="ti ti-alert-circle text-sm"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Change Password Section --}}
            <div class="relative pt-6 border-t border-[#e3e8f0]">
                <label class="text-xs font-bold uppercase tracking-widest text-[#623ed8] block mb-3">Ubah Kata Sandi</label>
                
                {{-- Password Field --}}
                <div class="relative group mb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-400/20 to-orange-600/10 rounded-2xl blur-lg group-focus-within:blur-xl transition-all"></div>
                    <div class="relative">
                        <label class="text-sm font-semibold text-[#172033] block mb-2">Kata Sandi Baru</label>
                        <div class="relative flex items-center">
                            <i class="ti ti-lock absolute left-4 text-[#8a95a8] text-lg"></i>
                            <input 
                                type="password"
                                name="password" 
                                class="w-full h-12 rounded-xl border border-[#e3e8f0] bg-white pl-12 pr-4 text-sm focus:border-[#2c68f5] focus:ring-2 focus:ring-[#2c68f5]/20 transition outline-none"
                                placeholder="Kata sandi baru (abaikan jika tidak ingin mengubah)"
                            >
                        </div>
                        <p class="text-xs text-[#8a95a8] mt-1">Minimal 6 karakter</p>
                        @error('password')<p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="ti ti-alert-circle text-sm"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Confirm Password Field --}}
                <div class="relative group mb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-400/20 to-orange-600/10 rounded-2xl blur-lg group-focus-within:blur-xl transition-all"></div>
                    <div class="relative">
                        <label class="text-sm font-semibold text-[#172033] block mb-2">Konfirmasi Kata Sandi</label>
                        <div class="relative flex items-center">
                            <i class="ti ti-lock-check absolute left-4 text-[#8a95a8] text-lg"></i>
                            <input 
                                type="password"
                                name="password_confirmation" 
                                class="w-full h-12 rounded-xl border border-[#e3e8f0] bg-white pl-12 pr-4 text-sm focus:border-[#2c68f5] focus:ring-2 focus:ring-[#2c68f5]/20 transition outline-none"
                                placeholder="Konfirmasi kata sandi"
                            >
                        </div>
                        @error('password_confirmation')<p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="ti ti-alert-circle text-sm"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 pt-4">
                <button 
                    type="submit" 
                    @click="isSubmitting = true"
                    :disabled="isSubmitting"
                    class="flex-1 bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] text-white rounded-xl py-3 font-semibold text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <i class="ti" :class="isSubmitting ? 'ti-loader animate-spin' : 'ti-check'"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                </button>
                <a 
                    href="{{ route('student.dashboard') }}" 
                    class="flex-1 bg-white border-2 border-[#e3e8f0] text-[#172033] rounded-xl py-3 font-semibold text-center hover:bg-[#f8f9fc] transition-all duration-300 flex items-center justify-center gap-2"
                >
                    <i class="ti ti-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </form>

        {{-- Logout Button --}}
        <div class="pt-4 border-t border-[#e3e8f0]">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl py-3 font-semibold text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 flex items-center justify-center gap-2"
                >
                    <i class="ti ti-logout"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <div class="fixed bottom-0 left-0 right-0 mx-auto max-w-md bg-white border-t border-[#e3e8f0] shadow-2xl">
        <nav class="flex items-center justify-around">
            <a href="{{ route('student.dashboard') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition text-[#8a95a8] hover:text-[#172033]">
                <i class="ti ti-calendar-event text-lg"></i>
                <span class="text-xs font-semibold">Absensi</span>
            </a>
            <a href="{{ route('attendance.scan') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition text-[#8a95a8] hover:text-[#172033]">
                <i class="ti ti-qrcode text-lg"></i>
                <span class="text-xs font-semibold">Scan</span>
            </a>
            <a href="{{ route('student.profile') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-3 px-2 text-center transition text-[#623ed8]">
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
</style>

@push('scripts')
<script>
function profilePage() {
    return {
        isSubmitting: false,
        handleSubmit(e) {
            this.isSubmitting = true;
        }
    }
}
</script>
@endpush
@endsection
