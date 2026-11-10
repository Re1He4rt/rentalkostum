<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\TransaksiRental;
use App\Services\PengembalianService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $pengembalian = Pengembalian::with(['transaksiRental.pelanggan'])
            ->when($request->input('q'), function ($query, $q) {
                $query->whereHas('transaksiRental', function ($sub) use ($q) {
                    $sub->where('kode_transaksi', 'like', "%{$q}%")
                        ->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', "%{$q}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $transaksiAktif = TransaksiRental::with('pelanggan')
            ->where('status', 'disewa')
            ->orderBy('tanggal_rencana_kembali')
            ->get();

        $rekap = [
            'total' => Pengembalian::count(),
            'total_denda' => (float) Pengembalian::sum('denda'),
            'terlambat' => Pengembalian::where('denda', '>', 0)->count(),
        ];

        return view('pengembalian.index', compact('pengembalian', 'transaksiAktif', 'rekap'));
    }

    public function form(TransaksiRental $transaksi)
    {
        if ($transaksi->status === 'dikembalikan') {
            return redirect()
                ->route('transaksi.show', $transaksi)
                ->with('error', 'Transaksi ini sudah dikembalikan.');
        }

        $transaksi->load(['pelanggan', 'detailTransaksi.kostum']);

        $details = $transaksi->detailTransaksi->map(fn ($detail) => [
            'harga_satuan' => (float) $detail->harga_satuan,
            'jumlah' => (int) $detail->jumlah,
        ]);

        return view('pengembalian.form', compact('transaksi', 'details'));
    }

    public function proses(Request $request, TransaksiRental $transaksi, PengembalianService $service)
    {
        if ($transaksi->status === 'dikembalikan') {
            return redirect()
                ->route('transaksi.show', $transaksi)
                ->with('error', 'Transaksi ini sudah dikembalikan.');
        }

        $data = $request->validate([
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:'.$transaksi->tanggal_sewa->format('Y-m-d')],
            'kondisi' => ['required', Rule::in(['baik', 'rusak'])],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal sewa.',
            'kondisi.required' => 'Kondisi kostum wajib dipilih.',
        ]);

        $pengembalian = $service->proses($transaksi, $data);

        $pesan = 'Pengembalian berhasil diproses.';
        if ((float) $pengembalian->denda > 0) {
            $pesan .= ' Denda: Rp '.number_format((float) $pengembalian->denda, 0, ',', '.');
        }

        return redirect()->route('transaksi.show', $transaksi)->with('success', $pesan);
    }
}
