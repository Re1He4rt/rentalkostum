<?php

use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    KostumController,
    PelangganController,
    TransaksiController,
    PengembalianController,
    LaporanController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('kategori', KategoriController::class);
    Route::resource('kostum', KostumController::class);
    Route::resource('pelanggan', PelangganController::class);

    Route::resource('transaksi', TransaksiController::class);
    Route::get('transaksi/{transaksi}/nota', [TransaksiController::class, 'nota'])->name('transaksi.nota');

    Route::get('pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('pengembalian/{transaksi}/form', [PengembalianController::class, 'form'])->name('pengembalian.form');
    Route::post('pengembalian/{transaksi}', [PengembalianController::class, 'proses'])->name('pengembalian.proses');

    Route::middleware('role:penjual')->group(function () {
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/pendapatan', [LaporanController::class, 'pendapatan'])->name('laporan.pendapatan');
        Route::get('laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    });
});

require __DIR__.'/auth.php';
