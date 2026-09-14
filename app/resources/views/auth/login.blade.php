@extends('layouts.auth')

@section('content')
    <div class="w-full max-w-[980px] overflow-hidden rounded-[18px] border border-school-line bg-white shadow-school-section lg:grid lg:grid-cols-[1.05fr_.95fr]">
        <section class="hidden bg-school-navy p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-14" aria-label="Informasi aplikasi">
            <div>
                <div class="flex items-center gap-3">
                    <div class="grid h-11 w-11 place-items-center rounded-school-card bg-white text-sm font-bold text-school-navy">BU</div>
                    <div><div class="school-display text-sm font-semibold">SMK BINA UTAMA</div><div class="text-xs text-white/55">KENDAL · Absen Digital</div></div>
                </div>
                <div class="mt-16 max-w-md"><div class="mb-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#aaa1ff]">Pusat kehadiran sekolah</div><h1 class="school-display text-4xl font-semibold leading-tight">Absensi yang jelas, aman, dan siap dipindai.</h1><p class="mt-5 text-base leading-7 text-white/65">QR dinamis, lokasi sekolah, dan status kehadiran diperiksa berurutan sebelum catatan disimpan.</p></div>
            </div>
            <div class="grid grid-cols-3 gap-3 text-xs text-white/65"><div class="rounded-school-control border border-white/10 bg-white/5 p-3"><i class="ti ti-qrcode text-lg text-[#aaa1ff]" aria-hidden="true"></i><div class="mt-2">QR aktif</div></div><div class="rounded-school-control border border-white/10 bg-white/5 p-3"><i class="ti ti-map-pin-check text-lg text-[#71ddc1]" aria-hidden="true"></i><div class="mt-2">GPS sesuai</div></div><div class="rounded-school-control border border-white/10 bg-white/5 p-3"><i class="ti ti-database-check text-lg text-[#9bbaff]" aria-hidden="true"></i><div class="mt-2">Tercatat</div></div></div>
        </section>

        <section class="p-6 sm:p-10 xl:p-14">
            <div class="mb-8 lg:hidden"><div class="mb-4 flex items-center gap-3"><div class="grid h-10 w-10 place-items-center rounded-school-card bg-school-navy text-xs font-bold text-white">BU</div><div><div class="school-display text-sm font-semibold">SMK BINA UTAMA</div><div class="text-xs text-school-muted">KENDAL · Absen Digital</div></div></div></div>
            <div class="mb-8"><div class="mb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-school-purple">Selamat datang</div><h2 class="school-display text-2xl font-semibold">Masuk ke ruang absensi</h2><p class="mt-2 text-sm leading-6 text-school-muted">Gunakan akun demo sesuai peran Anda untuk mencoba alur Digital Plinth.</p></div>

            @if ($errors->any())
                <div class="mb-5 rounded-school-control border border-[#f0d2d5] bg-[#fff6f7] px-4 py-3 text-sm text-school-danger" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-school-ink">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@sekolah.test" class="h-11 w-full rounded-school-control border border-school-line bg-white px-3 text-sm outline-none transition focus:border-school-blue focus:ring-3 focus:ring-school-blue/10">
                    @error('email')<p class="mt-1.5 text-xs text-school-danger">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="mb-2 flex items-center justify-between gap-3"><label for="password" class="block text-sm font-semibold text-school-ink">Kata sandi</label><span class="text-xs text-school-soft">demo: password</span></div>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="h-11 w-full rounded-school-control border border-school-line bg-white px-3 text-sm outline-none transition focus:border-school-blue focus:ring-3 focus:ring-school-blue/10">
                    @error('password')<p class="mt-1.5 text-xs text-school-danger">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-2 text-xs text-school-muted"><input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-school-line text-school-action focus:ring-school-blue/20">Ingat perangkat ini</label>
                <button type="submit" class="school-button school-button-primary w-full">Masuk ke aplikasi <i class="ti ti-arrow-right" aria-hidden="true"></i></button>
            </form>

            <div class="mt-8 border-t border-school-line pt-5"><div class="mb-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-school-soft">Akun uji tersedia</div><div class="grid gap-2 text-xs"><div class="flex items-center justify-between rounded-school-control bg-school-canvas px-3 py-2.5"><span class="font-semibold">Admin sekolah</span><code class="text-school-muted">adminsekolah@example.test</code></div><div class="flex items-center justify-between rounded-school-control bg-school-canvas px-3 py-2.5"><span class="font-semibold">Guru</span><code class="text-school-muted">guru@example.test</code></div><div class="flex items-center justify-between rounded-school-control bg-school-canvas px-3 py-2.5"><span class="font-semibold">Siswa</span><code class="text-school-muted">siswa@example.test</code></div></div><p class="mt-3 text-xs text-school-soft">Kata sandi untuk semua akun demo: <strong class="text-school-muted">password</strong></p></div>
        </section>
    </div>
@endsection
