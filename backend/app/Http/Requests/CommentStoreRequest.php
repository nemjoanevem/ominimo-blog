<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {

        $body = $this->filled('body') ? trim((string)$this->input('body')) : '';

        if ($this->authUser()) {
            $name = null;
            $email = null;
        }
        else {
            $name = $this->filled('guest_name') ? trim((string)$this->input('guest_name')) : null;
            $email= $this->filled('guest_email') ? trim((string)$this->input('guest_email')) : null;
        }

        $this->merge([
            'body' => $body,
            'guest_name'  => $name,
            'guest_email' => $email,
        ]);
    }

    public function rules(): array
    {
        $auth = $this->authUser();

        $rules = [
            'body' => ['required','string','min:1'],
        ];

        if (!$auth) {
            $rules['guest_name']  = ['required','string','min:2','max:100'];
            $rules['guest_email'] = ['required','email','max:255'];
        } else {
            $rules['guest_name']  = ['nullable'];
            $rules['guest_email'] = ['nullable'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'body.required'        => __('messages.validation.comment_required'),
            'guest_name.required'  => __('messages.validation.guest_name_required'),
            'guest_email.required' => __('messages.validation.guest_email_required'),
            'guest_email.email'    => __('messages.validation.guest_email_email'),
        ];
    }

    public function authUser()
    {
        return $this->user('sanctum') ?? $this->user();
    }
}
