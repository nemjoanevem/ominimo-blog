<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->filled('title') ? trim((string) $this->input('title')) : null,
            'slug'  => $this->filled('slug')  ? trim((string) $this->input('slug'))  : null,
        ]);
    }

    public function rules(): array
    {
        $postId = $this->route('post')?->id; // model bound by slug
        return [
            'title' => ['sometimes','string','max:255'],
            'body'  => ['sometimes','string'],
            'slug'  => ['sometimes','string','max:255','unique:posts,slug,' . $postId],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => __('messages.validation.slug_unique'),
        ];
    }
}
