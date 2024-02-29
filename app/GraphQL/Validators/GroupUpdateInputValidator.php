<?php declare(strict_types=1);

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class GroupUpdateInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name'                          => ['required', 'string'],
            'active'                        => ['required', 'boolean'],
            'groups.connect'                => ['sometimes', 'array'],
            'groups.connect.*'              => ['numeric'],
            'groups.sync'                   => ['sometimes', 'array'],
            'groups.sync.*'                 => ['numeric'],
            'groups.syncWithoutDetaching'   => ['sometimes', 'array'],
            'groups.syncWithoutDetaching.*' => ['numeric'],
            'groups.disconnect'             => ['sometimes', 'array'],
            'groups.disconnect.*'           => ['numeric'],
        ];
    }
}
