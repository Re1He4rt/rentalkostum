{{--
    transaksi/create.blade.php
    Form membuat transaksi baru. Bagian "Daftar Kostum" dibuat dinamis
    memakai Alpine.js: baris bisa ditambah/dihapus dan total dihitung otomatis.

    Variabel x-data (state Alpine):
      tanggalSewa/tanggalSelesai : input tanggal (untuk hitung durasi)
      kostums                    : daftar kostum dari controller (JSON)
      items                      : baris-baris kostum yang dipilih
      durasi (getter)            : jumlah hari dihitung dari kedua tanggal
      harga/stok/subtotal/total  : fungsi perhitungan angka
      tambahItem/hapusItem       : menambah/menghapus baris
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold text-gray-800">Transaksi Baru</h2>
            <a href="{{ route('transaksi.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </x-slot>

    {{-- Kotak ringkasan error validasi (jika ada) --}}
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

    <form method="POST" action="{{ route('transaksi.store') }}"
          x-data="{
              // Nilai awal diambil dari old() agar tidak hilang saat validasi gagal
              tanggalSewa: '{{ old('tanggal_sewa', now()->format('Y-m-d')) }}',
              tanggalSelesai: '{{ old('tanggal_rencana_kembali', now()->addDay()->format('Y-m-d')) }}',
              // direktif js() mengubah data PHP menjadi JSON yang bisa dibaca JavaScript
              kostums: @js($kostum),
              items: @js(old('items', [['kostum_id' => '', 'jumlah' => 1]])),
              // Getter durasi: selisih hari + 1 (hitungan inklusif), minimal 1 hari
              get durasi() {
                  if (!this.tanggalSewa || !this.tanggalSelesai) return 1;
                  const a = new Date(this.tanggalSewa);
                  const b = new Date(this.tanggalSelesai);
                  const d = Math.floor((b - a) / 86400000) + 1;
                  return d >= 1 ? d : 1;
              },
              // Cari objek kostum berdasarkan id
              kostum(id) {
                  return this.kostums.find(k => k.id == id) || null;
              },
              // Harga sewa per hari sebuah kostum
              harga(id) {
                  const k = this.kostum(id);
                  return k ? parseFloat(k.harga_sewa) : 0;
              },
              // Stok tersedia sebuah kostum
              stok(id) {
                  const k = this.kostum(id);
                  return k ? parseInt(k.stok) : 0;
              },
              // Subtotal satu baris = harga/hari x durasi x jumlah
              subtotal(item) {
                  return this.harga(item.kostum_id) * this.durasi * (parseInt(item.jumlah) || 0);
              },
              // Total seluruh baris
              total() {
                  return this.items.reduce((sum, item) => sum + this.subtotal(item), 0);
              },
              // Format angka menjadi 'Rp 75.000'
              rupiah(nilai) {
                  return 'Rp ' + (nilai || 0).toLocaleString('id-ID');
              },
              // Menambah satu baris kosong
              tambahItem() {
                  this.items.push({ kostum_id: '', jumlah: 1 });
              },
              // Menghapus baris ke-index
              hapusItem(index) {
                  this.items.splice(index, 1);
              }
          }">
        @csrf

        {{-- Kartu 1: pilih pelanggan + tanggal mulai/selesai --}}
        <div class="card mb-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="pelanggan_id" value="Pelanggan" />
                    <select id="pelanggan_id" name="pelanggan_id" class="input-field mt-1">
                        <option value="">— Pilih Pelanggan —</option>
                        @foreach ($pelanggan as $item)
                            <option value="{{ $item->id }}" @selected(old('pelanggan_id') == $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('pelanggan_id')" class="mt-1" />
                </div>

                {{-- x-model mengikat input ini ke state tanggalSewa (memicu hitung ulang durasi) --}}
                <div>
                    <x-input-label for="tanggal_sewa" value="Tanggal Mulai Sewa" />
                    <input id="tanggal_sewa" name="tanggal_sewa" type="date" x-model="tanggalSewa" class="input-field mt-1">
                    <x-input-error :messages="$errors->get('tanggal_sewa')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="tanggal_rencana_kembali" value="Tanggal Selesai Sewa" />
                    <input id="tanggal_rencana_kembali" name="tanggal_rencana_kembali" type="date" x-model="tanggalSelesai" class="input-field mt-1">
                    <x-input-error :messages="$errors->get('tanggal_rencana_kembali')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Kartu 2: daftar kostum yang disewa (baris dinamis) --}}
        <div class="card mb-4">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="font-medium text-gray-800">Daftar Kostum</h3>
                <button type="button" @click="tambahItem()" class="btn-secondary">Tambah Baris</button>
            </div>

            <div class="space-y-3">
                {{-- x-for mengulang template untuk setiap item di array items --}}
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex flex-wrap items-end gap-3 border-b border-gray-100 pb-3">
                        {{-- Pilihan kostum. :name dibuat dinamis: items[0][kostum_id], items[1][kostum_id], ... --}}
                        <div class="min-w-[220px] flex-1">
                            <label class="mb-1 block text-xs text-gray-500">Kostum</label>
                            <select :name="`items[${index}][kostum_id]`" x-model="item.kostum_id" class="input-field">
                                <option value="">— Pilih Kostum —</option>
                                <template x-for="k in kostums" :key="k.id">
                                    <option :value="k.id" x-text="k.kode + ' - ' + k.nama + ' (stok ' + k.stok + ')'"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Jumlah: dibatasi maksimal sesuai stok kostum terpilih --}}
                        <div class="w-24">
                            <label class="mb-1 block text-xs text-gray-500">Jumlah</label>
                            <input type="number" min="1" :max="stok(item.kostum_id) || 1" x-model="item.jumlah" :name="`items[${index}][jumlah]`" class="input-field">
                        </div>

                        {{-- Harga/hari & subtotal dihitung otomatis (x-text menampilkan hasil ekspresi) --}}
                        <div class="w-32">
                            <label class="mb-1 block text-xs text-gray-500">Harga / Hari</label>
                            <div class="py-2 text-sm text-gray-700" x-text="rupiah(harga(item.kostum_id))"></div>
                        </div>

                        <div class="w-32">
                            <label class="mb-1 block text-xs text-gray-500">Subtotal</label>
                            <div class="py-2 text-sm font-medium text-gray-800" x-text="rupiah(subtotal(item))"></div>
                        </div>

                        {{-- Tombol hapus baris; hanya tampil bila baris lebih dari satu --}}
                        <button type="button" @click="hapusItem(index)" x-show="items.length > 1" class="py-2 text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Kartu 3: ringkasan durasi & total biaya (live) --}}
        <div class="card">
            <div class="flex flex-col items-end gap-1">
                <div class="flex w-full max-w-xs justify-between text-sm text-gray-600">
                    <span>Durasi</span>
                    <span x-text="durasi + ' hari'"></span>
                </div>
                <div class="flex w-full max-w-xs justify-between text-base font-semibold text-gray-800">
                    <span>Total Biaya</span>
                    <span x-text="rupiah(total())"></span>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-2">
                <button type="submit" class="btn-primary">Simpan Transaksi</button>
                <a href="{{ route('transaksi.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</x-app-layout>
