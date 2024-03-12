<?php declare(strict_types=1);

namespace App\GraphQL\Validators;

use App\Models\User;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class GroupCreateInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name'                 => ['required', 'string'],
            'active'               => ['required', 'boolean'],
        ];
    }
}
