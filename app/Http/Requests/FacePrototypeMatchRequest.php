<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class FacePrototypeMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return config('face-prototype.isolated') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'direction' => ['required', 'in:masuk'],
            'consent' => ['required', 'accepted'],
            'model' => ['required', \Illuminate\Validation\Rule::in([config('face-prototype.model')])],
            'descriptor' => ['required', 'array', 'list', 'size:128'],
            'references' => ['required', 'array', 'list', 'min:1', 'max:'.config('face-prototype.max_references')],
            'references.*' => ['required', 'array:alias,descriptor'],
            'references.*.alias' => ['required', 'string', 'regex:/^UJI-[0-9]{3}$/'],
            'references.*.descriptor' => ['required', 'array', 'list', 'size:128'],
        ];
    }

    /** @return list<\Closure(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateCoordinates($validator, 'descriptor', $this->input('descriptor'));
            foreach ($this->input('references') as $index => $reference) {
                $this->validateCoordinates($validator, "references.{$index}.descriptor", $reference['descriptor']);
            }
        }];
    }

    /** @param list<mixed> $coordinates */
    private function validateCoordinates(Validator $validator, string $field, array $coordinates): void
    {
        foreach ($coordinates as $index => $coordinate) {
            if (! is_numeric($coordinate) || ! is_finite((float) $coordinate)
                || (float) $coordinate < -2 || (float) $coordinate > 2) {
                $validator->errors()->add("{$field}.{$index}", 'Koordinat vektor harus berupa angka terbatas antara -2 dan 2.');
            }
        }
    }

    public function messages(): array
    {
        return [
            'direction.in' => 'Prototipe hanya menerima jalur MASUK.',
            'consent.accepted' => 'Persetujuan peserta uji wajib diberikan.',
            'model.in' => 'Model referensi dan pemindaian harus sama.',
            'descriptor.size' => 'Vektor wajah harus berisi 128 angka.',
            'references.*.descriptor.size' => 'Vektor referensi harus berisi 128 angka.',
            'references.*.alias.regex' => 'Gunakan alias uji seperti UJI-001, bukan identitas siswa.',
        ];
    }
}
