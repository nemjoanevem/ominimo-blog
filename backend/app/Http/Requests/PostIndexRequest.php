<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostIndexRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        // Provide sensible defaults
        $this->merge([
            'page' => $this->input('page', 1),
            'per_page' => $this->input('per_page', 10),
        ]);
    }

    public function rules(): array
    {
        return [
            'page' => ['integer','min:1'],
            'per_page' => ['integer','in:5,10,20,50'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.in' => __('messages.validation.per_page_in'),
        ];
    }
}
