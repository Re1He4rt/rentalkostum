{{--
    laporan/pendapatan.blade.php
    Daftar transaksi pada periode terpilih beserta total pendapatannya.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Laporan Pendapatan</h2>
            <div class="flex items-center gap-2">
                {{-- Buka versi cetak di tab baru, tetap bawa periode aktif --}}
                <a href="{{ route('laporan.cetak', ['dari' => $dari, 'sampai' => $sampai]) }}" target="_blank" class="btn-secondary">Cetak</a>
                <a href="{{ route('laporan.index') }}" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    {{-- Filter periode + teks periode aktif --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('laporan.pendapatan') }}" class="flex flex-wrap items-end gap-2">
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
        {{-- Carbon::parse untuk mengubah string tanggal menjadi objek tanggal terformat --}}
        <p class="mt-2 text-sm text-gray-500">Periode: {{ \Illuminate\Support\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Illuminate\Support\Carbon::parse($sampai)->format('d/m/Y') }}</p>
    </div>

    {{-- Tabel transaksi periode ini --}}
    <div class="card">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Sewa</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-medium text-gray-800">{{ $item->kode_transaksi }}</td>
                            <td>{{ $item->pelanggan->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal_sewa?->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $item->status === 'dikembalikan' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-right">Rp {{ number_format((float) $item->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">Belum ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- Baris total dari variabel $total (dikirim controller) --}}
                <tfoot>
                    <tr class="border-t border-gray-200">
                        <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700">Total Pendapatan</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
