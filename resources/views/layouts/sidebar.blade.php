{{--
    layouts/sidebar.blade.php
    =========================================================
    Menu navigasi kiri. Menampilkan daftar menu sesuai role
    user yang sedang login (pembeli/penjual).
--}}
@php
    // Ambil user yang login beserta rolenya (default 'pembeli' jika kosong)
    $user = Auth::user();
    $role = $user->role ?? 'pembeli';

    // Daftar menu: route tujuan, pola aktif, label, dan role yang boleh melihat
    $menus = [
        ['route' => 'dashboard', 'pattern' => 'dashboard', 'label' => 'Dashboard', 'roles' => ['pembeli', 'penjual']],
        ['route' => 'kategori.index', 'pattern' => 'kategori.*', 'label' => 'Kategori', 'roles' => ['penjual']],
        ['route' => 'kostum.index', 'pattern' => 'kostum.*', 'label' => 'Kostum', 'roles' => ['penjual']],
        ['route' => 'pelanggan.index', 'pattern' => 'pelanggan.*', 'label' => 'Pelanggan', 'roles' => ['penjual']],
        ['route' => 'transaksi.index', 'pattern' => 'transaksi.*', 'label' => 'Transaksi', 'roles' => ['pembeli', 'penjual']],
        ['route' => 'pengembalian.index', 'pattern' => 'pengembalian.*', 'label' => 'Pengembalian', 'roles' => ['pembeli', 'penjual']],
        ['route' => 'laporan.index', 'pattern' => 'laporan.*', 'label' => 'Laporan', 'roles' => ['penjual']],
    ];
@endphp

{{-- Elemen <aside> = panel samping. Class Alpine mengatur posisi geser di mobile --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 transform transition-transform duration-200 lg:translate-x-0 lg:static lg:shrink-0"
>
    {{-- Bagian atas sidebar: nama aplikasi, diklik menuju dashboard --}}
    <div class="h-16 flex items-center px-6 border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="font-display text-lg font-semibold text-primary-600">
            {{ config('app.name', 'Rental Kostum') }}
        </a>
    </div>

    {{-- Daftar menu: hanya menu yang cocok dengan role user yang ditampilkan --}}
    <nav class="px-3 py-4 space-y-1 overflow-y-auto">
        @foreach ($menus as $menu)
            {{-- Cek apakah role user ada di dalam daftar role menu ini --}}
            @if (in_array($role, $menu['roles'], true))
                {{-- Warna berbeda bila halaman sedang aktif (request()->routeIs) --}}
                <a href="{{ route($menu['route']) }}"
                   class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs($menu['pattern']) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    {{ $menu['label'] }}
                </a>
            @endif
        @endforeach
    </nav>
</aside>
