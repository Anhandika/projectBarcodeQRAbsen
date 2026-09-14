@php
    $navItems = [
        ['route' => 'dashboard', 'label' => 'Absensi hari ini', 'icon' => 'ti-layout-dashboard'],
        ['route' => 'monitor', 'label' => 'Layar QR', 'icon' => 'ti-qrcode'],
    ];
@endphp

<aside class="school-sidebar hidden w-[232px] shrink-0 flex-col px-5 py-5 xl:flex xl:min-h-screen" aria-label="Navigasi administrasi">
    <div class="mb-8 flex items-center gap-3">
        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-school-card bg-white text-school-navy">
            <span class="school-display text-[13px] font-bold tracking-[-0.06em]">BU</span>
        </div>
        <div>
            <div class="school-display text-xs font-semibold leading-4">SMK BINA</div>
            <div class="text-xs leading-4 text-white/55">UTAMA KENDAL</div>
        </div>
    </div>

    <div class="mb-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/50">Administrasi</div>
    <nav class="space-y-1">
        @foreach ($navItems as $item)
            @php $isActive = $active === $item['route']; @endphp
            <a
                href="{{ route($item['route']) }}"
                @if ($isActive) aria-current="page" @endif
                class="flex h-[42px] w-full items-center gap-3 rounded-school-control px-3 text-left text-[13px] font-semibold transition-colors {{ $isActive ? 'bg-gradient-to-r from-school-purple to-school-blue text-white' : 'text-white/65 hover:bg-white/10 hover:text-white' }}"
            >
                <i class="ti {{ $item['icon'] }} text-[18px]" aria-hidden="true"></i>
                {{ $item['label'] }}
            </a>
        @endforeach

        @foreach ([['label' => 'Data guru', 'icon' => 'ti-users'], ['label' => 'Data siswa', 'icon' => 'ti-school'], ['label' => 'Kelas', 'icon' => 'ti-books'], ['label' => 'Laporan', 'icon' => 'ti-file-spreadsheet'], ['label' => 'Lokasi sekolah', 'icon' => 'ti-map-pin']] as $item)
            <button type="button" class="flex h-[42px] w-full items-center gap-3 rounded-school-control px-3 text-left text-[13px] font-semibold text-white/65 transition-colors hover:bg-white/10 hover:text-white">
                <i class="ti {{ $item['icon'] }} text-[18px]" aria-hidden="true"></i>
                {{ $item['label'] }}
            </button>
        @endforeach
    </nav>

    <div class="mt-auto border-t border-white/10 pt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 text-[13px] text-white/55 transition-colors hover:text-white">
                <i class="ti ti-logout text-[17px]" aria-hidden="true"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>
