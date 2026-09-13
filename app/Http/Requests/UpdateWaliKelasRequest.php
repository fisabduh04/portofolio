<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWaliKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-data-master') ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'tahun_id' => ['sometimes', 'required', 'integer', 'exists:tahuns,id'],
            'kelas_id' => ['sometimes', 'required', 'integer', 'exists:kelas,id'],
            'pegawai_id' => ['sometimes', 'required', 'integer', 'exists:pegawais,id'],
        ];
    }
}
