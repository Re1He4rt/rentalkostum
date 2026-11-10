{{--
    kostum/edit.blade.php
    Halaman form untuk mengubah data kostum.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-semibold text-gray-800">Edit Kostum</h2>
    </x-slot>

    <div class="card max-w-2xl">
        {{-- Partial _form dengan action update + method PUT --}}
        @include('kostum._form', ['action' => route('kostum.update', $kostum), 'method' => 'PUT'])
    </div>
</x-app-layout>
