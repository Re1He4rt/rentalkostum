{{--
    kostum/create.blade.php
    Halaman form untuk menambah kostum baru.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-semibold text-gray-800">Tambah Kostum</h2>
    </x-slot>

    <div class="card max-w-2xl">
        {{-- Memakai partial _form; action ke route store. Data $kategori dikirim otomatis dari controller --}}
        @include('kostum._form', ['action' => route('kostum.store')])
    </div>
</x-app-layout>
