{{--
    transaksi/nota.blade.php
    Halaman nota siap cetak. TIDAK memakai layout admin (berdiri sendiri)
    agar bersih saat dicetak. Tombol "Cetak" disembunyikan saat print (class no-print).
--}}
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nota {{ $transaksi->kode_transaksi }}</title>
        {{-- Hanya CSS yang dibutuhkan (tanpa JS) --}}
        @vite(['resources/css/app.css'])
        <style>
            /* Saat halaman dicetak, elemen ber-class .no-print disembunyikan */
            @media print {
                .no-print { display: none; }
            }
        </style>
    </head>
    <body class="bg-gray-100 p-6">
        {{-- Lembar nota berukuran terbatas di tengah halaman --}}
        <div class="mx-auto max-w-lg rounded-xl bg-white p-8 shadow-sm">
            {{-- Kepala nota: nama toko + subjudul --}}
            <div class="mb-6 text-center">
                <h1 class="font-display text-xl font-semibold text-gray-800">{{ config('app.name', 'Rental Kostum') }}</h1>
                <p class="text-sm text-gray-500">Nota Penyewaan Kostum</p>
            </div>

            {{-- Informasi transaksi (label kiri, nilai kanan) --}}
            <div class="mb-4 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Kode</span><span class="font-medium">{{ $transaksi->kode_transaksi }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Pelanggan</span><span class="font-medium">{{ $transaksi->pelanggan->nama ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal Sewa</span><span class="font-medium">{{ $transaksi->tanggal_sewa?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Rencana Kembali</span><span class="font-medium">{{ $transaksi->tanggal_rencana_kembali?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Petugas</span><span class="font-medium">{{ $transaksi->user->name ?? '-' }}</span></div>
            </div>

            {{-- Tabel rincian: nama kostum, qty, subtotal --}}
            <table class="mb-4 w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-gray-500">
                        <th class="py-2">Kostum</th>
                        <th class="py-2 text-center">Qty</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi->detailTransaksi as $detail)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ $detail->kostum->nama ?? '-' }}</td>
                            <td class="py-2 text-center">{{ $detail->jumlah }}</td>
                            <td class="py-2 text-right">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="py-2 text-right font-semibold">Total</td>
                        <td class="py-2 text-right font-semibold">Rp {{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <p class="text-center text-xs text-gray-400">Terima kasih telah menyewa di tempat kami.</p>

            {{-- Tombol cetak; window.print() memicu dialog print browser. Tidak ikut tercetak. --}}
            <div class="no-print mt-6 text-center">
                <button type="button" onclick="window.print()" class="btn-primary">Cetak</button>
            </div>
        </div>
    </body>
</html>
