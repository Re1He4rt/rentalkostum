<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pelanggan = $this->route('pelanggan');
        $userId = $pelanggan?->user_id;

        return [
            'nama' => ['required', 'string', 'max:150'],
            'nik' => ['nullable', 'string', 'max:30', Rule::unique('pelanggan', 'nik')->ignore($pelanggan)],
            'telepon' => ['nullable', 'string', 'max:30'],
            // Email wajib karena dipakai sebagai username akun login pelanggan (role pembeli)
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('pelanggan', 'email')->ignore($pelanggan),
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'alamat' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi karena digunakan sebagai akun login pelanggan.',
            'email.unique' => 'Email ini sudah dipakai oleh akun lain.',
        ];
    }
}
