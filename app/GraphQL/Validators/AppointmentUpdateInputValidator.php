<?php declare(strict_types=1);

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class AppointmentUpdateInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'id'                => ['required', 'exists:appointments,id',],
            'title'             => ['string', 'max:255',],
            'description'       => ['nullable', 'string',],
            'startDateTime'     => ['date', 'after:now',],
            'endDateTime'       => ['date', 'after:start_date_time',],
            'user.connect'      => ['nullable', 'numeric'],
            'attendees'         => ['nullable', 'array', 'exists:users,id',],
            'status.connect'    => ['nullable', 'numeric'],
            'status.disconnect' => ['nullable', 'boolean'],
        ];
    }
}
