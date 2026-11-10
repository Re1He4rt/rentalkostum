<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransaksiRequest;
use App\Models\Kostum;
use App\Models\Pelanggan;
use App\Models\TransaksiRental;
use App\Services\TransaksiService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $transaksi = TransaksiRental::with(['pelanggan', 'user'])
            ->when($request->input('q'), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('kode_transaksi', 'like', "%{$q}%")
                        ->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', "%{$q}%"));
                });
            })
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('dari'), fn ($query, $dari) => $query->whereDate('tanggal_sewa', '>=', $dari))
            ->when($request->input('sampai'), fn ($query, $sampai) => $query->whereDate('tanggal_sewa', '<=', $sampai))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $rekap = [
            'total_transaksi' => TransaksiRental::count(),
            'sedang_disewa' => TransaksiRental::where('status', 'disewa')->count(),
            'dikembalikan' => TransaksiRental::where('status', 'dikembalikan')->count(),
            'total_pendapatan' => (float) TransaksiRental::sum('total_harga'),
        ];

        return view('transaksi.index', compact('transaksi', 'rekap'));
    }

    public function create()
    {
        $pelanggan = Pelanggan::orderBy('nama')->get();
        $kostum = Kostum::where('stok', '>', 0)
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'harga_sewa', 'stok']);

        return view('transaksi.create', compact('pelanggan', 'kostum'));
    }

    public function store(StoreTransaksiRequest $request, TransaksiService $service)
    {
        $data = $request->validated();

        $transaksi = $service->simpan($data, $data['items']);

        return redirect()
            ->route('transaksi.show', $transaksi)
            ->with('success', "Transaksi {$transaksi->kode_transaksi} berhasil dibuat.");
    }

    public function show(TransaksiRental $transaksi)
    {
        $transaksi->load(['pelanggan', 'user', 'detailTransaksi.kostum']);
        $durasi = (new TransaksiService)->hitungDurasi($transaksi->tanggal_sewa, $transaksi->tanggal_rencana_kembali);

        return view('transaksi.show', compact('transaksi', 'durasi'));
    }

    public function nota(TransaksiRental $transaksi)
    {
        $transaksi->load(['pelanggan', 'user', 'detailTransaksi.kostum']);

        return view('transaksi.nota', compact('transaksi'));
    }

    public function destroy(TransaksiRental $transaksi, TransaksiService $service)
    {
        $service->batalkan($transaksi);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
    }
}
