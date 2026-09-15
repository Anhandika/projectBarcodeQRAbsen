<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan · Absen Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f1e3d] text-white min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    {{-- Decorative Background Glows --}}
    <div class="absolute -top-40 -left-40 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-[#2c68f5]/30 to-[#623ed8]/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 h-[500px] w-[500px] rounded-full bg-gradient-to-tr from-[#ffd500]/10 to-[#623ed8]/30 blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full text-center relative z-10">
        {{-- Glassmorphism 3D Card --}}
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5),inset_0_1px_0_rgba(255,255,255,0.2)] transform hover:scale-[1.02] transition-all duration-500">
            {{-- 3D Semi Sphere or Icon Group --}}
            <div class="relative w-32 h-32 mx-auto mb-6">
                <div class="absolute inset-0 bg-gradient-to-br from-[#2c68f5] to-[#623ed8] rounded-full opacity-30 blur-lg animate-pulse"></div>
                <div class="relative w-full h-full bg-gradient-to-tr from-[#1a3a7a] via-[#2c68f5] to-white/40 rounded-full flex items-center justify-center border border-white/30 shadow-2xl">
                    <span class="font-display font-black text-5xl text-white tracking-tighter drop-shadow-[0_4px_8px_rgba(0,0,0,0.3)]">404</span>
                </div>
                {{-- Little 3D Floating badge --}}
                <div class="absolute -bottom-2 -right-2 h-10 w-10 bg-[#ffd500] rounded-xl flex items-center justify-center text-[#0f1e3d] text-lg font-bold shadow-lg shadow-[#ffd500]/30 transform rotate-12 animate-bounce">
                    <i class="ti ti-question-mark"></i>
                </div>
            </div>

            <h1 class="font-display text-2xl font-bold tracking-tight text-white mb-2">Halaman Tidak Ditemukan</h1>
            <p class="text-white/70 text-sm mb-8 leading-relaxed">
                Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan ke alamat lain. Pastikan tautan sudah benar.
            </p>

            <a href="/" class="inline-flex w-full h-12 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#2c68f5] to-[#623ed8] text-sm font-bold text-white shadow-xl shadow-[#2c68f5]/20 hover:opacity-90 active:scale-[0.98] transition-all">
                <i class="ti ti-home text-lg"></i>
                Kembali ke Beranda
            </a>
        </div>

        <p class="mt-8 text-xs text-white/40 uppercase tracking-widest font-bold">SMK BINA UTAMA KENDAL</p>
    </div>
</body>
</html>
