{{--
    kategori/index.blade.php
    Halaman daftar kategori: pencarian, tabel data, tombol aksi, paginasi.
--}}
<x-app-layout>
    {{-- Header halaman: judul + tombol "Tambah" --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Data Kategori</h2>
            <a href="{{ route('kategori.create') }}" class="btn-primary">Tambah Kategori</a>
        </div>
    </x-slot>

    <div class="card">
        {{-- Form pencarian: mengirim parameter ?q= ke URL saat submit (method GET) --}}
        <form method="GET" action="{{ route('kategori.index') }}" class="mb-4 flex flex-wrap gap-2">
            {{-- value diisi ulang dari request('q') agar kata kunci tetap terlihat --}}
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kategori..." class="input-field max-w-xs">
            <button type="submit" class="btn-secondary">Cari</button>
            {{-- Tombol reset hanya muncul bila sedang mencari --}}
            @if (request('q'))
                <a href="{{ route('kategori.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>

        {{-- Pembungkus agar tabel bisa digeser horizontal di layar sempit --}}
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Kostum</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- $kategori = hasil paginasi dari controller. @forelse = loop + penanganan bila kosong --}}
                    @forelse ($kategori as $item)
                        <tr>
                            {{-- Nomor urut lanjutan yang benar di setiap halaman --}}
                            <td>{{ $kategori->firstItem() + $loop->index }}</td>
                            <td class="font-medium text-gray-800">{{ $item->nama }}</td>
                            {{-- ?: '-' menampilkan tanda strip bila deskripsi kosong --}}
                            <td>{{ $item->deskripsi ?: '-' }}</td>
                            {{-- kostum_count dari withCount('kostum') di controller --}}
                            <td><span class="badge badge-info">{{ $item->kostum_count }}</span></td>
                            <td class="text-right">
                                {{-- Tautan ke form edit --}}
                                <a href="{{ route('kategori.edit', $item) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Edit</a>
                                {{-- Form hapus: memakai method DELETE + konfirmasi JavaScript --}}
                                <form action="{{ route('kategori.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        {{-- Baris ini tampil bila data kosong --}}
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Menampilkan tombol halaman (1, 2, 3, ...) --}}
        <x-pagination :paginator="$kategori" />
    </div>
</x-app-layout>
