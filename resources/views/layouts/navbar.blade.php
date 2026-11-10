{{--
    layouts/navbar.blade.php
    =========================================================
    Bar atas (header) aplikasi. Berisi tombol menu (mobile),
    nama aplikasi, nama+role user, dan tombol logout.
--}}
<header class="h-16 shrink-0 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6">
    {{-- Sisi kiri: tombol hamburger (hanya mobile) + nama aplikasi --}}
    <div class="flex items-center gap-3">
        {{-- Tombol ini membalik nilai sidebarOpen (buka/tutup sidebar) di layar kecil --}}
        <button type="button" @click="sidebarOpen = ! sidebarOpen"
                class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <span class="font-semibold text-gray-800">{{ config('app.name', 'Rental Kostum') }}</span>
    </div>

    {{-- Sisi kanan: identitas user (nama + badge role) dan tombol logout --}}
    <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center gap-2">
            <span class="text-sm text-gray-700">{{ Auth::user()->name ?? '' }}</span>
            <span class="badge badge-info capitalize">{{ Auth::user()->role ?? 'pembeli' }}</span>
        </div>

        {{-- Form logout: mengirim POST ke route logout (CSRF wajib) --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-800">
                Logout
            </button>
        </form>
    </div>
</header>
