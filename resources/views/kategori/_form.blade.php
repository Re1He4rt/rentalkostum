{{--
    kategori/_form.blade.php
    Partial (potongan) form yang dipakai bersama oleh halaman create & edit.
    Variabel $action (URL tujuan) dan $method (PUT saat edit, kosong saat create) dikirim dari pemanggil.
--}}
<form method="POST" action="{{ $action }}" class="space-y-4">
    {{-- Token CSRF: wajib untuk semua form POST --}}
    @csrf
    {{-- Jika $method dikirim (edit), tambahkan method PUT/PATCH tersembunyi --}}
    @isset($method)
        @method($method)
    @endisset

    {{-- Field: Nama Kategori --}}
    <div>
        <x-input-label for="nama" value="Nama Kategori" />
        {{-- old('nama', ...) = isi ulang input lama bila validasi gagal, atau nilai lama saat edit --}}
        <input id="nama" name="nama" type="text" value="{{ old('nama', $kategori->nama ?? '') }}" class="input-field mt-1">
        {{-- Menampilkan pesan error validasi khusus field ini --}}
        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
    </div>

    {{-- Field: Deskripsi (opsional) --}}
    <div>
        <x-input-label for="deskripsi" value="Deskripsi" />
        <textarea id="deskripsi" name="deskripsi" rows="3" class="input-field mt-1">{{ old('deskripsi', $kategori->deskripsi ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('deskripsi')" class="mt-1" />
    </div>

    {{-- Tombol simpan + batal (kembali ke daftar) --}}
    <div class="flex items-center gap-2 pt-2">
        <button type="submit" class="btn-primary">Simpan</button>
        <a href="{{ route('kategori.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>
