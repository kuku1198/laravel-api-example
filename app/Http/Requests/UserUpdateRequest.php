<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|filled|string|max:255',
            'email' => 'nullable|filled|string|email|max:255|unique:users,email,' . $this->route('id'),
            'new_password' => ['nullable', 'filled', Password::min(8)],
            'old_password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
