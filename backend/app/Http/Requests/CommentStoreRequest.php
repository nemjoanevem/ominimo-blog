<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'body' => trim((string) $this->input('body')),
        ]);
    }

    public function rules(): array
    {
        return [
            'body' => ['required','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => __('messages.validation.body_required'),
        ];
    }
}
