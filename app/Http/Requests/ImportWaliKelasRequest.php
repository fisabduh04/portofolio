<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportWaliKelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-data-master') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tahun_id' => ['required', 'integer', 'exists:tahuns,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'extensions:xlsx,xls,csv', 'max:5120'],
        ];
    }
}
