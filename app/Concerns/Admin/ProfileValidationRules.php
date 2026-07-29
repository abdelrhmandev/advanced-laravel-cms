<?php
namespace App\Concerns\Admin;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'mobile' => $this->mobileRules($userId),
            'email' => $this->emailRules($userId),
            'avatar' => $this->avatarRules(),
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return ['required', 'string', 'email', 'max:255', $userId === null ? Rule::unique(User::class) : Rule::unique(User::class)->ignore($userId)];
    }

    protected function mobileRules(?int $userId = null): array
    {
        return [
            'nullable',
            'string',
            'regex:/^([0-9\s\-\+\(\)]*)$/', // Allows digits, spaces, hyphens, plus, parens
            'max:255',
            $userId === null ? Rule::unique(User::class, 'mobile') : Rule::unique(User::class, 'mobile')->ignore($userId),
        ];
    }

    protected function avatarRules(): array
    {
        return [
            'nullable', // Avatar is not required to update the profile
            'image', // Must be an image file (jpeg, png, bmp, gif, svg, webp)
            'mimes:jpeg,png,jpg,webp', // Limit to specific formats
            'max:2048', // Max size 2MB (2048 KB)
        ];
    }
}
