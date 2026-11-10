{{--
    laporan/index.blade.php
    Halaman rekap: filter periode (dari–sampai), kartu ringkasan, dan tabel kostum terlaris.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Rekap Transaksi</h2>
            {{-- Bawa periode aktif ke halaman pendapatan --}}
            <a href="{{ route('laporan.pendapatan', ['dari' => $dari, 'sampai' => $sampai]) }}" class="btn-secondary">Lihat Pendapatan</a>
        </div>
    </x-slot>

    {{-- Form filter periode; $dari/$sampai dikirim dari controller --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <x-input-label for="dari" value="Dari Tanggal" />
                <input id="dari" name="dari" type="date" value="{{ $dari }}" class="input-field mt-1">
            </div>
            <div>
                <x-input-label for="sampai" value="Sampai Tanggal" />
                <input id="sampai" name="sampai" type="date" value="{{ $sampai }}" class="input-field mt-1">
            </div>
            <button type="submit" class="btn-secondary">Terapkan</button>
        </form>
    </div>

    {{-- Kartu ringkasan periode --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-card-stat label="Total Transaksi" :value="$rekap['total_transaksi']" />
        <x-card-stat label="Sedang Disewa" :value="$rekap['sedang_disewa']" />
        <x-card-stat label="Dikembalikan" :value="$rekap['dikembalikan']" />
        <x-card-stat label="Total Pendapatan" value="Rp {{ number_format($rekap['total_pendapatan'], 0, ',', '.') }}" />
        <x-card-stat label="Total Denda" value="Rp {{ number_format($totalDenda, 0, ',', '.') }}" />
    </div>

    {{-- Tabel kostum terlaris pada periode terpilih --}}
    <div class="card">
        <h3 class="mb-3 font-medium text-gray-800">Kostum Terlaris</h3>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Kostum</th>
                        <th>Jumlah Disewa</th>
                        <th class="text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- $topKostum = hasil query agregat (group by kostum, urut jumlah disewa) --}}
                    @forelse ($topKostum as $item)
                        <tr>
                            {{-- $loop->iteration otomatis 1, 2, 3, ... --}}
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kostum->kode ?? '-' }}</td>
                            <td class="font-medium text-gray-800">{{ $item->kostum->nama ?? '-' }}</td>
                            <td><span class="badge badge-info">{{ $item->total_jumlah }}</span></td>
                            <td class="text-right">Rp {{ number_format((float) $item->total_pendapatan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Belum ada data pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
