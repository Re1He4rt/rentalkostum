{{--
    layouts/app.blade.php
    =========================================================
    Kerangka (layout) utama aplikasi.
    Berfungsi membungkus semua halaman yang butuh login,
    dengan struktur: sidebar kiri + navbar atas + area konten.
    Halaman lain cukup memakai <x-app-layout> ... </x-app-layout>.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
    {{-- x-data di atas = state Alpine.js untuk membuka/menutup sidebar di layar kecil --}}
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- Token CSRF wajib ada agar form POST aman dari serangan CSRF --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Judul tab browser, diambil dari config/app.php (APP_NAME) --}}
        <title>{{ config('app.name', 'Rental Kostum') }}</title>

        {{-- Mengambil font Figtree & Poppins dari CDN --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|poppins:400,500,600,700&display=swap" rel="stylesheet" />

        {{-- Memuat CSS (Tailwind) dan JS (Alpine) hasil build Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        <div class="min-h-screen lg:flex">
            {{-- Lapisan gelap (overlay) saat sidebar terbuka di mode mobile; klik untuk menutup --}}
            <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden" style="display: none;"></div>

            {{-- Memuat komponen sidebar (menu navigasi kiri) --}}
            @include('layouts.sidebar')

            {{-- Area kanan: menampung navbar + konten halaman --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Memuat komponen navbar (bar atas: tombol menu, nama user, logout) --}}
                @include('layouts.navbar')

                {{-- Area utama konten halaman --}}
                <main class="flex-1 p-4 sm:p-6">
                    {{-- Pesan sukses: tampil setelah operasi simpan/ubah/hapus berhasil --}}
                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success">{{ session('success') }}</x-alert>
                        </div>
                    @endif

                    {{-- Pesan error: tampil jika operasi gagal --}}
                    @if (session('error'))
                        <div class="mb-4">
                            <x-alert type="error">{{ session('error') }}</x-alert>
                        </div>
                    @endif

                    {{-- Slot "header" (judul halaman) yang dikirim dari tiap halaman (opsional) --}}
                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    {{-- Slot utama: isi konten halaman --}}
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
