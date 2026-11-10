{{--
    transaksi/show.blade.php
    Detail satu transaksi: informasi umum, rincian kostum, total, tombol aksi.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Detail Transaksi</h2>
            <div class="flex items-center gap-2">
                {{-- Nota dibuka di tab baru (target="_blank") --}}
                <a href="{{ route('transaksi.nota', $transaksi) }}" target="_blank" class="btn-secondary">Cetak Nota</a>
                <a href="{{ route('transaksi.index') }}" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    {{-- Kartu 1: informasi umum transaksi (4 kolom) --}}
    <div class="card mb-4">
        <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="text-gray-500">Kode Transaksi</div>
                <div class="font-medium text-gray-800">{{ $transaksi->kode_transaksi }}</div>
            </div>
            <div>
                <div class="text-gray-500">Pelanggan</div>
                <div class="font-medium text-gray-800">{{ $transaksi->pelanggan->nama ?? '-' }}</div>
            </div>
            <div>
                {{-- Badge hijau bila sudah dikembalikan, kuning bila masih disewa --}}
                <div class="text-gray-500">Status</div>
                <div>
                    <span class="badge {{ $transaksi->status === 'dikembalikan' ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($transaksi->status) }}
                    </span>
                </div>
            </div>
            <div>
                {{-- Petugas = user yang membuat transaksi (relasi user) --}}
                <div class="text-gray-500">Petugas</div>
                <div class="font-medium text-gray-800">{{ $transaksi->user->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-gray-500">Tanggal Mulai</div>
                <div class="font-medium text-gray-800">{{ $transaksi->tanggal_sewa?->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="text-gray-500">Rencana Kembali</div>
                <div class="font-medium text-gray-800">{{ $transaksi->tanggal_rencana_kembali?->format('d/m/Y') }}</div>
            </div>
            <div>
                {{-- ?-> aman bila null, lalu ?? '-' menampilkan strip bila kosong --}}
                <div class="text-gray-500">Tanggal Kembali</div>
                <div class="font-medium text-gray-800">{{ $transaksi->tanggal_kembali?->format('d/m/Y') ?? '-' }}</div>
            </div>
            <div>
                {{-- $durasi dikirim dari controller (hasil hitung durasi) --}}
                <div class="text-gray-500">Durasi</div>
                <div class="font-medium text-gray-800">{{ $durasi }} hari</div>
            </div>
        </div>
    </div>

    {{-- Kartu 2: tabel rincian kostum yang disewa --}}
    <div class="card">
        <h3 class="mb-3 font-medium text-gray-800">Rincian Kostum</h3>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Kostum</th>
                        <th>Harga / Hari</th>
                        <th>Durasi</th>
                        <th>Jumlah</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Mengulang semua baris detail milik transaksi ini --}}
                    @foreach ($transaksi->detailTransaksi as $detail)
                        <tr>
                            <td class="font-medium text-gray-800">{{ $detail->kostum->nama ?? '-' }}</td>
                            <td>Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ $durasi }} hari</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td class="text-right">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- Baris total biaya keseluruhan --}}
                <tfoot>
                    <tr class="border-t border-gray-200">
                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Total Biaya</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Tombol proses pengembalian hanya muncul untuk transaksi yang masih disewa --}}
        @if ($transaksi->status === 'disewa')
            <div class="mt-4 flex items-center justify-end">
                <a href="{{ route('pengembalian.form', $transaksi) }}" class="btn-primary">Proses Pengembalian</a>
            </div>
        @endif
    </div>
</x-app-layout>
