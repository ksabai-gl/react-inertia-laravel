<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|\Illuminate\Validation\Rules\In>>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'paused', 'failed'])],
            'region' => ['nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('region')) {
            $this->merge([
                'region' => strtoupper((string) $this->input('region')),
            ]);
        }
    }
}
