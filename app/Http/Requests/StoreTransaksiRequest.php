<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['required', 'exists:pelanggan,id'],
            'tanggal_sewa' => ['required', 'date'],
            'tanggal_rencana_kembali' => ['required', 'date', 'after_or_equal:tanggal_sewa'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.kostum_id' => ['required', 'exists:kostum,id', 'distinct'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_id.required' => 'Pelanggan wajib dipilih.',
            'tanggal_rencana_kembali.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'items.required' => 'Minimal pilih satu kostum.',
            'items.min' => 'Minimal pilih satu kostum.',
            'items.*.kostum_id.required' => 'Kostum wajib dipilih pada setiap baris.',
            'items.*.kostum_id.distinct' => 'Kostum tidak boleh dipilih lebih dari satu kali.',
            'items.*.jumlah.min' => 'Jumlah minimal 1.',
        ];
    }
}
