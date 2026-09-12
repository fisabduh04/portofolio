<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaliKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-data-master') ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'tahun_id' => ['required', 'integer', 'exists:tahuns,id'],
            'penugasans' => ['required', 'array', 'min:1', 'max:100'],
            'penugasans.*' => ['required', 'array:kelas_id,pegawai_id,is_active,keterangan'],
            'penugasans.*.kelas_id' => ['required', 'integer', 'distinct', 'exists:kelas,id'],
            'penugasans.*.pegawai_id' => ['required', 'integer', 'exists:pegawais,id'],
            'penugasans.*.is_active' => ['required', 'boolean'],
            'penugasans.*.keterangan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'tahun_id.required' => 'Pilih periode terlebih dahulu.',
            'tahun_id.exists' => 'Periode tidak ditemukan.',
            'penugasans.required' => 'Tambahkan minimal satu penugasan.',
            'penugasans.*.kelas_id.distinct' => 'Kelas tidak boleh berulang dalam satu pengiriman.',
            'penugasans.*.kelas_id.exists' => 'Kelas tidak ditemukan.',
            'penugasans.*.pegawai_id.exists' => 'Pegawai tidak ditemukan.',
            'penugasans.*.is_active.boolean' => 'Status penugasan tidak valid.',
        ];
    }
}
