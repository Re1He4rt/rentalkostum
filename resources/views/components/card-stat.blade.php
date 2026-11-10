{{--
    components/card-stat.blade.php  →  dipakai dengan <x-card-stat label="Total" :value="10" />
    Kotak kecil untuk menampilkan satu angka statistik (label + nilai).
--}}
@props(['label' => '', 'value' => '0'])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg p-6']) }}>
    {{-- Judul/keterangan dari angka (label) --}}
    <div class="text-sm text-gray-500">{{ $label }}</div>
    {{-- Nilai angka yang ditampilkan --}}
    <div class="mt-1 text-2xl font-semibold text-gray-800">{{ $value }}</div>
</div>
