{{--
    kostum/_form.blade.php
    Partial form kostum (dipakai create & edit).
    enctype="multipart/form-data" WAJIB ada karena form mengunggah file gambar.
--}}
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Field: Kode Kostum (unik) --}}
        <div>
            <x-input-label for="kode" value="Kode Kostum" />
            <input id="kode" name="kode" type="text" value="{{ old('kode', $kostum->kode ?? '') }}" class="input-field mt-1">
            <x-input-error :messages="$errors->get('kode')" class="mt-1" />
        </div>

        {{-- Field: Nama Kostum --}}
        <div>
            <x-input-label for="nama" value="Nama Kostum" />
            <input id="nama" name="nama" type="text" value="{{ old('nama', $kostum->nama ?? '') }}" class="input-field mt-1">
            <x-input-error :messages="$errors->get('nama')" class="mt-1" />
        </div>

        {{-- Field: Kategori (relasi) — daftar opsi dari data $kategori --}}
        <div>
            <x-input-label for="kategori_id" value="Kategori" />
            <select id="kategori_id" name="kategori_id" class="input-field mt-1">
                <option value="">— Pilih Kategori —</option>
                @foreach ($kategori as $item)
                    <option value="{{ $item->id }}" @selected(old('kategori_id', $kostum->kategori_id ?? '') == $item->id)>{{ $item->nama }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kategori_id')" class="mt-1" />
        </div>

        {{-- Field: Ukuran --}}
        <div>
            <x-input-label for="ukuran" value="Ukuran" />
            <input id="ukuran" name="ukuran" type="text" value="{{ old('ukuran', $kostum->ukuran ?? '') }}" class="input-field mt-1" placeholder="S / M / L / XL">
            <x-input-error :messages="$errors->get('ukuran')" class="mt-1" />
        </div>

        {{-- Field: Harga sewa per hari (angka) --}}
        <div>
            <x-input-label for="harga_sewa" value="Harga Sewa per Hari" />
            <input id="harga_sewa" name="harga_sewa" type="number" min="0" step="500" value="{{ old('harga_sewa', $kostum->harga_sewa ?? '') }}" class="input-field mt-1">
            <x-input-error :messages="$errors->get('harga_sewa')" class="mt-1" />
        </div>

        {{-- Field: Stok (angka bulat) --}}
        <div>
            <x-input-label for="stok" value="Stok" />
            <input id="stok" name="stok" type="number" min="0" value="{{ old('stok', $kostum->stok ?? 0) }}" class="input-field mt-1">
            <x-input-error :messages="$errors->get('stok')" class="mt-1" />
        </div>

        {{-- Field: Status kostum --}}
        <div>
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="input-field mt-1">
                @foreach (['tersedia', 'disewa', 'rusak'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $kostum->status ?? 'tersedia') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-1" />
        </div>

        {{-- Field: Upload gambar — input file; preview gambar lama saat mode edit --}}
        <div>
            <x-input-label for="gambar" value="Gambar" />
            <input id="gambar" name="gambar" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-600">
            <x-input-error :messages="$errors->get('gambar')" class="mt-1" />
            @isset($kostum->gambar)
                <img src="{{ Storage::url($kostum->gambar) }}" alt="{{ $kostum->nama }}" class="mt-2 h-24 w-24 rounded-lg object-cover">
            @endisset
        </div>
    </div>

    {{-- Field: Deskripsi (textarea, boleh kosong) --}}
    <div>
        <x-input-label for="deskripsi" value="Deskripsi" />
        <textarea id="deskripsi" name="deskripsi" rows="3" class="input-field mt-1">{{ old('deskripsi', $kostum->deskripsi ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('deskripsi')" class="mt-1" />
    </div>

    {{-- Tombol simpan & batal --}}
    <div class="flex items-center gap-2 pt-2">
        <button type="submit" class="btn-primary">Simpan</button>
        <a href="{{ route('kostum.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>
