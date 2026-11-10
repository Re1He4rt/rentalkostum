{{--
    kostum/show.blade.php
    Halaman detail satu kostum: gambar di kiri, data lengkap di kanan.
--}}
<x-app-layout>
    {{-- Header: judul + tombol edit/kembali --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Detail Kostum</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('kostum.edit', $kostum) }}" class="btn-primary">Edit</a>
                <a href="{{ route('kostum.index') }}" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="card max-w-3xl">
        {{-- Grid 3 kolom: 1 kolom gambar, 2 kolom data --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            {{-- Kolom gambar kostum --}}
            <div>
                @if ($kostum->gambar)
                    <img src="{{ Storage::url($kostum->gambar) }}" alt="{{ $kostum->nama }}" class="h-48 w-full rounded-xl object-cover">
                @else
                    <div class="h-48 w-full rounded-xl bg-gray-100"></div>
                @endif
            </div>

            {{-- Kolom data: <dl>/<dt>/<dd> = daftar istilah + keterangan --}}
            <div class="sm:col-span-2">
                <dl class="grid grid-cols-1 gap-y-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Kode</dt>
                        <dd class="font-medium text-gray-800">{{ $kostum->kode }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium text-gray-800">{{ $kostum->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Kategori</dt>
                        <dd class="font-medium text-gray-800">{{ $kostum->kategori->nama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Ukuran</dt>
                        <dd class="font-medium text-gray-800">{{ $kostum->ukuran ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Harga per Hari</dt>
                        <dd class="font-medium text-gray-800">Rp {{ number_format((float) $kostum->harga_sewa, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Stok</dt>
                        <dd class="font-medium text-gray-800">{{ $kostum->stok }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        {{-- capitalize: huruf pertama jadi besar (tersedia → Tersedia) --}}
                        <dd class="font-medium text-gray-800 capitalize">{{ $kostum->status }}</dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <dt class="text-sm text-gray-500">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-800">{{ $kostum->deskripsi ?: '-' }}</dd>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
