<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKostumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kostum = $this->route('kostum');

        return [
            'kode' => ['required', 'string', 'max:50', Rule::unique('kostum', 'kode')->ignore($kostum)],
            'nama' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'harga_sewa' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['tersedia', 'disewa', 'rusak'])],
            'gambar' => ['nullable', 'image', 'max:2048'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
