{{--
    pengembalian/form.blade.php
    Form proses pengembalian transaksi. Menampilkan info transaksi lalu form input
    tanggal kembali, kondisi, keterangan. Denda dihitung otomatis dengan Alpine.js.

    State Alpine (x-data):
      rencana       : tanggal rencana kembali (dari transaksi)
      kembali       : tanggal kembali aktual (input, x-model)
      details       : rincian kostum (untuk hitung denda per hari)
      hariTerlambat : selisih hari rencana → kembali (getter, minimal 0)
      denda         : total denda = (harga/hari x jumlah semua item) x hari terlambat
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Form Pengembalian</h2>
            <a href="{{ route('pengembalian.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </x-slot>

    {{-- Kotak ringkasan error validasi --}}
    @if ($errors->any())
        <div class="mb-4">
            <x-alert type="error">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    {{-- x-data membungkus seluruh halaman agar denda bisa dihitung live dari input tanggal --}}
    <div
        x-data="{
            // Tanggal rencana kembali (nilai tetap dari transaksi)
            rencana: '{{ $transaksi->tanggal_rencana_kembali?->format('Y-m-d') }}',
            // Tanggal kembali aktual; default hari ini, isi ulang dari old() bila ada
            kembali: '{{ old('tanggal_kembali', now()->format('Y-m-d')) }}',
            // direktif js() mengubah data PHP menjadi JSON agar bisa dipakai JavaScript
            details: @js($details),
            // Getter: jumlah hari terlambat (selisih, minimal 0 bila tidak lewat)
            get hariTerlambat() {
                if (!this.rencana || !this.kembali) return 0;
                const a = new Date(this.rencana);
                const b = new Date(this.kembali);
                const d = Math.floor((b - a) / 86400000);
                return d > 0 ? d : 0;
            },
            // Getter: denda = (total harga/hari semua item) x jumlah hari terlambat
            get denda() {
                const perHari = this.details.reduce((sum, d) => sum + parseFloat(d.harga_satuan) * parseInt(d.jumlah), 0);
                return perHari * this.hariTerlambat;
            },
            // Format ke 'Rp 50.000'
            rupiah(nilai) {
                return 'Rp ' + (nilai || 0).toLocaleString('id-ID');
            }
        }"
    >
        {{-- Kartu 1: informasi transaksi yang akan dikembalikan --}}
        <div class="card mb-4">
            <h3 class="mb-3 font-medium text-gray-800">Informasi Transaksi</h3>
            <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
                <div>
                    <div class="text-gray-500">Kode Transaksi</div>
                    <div class="font-medium text-gray-800">{{ $transaksi->kode_transaksi }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Pelanggan</div>
                    <div class="font-medium text-gray-800">{{ $transaksi->pelanggan->nama ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Rencana Kembali</div>
                    <div class="font-medium text-gray-800">{{ $transaksi->tanggal_rencana_kembali?->format('d/m/Y') }}</div>
                </div>
            </div>

            {{-- Rincian kostum: dipakai juga oleh Alpine untuk hitung denda --}}
            <div class="mt-4 overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Kostum</th>
                            <th>Harga / Hari</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksi->detailTransaksi as $detail)
                            <tr>
                                <td class="font-medium text-gray-800">{{ $detail->kostum->nama ?? '-' }}</td>
                                <td>Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}</td>
                                <td>{{ $detail->jumlah }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form proses: mengirim tanggal kembali + kondisi + keterangan --}}
        <form method="POST" action="{{ route('pengembalian.proses', $transaksi) }}" class="card">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    {{-- x-model="kembali" mengikat input ini agar denda ikut terhitung ulang --}}
                    <x-input-label for="tanggal_kembali" value="Tanggal Kembali" />
                    <input id="tanggal_kembali" name="tanggal_kembali" type="date" x-model="kembali" class="input-field mt-1">
                    <x-input-error :messages="$errors->get('tanggal_kembali')" class="mt-1" />
                </div>

                {{-- Kondisi kostum menentukan apakah stok dikembalikan/ditandai rusak --}}
                <div>
                    <x-input-label for="kondisi" value="Kondisi Kostum" />
                    <select id="kondisi" name="kondisi" class="input-field mt-1">
                        <option value="baik" @selected(old('kondisi', 'baik') === 'baik')>Baik</option>
                        <option value="rusak" @selected(old('kondisi') === 'rusak')>Rusak</option>
                    </select>
                    <x-input-error :messages="$errors->get('kondisi')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan (opsional)" />
                    <input id="keterangan" name="keterangan" type="text" value="{{ old('keterangan') }}" class="input-field mt-1">
                    <x-input-error :messages="$errors->get('keterangan')" class="mt-1" />
                </div>
            </div>

            {{-- Ringkasan denda live (x-text terhubung ke getter hariTerlambat & denda) --}}
            <div class="mt-6 flex flex-col items-end gap-1">
                <div class="flex w-full max-w-xs justify-between text-sm text-gray-600">
                    <span>Hari Terlambat</span>
                    <span x-text="hariTerlambat + ' hari'"></span>
                </div>
                <div class="flex w-full max-w-xs justify-between text-base font-semibold text-gray-800">
                    <span>Denda</span>
                    <span x-text="rupiah(denda)"></span>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-2">
                <button type="submit" class="btn-primary">Proses Pengembalian</button>
                <a href="{{ route('pengembalian.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
