{{--
    components/pagination.blade.php  →  dipakai dengan <x-pagination :paginator="$data" />
    Menampilkan tombol navigasi halaman (dari objek paginator Laravel).
--}}
@props(['paginator' => null])

{{-- Hanya tampil jika objek paginator dikirim (tidak null) --}}
@if ($paginator)
    <div class="mt-4">
        {{-- ->links() menghasilkan tombol Prev/Next + nomor halaman otomatis --}}
        {{ $paginator->links() }}
    </div>
@endif
