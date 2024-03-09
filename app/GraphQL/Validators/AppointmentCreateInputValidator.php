<?php declare(strict_types=1);

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class AppointmentCreateInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'title'                            => ['required', 'string', 'max:255',],
            'description'                      => ['nullable', 'string',],
            'startDateTime'                    => ['required', 'date',],
            'endDateTime'                      => ['required', 'date', 'after:startDateTime',],
            'user.connect'                     => ['required', 'numeric'],
            'attendees.connect'                => ['sometimes', 'array'],
            'attendees.connect.*'              => ['numeric'],
            'attendees.sync'                   => ['sometimes', 'array'],
            'attendees.sync.*'                 => ['numeric'],
            'attendees.syncWithoutDetaching'   => ['sometimes', 'array'],
            'attendees.syncWithoutDetaching.*' => ['numeric'],
            'attendees.disconnect'             => ['sometimes', 'array'],
            'attendees.disconnect.*'           => ['numeric'],
        ];
    }
}
