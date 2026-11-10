{{--
    pelanggan/create.blade.php
    Halaman form untuk menambah pelanggan baru.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-semibold text-gray-800">Tambah Pelanggan</h2>
    </x-slot>

    <div class="card max-w-2xl">
        {{-- Memakai partial _form; action ke route store --}}
        @include('pelanggan._form', ['action' => route('pelanggan.store')])
    </div>
</x-app-layout>
