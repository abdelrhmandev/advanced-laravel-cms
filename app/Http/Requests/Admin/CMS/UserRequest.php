<?php
namespace App\Http\Requests\Admin\CMS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(?int $Id = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($Id)],
            'mobile' => ['nullable', 'string' , Rule::unique('users', 'mobile')->ignore($Id)],
            'password' => [$Id ? 'nullable' : 'required', 'string', 'min:8'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:2048'],
            'selectedRoles' => ['array','required'],
            'selectedRoles.*' => ['integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already taken by another user.',
        ];
    }
}
