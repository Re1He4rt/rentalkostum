{{--
    transaksi/index.blade.php
    Daftar transaksi + kartu rekap + filter (kode/pelanggan, status, rentang tanggal).
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Data Transaksi</h2>
            <a href="{{ route('transaksi.create') }}" class="btn-primary">Transaksi Baru</a>
        </div>
    </x-slot>

    {{-- Kartu rekap (dari controller: $rekap) --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card-stat label="Total Transaksi" :value="$rekap['total_transaksi']" />
        <x-card-stat label="Sedang Disewa" :value="$rekap['sedang_disewa']" />
        <x-card-stat label="Dikembalikan" :value="$rekap['dikembalikan']" />
        {{-- number_format untuk memformat rupiah dengan pemisah ribuan --}}
        <x-card-stat label="Total Pendapatan" value="Rp {{ number_format($rekap['total_pendapatan'], 0, ',', '.') }}" />
    </div>

    <div class="card">
        {{-- Form filter: kata kunci, status, dan rentang tanggal sewa --}}
        <form method="GET" action="{{ route('transaksi.index') }}" class="mb-4 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-5">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / nama pelanggan..." class="input-field lg:col-span-2">
            <select name="status" class="input-field">
                <option value="">Semua Status</option>
                @foreach (['disewa', 'dikembalikan'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            {{-- Filter tanggal sewa dari/sampai --}}
            <input type="date" name="dari" value="{{ request('dari') }}" class="input-field" title="Tanggal sewa dari">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="input-field" title="Tanggal sewa sampai">
            <div class="flex gap-2 sm:col-span-2 lg:col-span-5">
                <button type="submit" class="btn-secondary">Terapkan</button>
                @if (request()->hasAny(['q', 'status', 'dari', 'sampai']))
                    <a href="{{ route('transaksi.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tgl Sewa</th>
                        <th>Rencana Kembali</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $item)
                        <tr>
                            <td class="font-medium text-gray-800">{{ $item->kode_transaksi }}</td>
                            <td>{{ $item->pelanggan->nama ?? '-' }}</td>
                            {{-- ->format('d/m/Y') karena kolom ini di-cast sebagai date --}}
                            <td>{{ $item->tanggal_sewa?->format('d/m/Y') }}</td>
                            <td>{{ $item->tanggal_rencana_kembali?->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format((float) $item->total_harga, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $item->status === 'dikembalikan' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('transaksi.show', $item) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Detail</a>
                                {{-- Batal: hapus transaksi & kembalikan stok (lewat service di controller) --}}
                                <form action="{{ route('transaksi.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-700">Batal</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$transaksi" />
    </div>
</x-app-layout>
