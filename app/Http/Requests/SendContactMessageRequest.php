<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('site.validation_required'),
            'string' => __('site.validation_string'),
            'email' => __('site.validation_email'),
            'max' => __('site.validation_max'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('site.name'),
            'email' => 'Email',
            'subject' => __('site.subject'),
            'message' => __('site.message'),
            'website' => 'Website',
        ];
    }
}
