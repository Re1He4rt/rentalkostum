{{--
    pelanggan/index.blade.php
    Daftar pelanggan: pencarian, tabel, tombol aksi, paginasi.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Data Pelanggan</h2>
            <a href="{{ route('pelanggan.create') }}" class="btn-primary">Tambah Pelanggan</a>
        </div>
    </x-slot>

    <div class="card">
        {{-- Form pencarian: mencari pada nama/NIK/telepon/email --}}
        <form method="GET" action="{{ route('pelanggan.index') }}" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIK / telepon / email..." class="input-field max-w-sm">
            <button type="submit" class="btn-secondary">Cari</button>
            @if (request('q'))
                <a href="{{ route('pelanggan.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Akun Login</th>
                        <th>Transaksi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelanggan as $item)
                        <tr>
                            <td>{{ $pelanggan->firstItem() + $loop->index }}</td>
                            <td class="font-medium text-gray-800">{{ $item->nama }}</td>
                            <td>{{ $item->nik ?: '-' }}</td>
                            <td>{{ $item->telepon ?: '-' }}</td>
                            <td>{{ $item->email ?: '-' }}</td>
                            {{-- Status akun login: ada bila pelanggan terhubung ke user (role pembeli) --}}
                            <td>
                                @if ($item->user)
                                    <span class="badge badge-success">Ada</span>
                                @else
                                    <span class="badge badge-warning">Belum</span>
                                @endif
                            </td>
                            {{-- Jumlah transaksi dari withCount('transaksiRental') --}}
                            <td><span class="badge badge-info">{{ $item->transaksi_rental_count }}</span></td>
                            <td class="text-right">
                                <a href="{{ route('pelanggan.show', $item) }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">Detail</a>
                                <a href="{{ route('pelanggan.edit', $item) }}" class="ml-3 text-sm font-medium text-primary-600 hover:text-primary-700">Edit</a>
                                {{-- Hapus pelanggan (peringatan: transaksi terkait juga ikut terhapus karena relasi cascade) --}}
                                <form action="{{ route('pelanggan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pelanggan ini? Data transaksi & akun login terkait juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">Belum ada data pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$pelanggan" />
    </div>
</x-app-layout>
