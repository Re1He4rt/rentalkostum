{{--
    kategori/create.blade.php
    Halaman form untuk menambah kategori baru.
--}}
<x-app-layout>
    {{-- Judul halaman --}}
    <x-slot name="header">
        <h2 class="font-display text-xl font-semibold text-gray-800">Tambah Kategori</h2>
    </x-slot>

    <div class="card max-w-2xl">
        {{-- Mengambil isi form dari partial _form, dengan action ke route store --}}
        @include('kategori._form', ['action' => route('kategori.store')])
    </div>
</x-app-layout>
