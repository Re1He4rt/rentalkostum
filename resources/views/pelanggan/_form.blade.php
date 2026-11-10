{{--
    pelanggan/_form.blade.php
    Partial form pelanggan (dipakai create & edit).
--}}
<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    {{-- Field: Nama (wajib) --}}
    <div>
        <x-input-label for="nama" value="Nama Pelanggan" />
        <input id="nama" name="nama" type="text" value="{{ old('nama', $pelanggan->nama ?? '') }}" class="input-field mt-1">
        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Field: NIK (unik, boleh kosong) --}}
        <div>
            <x-input-label for="nik" value="NIK" />
            <input id="nik" name="nik" type="text" value="{{ old('nik', $pelanggan->nik ?? '') }}" class="input-field mt-1">
            <x-input-error :messages="$errors->get('nik')" class="mt-1" />
        </div>

        {{-- Field: Telepon --}}
        <div>
            <x-input-label for="telepon" value="Telepon" />
            <input id="telepon" name="telepon" type="text" value="{{ old('telepon', $pelanggan->telepon ?? '') }}" class="input-field mt-1">
            <x-input-error :messages="$errors->get('telepon')" class="mt-1" />
        </div>
    </div>

    {{-- Field: Email — wajib, sekaligus dipakai sebagai akun login pelanggan (role pembeli) --}}
    <div>
        <x-input-label for="email" value="Email (untuk login)" />
        <input id="email" name="email" type="email" value="{{ old('email', $pelanggan->email ?? '') }}" class="input-field mt-1">
        <p class="mt-1 text-xs text-gray-500">Email ini dipakai pelanggan untuk login. Password awal: <span class="font-medium">password</span>.</p>
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>

    {{-- Field: Alamat --}}
    <div>
        <x-input-label for="alamat" value="Alamat" />
        <textarea id="alamat" name="alamat" rows="3" class="input-field mt-1">{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
    </div>

    {{-- Tombol simpan & batal --}}
    <div class="flex items-center gap-2 pt-2">
        <button type="submit" class="btn-primary">Simpan</button>
        <a href="{{ route('pelanggan.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>
