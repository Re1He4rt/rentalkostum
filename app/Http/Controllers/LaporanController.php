<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Pengembalian;
use App\Models\TransaksiRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);

        $transaksi = TransaksiRental::whereBetween('tanggal_sewa', [$dari, $sampai]);

        $rekap = [
            'total_transaksi' => (clone $transaksi)->count(),
            'sedang_disewa' => (clone $transaksi)->where('status', 'disewa')->count(),
            'dikembalikan' => (clone $transaksi)->where('status', 'dikembalikan')->count(),
            'total_pendapatan' => (float) (clone $transaksi)->sum('total_harga'),
        ];

        $totalDenda = (float) Pengembalian::whereBetween('tanggal_kembali', [$dari, $sampai])->sum('denda');

        $topKostum = DetailTransaksi::query()
            ->select('kostum_id', DB::raw('SUM(jumlah) as total_jumlah'), DB::raw('SUM(subtotal) as total_pendapatan'))
            ->whereHas('transaksiRental', fn ($query) => $query->whereBetween('tanggal_sewa', [$dari, $sampai]))
            ->with('kostum:id,kode,nama')
            ->groupBy('kostum_id')
            ->orderByDesc('total_jumlah')
            ->limit(5)
            ->get();

        return view('laporan.index', compact('dari', 'sampai', 'rekap', 'totalDenda', 'topKostum'));
    }

    public function pendapatan(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);

        $transaksi = TransaksiRental::with('pelanggan')
            ->whereBetween('tanggal_sewa', [$dari, $sampai])
            ->orderBy('tanggal_sewa')
            ->get();

        $total = (float) $transaksi->sum('total_harga');

        return view('laporan.pendapatan', compact('dari', 'sampai', 'transaksi', 'total'));
    }

    public function cetak(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);

        $transaksi = TransaksiRental::with('pelanggan')
            ->whereBetween('tanggal_sewa', [$dari, $sampai])
            ->orderBy('tanggal_sewa')
            ->get();

        $total = (float) $transaksi->sum('total_harga');

        return view('laporan.cetak', compact('dari', 'sampai', 'transaksi', 'total'));
    }

    private function rentang(Request $request): array
    {
        $dari = $request->date('dari')?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->date('sampai')?->format('Y-m-d') ?? now()->format('Y-m-d');

        return [$dari, $sampai];
    }
}
