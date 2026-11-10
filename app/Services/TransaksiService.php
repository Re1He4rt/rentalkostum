<?php

namespace App\Services;

use App\Models\Kostum;
use App\Models\TransaksiRental;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransaksiService
{
    public function hitungDurasi($mulai, $selesai): int
    {
        $mulai = Carbon::parse($mulai)->startOfDay();
        $selesai = Carbon::parse($selesai)->startOfDay();

        return (int) $mulai->diffInDays($selesai) + 1;
    }

    public function hitungSubtotal(float $hargaPerHari, int $durasi, int $jumlah): float
    {
        return $hargaPerHari * $durasi * $jumlah;
    }

    public function generateKode(): string
    {
        do {
            $kode = 'TRX-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (TransaksiRental::where('kode_transaksi', $kode)->exists());

        return $kode;
    }

    public function simpan(array $data, array $items): TransaksiRental
    {
        return DB::transaction(function () use ($data, $items) {
            $durasi = $this->hitungDurasi($data['tanggal_sewa'], $data['tanggal_rencana_kembali']);

            $transaksi = TransaksiRental::create([
                'kode_transaksi' => $this->generateKode(),
                'pelanggan_id' => $data['pelanggan_id'],
                'user_id' => auth()->id(),
                'tanggal_sewa' => $data['tanggal_sewa'],
                'tanggal_rencana_kembali' => $data['tanggal_rencana_kembali'],
                'total_harga' => 0,
                'status' => 'disewa',
            ]);

            $total = 0;

            foreach ($items as $item) {
                $jumlah = (int) $item['jumlah'];
                $kostum = Kostum::lockForUpdate()->find($item['kostum_id']);

                if (! $kostum || $kostum->stok < $jumlah) {
                    throw ValidationException::withMessages([
                        'items' => "Stok kostum \"{$kostum?->nama}\" tidak mencukupi (tersedia: {$kostum?->stok}).",
                    ]);
                }

                $subtotal = $this->hitungSubtotal((float) $kostum->harga_sewa, $durasi, $jumlah);

                $transaksi->detailTransaksi()->create([
                    'kostum_id' => $kostum->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $kostum->harga_sewa,
                    'subtotal' => $subtotal,
                ]);

                $kostum->decrement('stok', $jumlah);

                if ($kostum->stok <= 0) {
                    $kostum->update(['status' => 'disewa']);
                }

                $total += $subtotal;
            }

            $transaksi->update(['total_harga' => $total]);

            return $transaksi;
        });
    }

    public function batalkan(TransaksiRental $transaksi): void
    {
        DB::transaction(function () use ($transaksi) {
            foreach ($transaksi->detailTransaksi as $detail) {
                $kostum = Kostum::lockForUpdate()->find($detail->kostum_id);

                if ($kostum) {
                    $kostum->increment('stok', $detail->jumlah);

                    if ($kostum->status === 'disewa') {
                        $kostum->update(['status' => 'tersedia']);
                    }
                }
            }

            $transaksi->delete();
        });
    }
}
