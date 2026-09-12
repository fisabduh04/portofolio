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
            'tahun_id' => ['prohibited'],
            'kelas_id' => ['prohibited'],
            'pegawai_id' => ['prohibited'],
        ];
    }
}
