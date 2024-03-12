<?php declare(strict_types=1);

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class AppointmentTypeUpdateInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array>
     */
    public function rules(): array
    {
        return [
            'id'                                  => ['required', 'numeric',],
            'title'                               => ['string', 'max:255',],
            'description'                         => ['nullable', 'string',],
            'duration'                            => ['nullable', 'numeric',],
            'price'                               => ['nullable', 'numeric',],
            'user.connect'                        => ['sometimes', 'numeric'],
            'appointments.connect'                => ['sometimes', 'array'],
            'appointments.connect.*'              => ['numeric'],
            'appointments.sync'                   => ['sometimes', 'array'],
            'appointments.sync.*'                 => ['numeric'],
            'appointments.syncWithoutDetaching'   => ['sometimes', 'array'],
            'appointments.syncWithoutDetaching.*' => ['numeric'],
            'appointments.disconnect'             => ['sometimes', 'array'],
            'appointments.disconnect.*'           => ['numeric'],
        ];
    }
}
