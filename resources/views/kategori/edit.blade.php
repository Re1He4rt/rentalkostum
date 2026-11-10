{{--
    kategori/edit.blade.php
    Halaman form untuk mengubah kategori yang sudah ada.
--}}
<x-app-layout>
    {{-- Judul halaman --}}
    <x-slot name="header">
        <h2 class="font-display text-xl font-semibold text-gray-800">Edit Kategori</h2>
    </x-slot>

    <div class="card max-w-2xl">
        {{-- Memakai partial _form yang sama, tetapi action ke route update + method PUT --}}
        @include('kategori._form', ['action' => route('kategori.update', $kategori), 'method' => 'PUT'])
    </div>
</x-app-layout>
