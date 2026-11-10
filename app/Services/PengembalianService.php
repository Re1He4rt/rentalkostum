<?php

namespace App\Services;

use App\Models\Kostum;
use App\Models\Pengembalian;
use App\Models\TransaksiRental;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PengembalianService
{
    public function hitungHariTerlambat($rencanaKembali, $tanggalKembali): int
    {
        $rencana = Carbon::parse($rencanaKembali)->startOfDay();
        $kembali = Carbon::parse($tanggalKembali)->startOfDay();

        if ($kembali->lessThanOrEqualTo($rencana)) {
            return 0;
        }

        return (int) $rencana->diffInDays($kembali);
    }

    public function hitungDenda(TransaksiRental $transaksi, $tanggalKembali): float
    {
        $hariTerlambat = $this->hitungHariTerlambat($transaksi->tanggal_rencana_kembali, $tanggalKembali);

        if ($hariTerlambat <= 0) {
            return 0;
        }

        $transaksi->loadMissing('detailTransaksi');

        return (float) $transaksi->detailTransaksi->sum(
            fn ($detail) => (float) $detail->harga_satuan * $hariTerlambat * (int) $detail->jumlah,
        );
    }

    public function proses(TransaksiRental $transaksi, array $data): Pengembalian
    {
        return DB::transaction(function () use ($transaksi, $data) {
            $denda = $this->hitungDenda($transaksi, $data['tanggal_kembali']);

            $pengembalian = $transaksi->pengembalian()->create([
                'tanggal_kembali' => $data['tanggal_kembali'],
                'kondisi' => $data['kondisi'],
                'denda' => $denda,
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            $transaksi->update([
                'tanggal_kembali' => $data['tanggal_kembali'],
                'status' => 'dikembalikan',
            ]);

            $transaksi->loadMissing('detailTransaksi');

            foreach ($transaksi->detailTransaksi as $detail) {
                $kostum = Kostum::lockForUpdate()->find($detail->kostum_id);

                if (! $kostum) {
                    continue;
                }

                $kostum->increment('stok', $detail->jumlah);

                if ($data['kondisi'] === 'rusak') {
                    $kostum->update(['status' => 'rusak']);
                } elseif ($kostum->status === 'disewa') {
                    $kostum->update(['status' => 'tersedia']);
                }
            }

            return $pengembalian;
        });
    }
}
