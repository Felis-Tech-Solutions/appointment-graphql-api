<?php declare(strict_types=1);

namespace App\GraphQL\Validators;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nuwave\Lighthouse\Validation\Validator;

final class UserUpdateInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'id'                       => ['required', 'numeric'],
            'name'                    => ['sometimes', 'required', 'string'],
            'email'                   => ['sometimes', 'required', 'email', Rule::unique(User::class, 'email')
                ->ignore($this->arg('id')),
            ],
            'currentPassword'         => ['required_with:newPassword', 'string', 'current_password:api'],
            'newPassword'             => ['sometimes', 'required', 'string', Password::defaults()],
            'newPasswordConfirmation' => ['required_with:newPassword', 'string', 'same:newPassword'],
        ];
    }
}
