{{--
    pelanggan/show.blade.php
    Halaman detail satu pelanggan.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Detail Pelanggan</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn-primary">Edit</a>
                <a href="{{ route('pelanggan.index') }}" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="card max-w-3xl">
        {{-- Daftar data pelanggan (istilah + keterangan) --}}
        <dl class="grid grid-cols-1 gap-y-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-gray-500">Nama</dt>
                <dd class="font-medium text-gray-800">{{ $pelanggan->nama }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">NIK</dt>
                <dd class="font-medium text-gray-800">{{ $pelanggan->nik ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Telepon</dt>
                <dd class="font-medium text-gray-800">{{ $pelanggan->telepon ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Email</dt>
                <dd class="font-medium text-gray-800">{{ $pelanggan->email ?: '-' }}</dd>
            </div>
            <div>
                {{-- Jumlah transaksi dari loadCount('transaksiRental') di controller --}}
                <dt class="text-gray-500">Jumlah Transaksi</dt>
                <dd class="font-medium text-gray-800">{{ $pelanggan->transaksi_rental_count }}</dd>
            </div>
        </dl>

        <div class="mt-4">
            <dt class="text-sm text-gray-500">Alamat</dt>
            <dd class="mt-1 text-sm text-gray-800">{{ $pelanggan->alamat ?: '-' }}</dd>
        </div>

        {{-- Informasi akun login: email pelanggan juga dipakai untuk masuk (password awal "password") --}}
        <div class="mt-4">
            <dt class="text-sm text-gray-500">Akun Login</dt>
            <dd class="mt-1 text-sm text-gray-800">
                @if ($pelanggan->user)
                    {{ $pelanggan->user->email }} <span class="badge badge-success">aktif</span>
                @else
                    <span class="text-gray-400">Belum punya akun login</span>
                @endif
            </dd>
        </div>
    </div>
</x-app-layout>
