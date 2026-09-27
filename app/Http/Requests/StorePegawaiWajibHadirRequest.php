<?php

namespace App\Http\Requests;

use App\Models\PegawaiWajibHadir;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePegawaiWajibHadirRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('view-kepegawaian') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tahun_id' => ['required', 'integer'],
            'version' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'],
            'complete' => ['required', 'accepted'],
            'manual_days' => ['sometimes', 'array'],
            'manual_days.*' => ['array', 'max:7'],
            'manual_days.*.*' => ['required', 'string', Rule::in(PegawaiWajibHadir::DAYS)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'tahun_id.required' => 'Muat ulang halaman jadwal sebelum menyimpan.',
            'version.required' => 'Muat ulang halaman jadwal sebelum menyimpan.',
            'complete.required' => 'Formulir tidak terkirim lengkap. Jadwal belum diubah; muat ulang halaman dan coba lagi.',
            'complete.accepted' => 'Formulir tidak terkirim lengkap. Jadwal belum diubah.',
            'manual_days.*.*.in' => 'Hari wajib hadir tidak valid.',
        ];
    }
}
