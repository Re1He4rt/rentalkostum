{{--
    laporan/cetak.blade.php
    Versi cetak laporan pendapatan. Berdiri sendiri (tanpa layout admin),
    tombol "Cetak" disembunyikan saat print melalui class no-print.
--}}
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cetak Laporan Pendapatan</title>
        @vite(['resources/css/app.css'])
        <style>
            /* Sembunyikan elemen ber-class .no-print saat mencetak */
            @media print {
                .no-print { display: none; }
            }
        </style>
    </head>
    <body class="bg-gray-100 p-6">
        {{-- Lembar laporan yang lebih lebar karena isinya tabel --}}
        <div class="mx-auto max-w-4xl rounded-xl bg-white p-8 shadow-sm">
            {{-- Kepala laporan: nama toko, judul, periode --}}
            <div class="mb-6 text-center">
                <h1 class="font-display text-xl font-semibold text-gray-800">{{ config('app.name', 'Rental Kostum') }}</h1>
                <p class="text-sm text-gray-500">Laporan Pendapatan</p>
                <p class="text-sm text-gray-500">
                    Periode: {{ \Illuminate\Support\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Illuminate\Support\Carbon::parse($sampai)->format('d/m/Y') }}
                </p>
            </div>

            {{-- Tabel transaksi ringkas (tanpa kolom status) --}}
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-300 text-left text-gray-600">
                        <th class="py-2">No</th>
                        <th class="py-2">Kode</th>
                        <th class="py-2">Pelanggan</th>
                        <th class="py-2">Tanggal</th>
                        <th class="py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ $loop->iteration }}</td>
                            <td class="py-2">{{ $item->kode_transaksi }}</td>
                            <td class="py-2">{{ $item->pelanggan->nama ?? '-' }}</td>
                            <td class="py-2">{{ $item->tanggal_sewa?->format('d/m/Y') }}</td>
                            <td class="py-2 text-right">Rp {{ number_format((float) $item->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="py-2 text-right font-semibold">Total Pendapatan</td>
                        <td class="py-2 text-right font-semibold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- Tombol cetak; tidak ikut tercetak --}}
            <div class="no-print mt-6 text-center">
                <button type="button" onclick="window.print()" class="btn-primary">Cetak</button>
            </div>
        </div>
    </body>
</html>
