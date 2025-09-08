<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('title')) {
            $data['title'] = trim((string) $this->input('title'));
        }
        if ($this->has('slug')) {
            $data['slug'] = trim((string) $this->input('slug'));
        }
        if ($this->has('body')) {
            $data['body'] = (string) $this->input('body');
        }

        if ($data) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $postId = $this->route('post')?->id;
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
