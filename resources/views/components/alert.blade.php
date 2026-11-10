{{--
    components/alert.blade.php  →  dipakai dengan <x-alert type="success">Teks</x-alert>
    Kotak pesan berwarna sesuai tipe (success/error/warning/info).
--}}

{{-- @props mendefinisikan parameter komponen. Jika `type` tidak dikirim, default 'info' --}}
@props(['type' => 'info'])

@php
    // Pilih rangkaian class warna berdasarkan tipe pesan
    $classes = [
        'success' => 'bg-green-50 text-green-700 border border-green-200',
        'error' => 'bg-red-50 text-red-700 border border-red-200',
        'warning' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
        'info' => 'bg-blue-50 text-blue-700 border border-blue-200',
    ][$type] ?? 'bg-gray-100 text-gray-800';
@endphp

{{-- $attributes->merge(...) menggabungkan class milik pemakai komponen dengan class bawaan --}}
<div role="alert" {{ $attributes->merge(['class' => 'rounded-lg p-4 text-sm '.$classes]) }}>
    {{-- $slot adalah isi yang ditulis di antara <x-alert> ... </x-alert> --}}
    {{ $slot }}
</div>
