<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    } // auth handled by route

    protected function prepareForValidation(): void
    {
        // Keep it simple: accept slug if provided; otherwise service will generate it.
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'slug' => $this->filled('slug') ? trim((string) $this->input('slug')) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('messages.validation.title_required'),
            'body.required' => __('messages.validation.body_required'),
            'slug.unique' => __('messages.validation.slug_unique'),
        ];
    }
}
