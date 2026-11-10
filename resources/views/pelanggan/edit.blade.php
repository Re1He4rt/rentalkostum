{{--
    pelanggan/edit.blade.php
    Halaman form untuk mengubah data pelanggan.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-semibold text-gray-800">Edit Pelanggan</h2>
    </x-slot>

    <div class="card max-w-2xl">
        {{-- Partial _form dengan action update + method PUT --}}
        @include('pelanggan._form', ['action' => route('pelanggan.update', $pelanggan), 'method' => 'PUT'])
    </div>
</x-app-layout>
