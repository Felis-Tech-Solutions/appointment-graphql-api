<?php declare(strict_types=1);

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class AppointmentStatusUpdateInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'id'   => ['required', 'numeric', 'exists:appointment_statuses,id',],
            'name' => ['nullable', 'string', 'max:255',],
        ];
    }
}
