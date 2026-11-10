{{--
    pengembalian/index.blade.php
    Dua bagian: (1) daftar transaksi yang masih disewa + tombol proses,
    (2) riwayat pengembalian yang sudah terjadi (dengan pencarian).
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-semibold text-gray-800">Data Pengembalian</h2>
    </x-slot>

    {{-- Kartu rekap pengembalian --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-card-stat label="Total Pengembalian" :value="$rekap['total']" />
        <x-card-stat label="Pengembalian Terlambat" :value="$rekap['terlambat']" />
        <x-card-stat label="Total Denda" value="Rp {{ number_format($rekap['total_denda'], 0, ',', '.') }}" />
    </div>

    {{-- Bagian 1: transaksi yang masih berstatus 'disewa' --}}
    <div class="card mb-6">
        <h3 class="mb-3 font-medium text-gray-800">Transaksi Sedang Disewa</h3>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tgl Sewa</th>
                        <th>Rencana Kembali</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksiAktif as $item)
                        <tr>
                            <td class="font-medium text-gray-800">{{ $item->kode_transaksi }}</td>
                            <td>{{ $item->pelanggan->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal_sewa?->format('d/m/Y') }}</td>
                            <td>{{ $item->tanggal_rencana_kembali?->format('d/m/Y') }}</td>
                            <td class="text-right">
                                {{-- Buka form pengembalian untuk transaksi ini --}}
                                <a href="{{ route('pengembalian.form', $item) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Proses Pengembalian</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Tidak ada transaksi yang sedang disewa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bagian 2: riwayat pengembalian --}}
    <div class="card">
        {{-- Pencarian berdasarkan kode transaksi / nama pelanggan --}}
        <form method="GET" action="{{ route('pengembalian.index') }}" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / nama pelanggan..." class="input-field max-w-sm">
            <button type="submit" class="btn-secondary">Cari</button>
            @if (request('q'))
                <a href="{{ route('pengembalian.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Kembali</th>
                        <th>Kondisi</th>
                        <th>Denda</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengembalian as $item)
                        <tr>
                            {{-- Relasi ke transaksi induk: pengembalian->transaksiRental --}}
                            <td class="font-medium text-gray-800">{{ $item->transaksiRental->kode_transaksi ?? '-' }}</td>
                            <td>{{ $item->transaksiRental->pelanggan->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal_kembali?->format('d/m/Y') }}</td>
                            {{-- Badge merah bila rusak, hijau bila baik --}}
                            <td>
                                <span class="badge {{ $item->kondisi === 'rusak' ? 'badge-danger' : 'badge-success' }}">
                                    {{ ucfirst($item->kondisi) }}
                                </span>
                            </td>
                            <td>Rp {{ number_format((float) $item->denda, 0, ',', '.') }}</td>
                            <td>{{ $item->keterangan ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data pengembalian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$pengembalian" />
    </div>
</x-app-layout>
