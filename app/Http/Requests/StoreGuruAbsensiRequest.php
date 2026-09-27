<?php

namespace App\Http\Requests;

use App\Models\Pegawai;
use App\Models\PegawaiAbsensi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreGuruAbsensiRequest extends FormRequest
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
            'tanggal' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'attendance' => ['required', 'array', 'min:1', 'max:200'],
            'attendance.*' => ['required', 'array:pegawai_id,status,keterangan'],
            'attendance.*.pegawai_id' => ['required', 'integer', 'distinct', Rule::in(Pegawai::guruAktif()->pluck('id')->all())],
            'attendance.*.status' => ['required', Rule::in(array_keys(PegawaiAbsensi::MANUAL_STATUSES))],
            'attendance.*.keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<int, \Closure> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $validated = $validator->validated();
            $eligibleIds = Pegawai::guruAktif()->wajibHadirPada($validated['tanggal'])->pluck('id');

            foreach ($validated['attendance'] as $index => $row) {
                if (! $eligibleIds->contains((int) $row['pegawai_id'])) {
                    $validator->errors()->add('attendance.'.$index.'.pegawai_id', 'Guru tidak memiliki kewajiban hadir pada hari yang dipilih.');
                }
            }
        }];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Pilih tanggal presensi.',
            'tanggal.date_format' => 'Tanggal presensi tidak valid.',
            'tanggal.before_or_equal' => 'Presensi tidak dapat diisi untuk tanggal mendatang.',
            'attendance.required' => 'Daftar guru harus diisi.',
            'attendance.*.pegawai_id.in' => 'Presensi hanya dapat diisi untuk guru aktif.',
            'attendance.*.pegawai_id.distinct' => 'Guru tidak boleh berulang dalam satu pengiriman.',
            'attendance.*.status.required' => 'Pilih status kehadiran setiap guru.',
            'attendance.*.status.in' => 'Status kehadiran tidak valid.',
            'attendance.*.keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ];
    }
}
