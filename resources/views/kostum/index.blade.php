{{--
    kostum/index.blade.php
    Daftar kostum: pencarian (kode/nama), filter (kategori & status), tabel, paginasi.
--}}
<x-app-layout>
    {{-- Header: judul + tombol tambah --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Data Kostum</h2>
            <a href="{{ route('kostum.create') }}" class="btn-primary">Tambah Kostum</a>
        </div>
    </x-slot>

    <div class="card">
        {{-- Form filter: kata kunci + kategori + status (semua dikirim lewat GET) --}}
        <form method="GET" action="{{ route('kostum.index') }}" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / nama..." class="input-field max-w-xs">
            {{-- Dropdown kategori: opsi dari data $kategori, terpilih bila sesuai request --}}
            <select name="kategori_id" class="input-field max-w-xs">
                <option value="">Semua Kategori</option>
                @foreach ($kategori as $item)
                    <option value="{{ $item->id }}" @selected(request('kategori_id') == $item->id)>{{ $item->nama }}</option>
                @endforeach
            </select>
            {{-- Dropdown status kostum --}}
            <select name="status" class="input-field max-w-xs">
                <option value="">Semua Status</option>
                @foreach (['tersedia', 'disewa', 'rusak'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary">Terapkan</button>
            {{-- Tombol reset muncul bila ada filter aktif --}}
            @if (request()->hasAny(['q', 'kategori_id', 'status']))
                <a href="{{ route('kostum.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Ukuran</th>
                        <th>Harga / Hari</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kostum as $item)
                        <tr>
                            <td>{{ $kostum->firstItem() + $loop->index }}</td>
                            {{-- Kolom gambar: tampilkan thumbnail bila ada file, jika tidak kotak abu-abu --}}
                            <td>
                                @if ($item->gambar)
                                    {{-- Storage::url() mengubah path file menjadi URL publik yang bisa diakses browser --}}
                                    <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}" class="h-12 w-12 rounded-lg object-cover">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-100"></div>
                                @endif
                            </td>
                            <td class="font-medium text-gray-800">{{ $item->kode }}</td>
                            <td>{{ $item->nama }}</td>
                            {{-- Relasi kategori (kostum->kategori->nama) --}}
                            <td>{{ $item->kategori->nama ?? '-' }}</td>
                            <td>{{ $item->ukuran ?: '-' }}</td>
                            {{-- number_format: 75000 → 75.000 --}}
                            <td>Rp {{ number_format((float) $item->harga_sewa, 0, ',', '.') }}</td>
                            <td>{{ $item->stok }}</td>
                            {{-- Badge warna berbeda sesuai status (hijau/kuning/merah) --}}
                            <td>
                                @php
                                    $badge = ['tersedia' => 'badge-success', 'disewa' => 'badge-warning', 'rusak' => 'badge-danger'][$item->status] ?? 'badge-info';
                                @endphp
                                <span class="badge {{ $badge }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td class="text-right">
                                {{-- Tautan detail, edit, dan form hapus --}}
                                <a href="{{ route('kostum.show', $item) }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">Detail</a>
                                <a href="{{ route('kostum.edit', $item) }}" class="ml-3 text-sm font-medium text-primary-600 hover:text-primary-700">Edit</a>
                                <form action="{{ route('kostum.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kostum ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-gray-500">Belum ada data kostum.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$kostum" />
    </div>
</x-app-layout>
