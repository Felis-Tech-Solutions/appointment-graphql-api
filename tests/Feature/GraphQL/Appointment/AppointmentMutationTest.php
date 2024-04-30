<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentStatus;

it('can create an appointment', function () {
    $actingUser    = User::factory()->create();
    $relatedUser   = User::factory()->create();
    $attendeeUsers = User::factory()->count(3)->create();
    $status        = AppointmentStatus::factory()->create();

    $startDateTime = Carbon::now()->toDateTimeString();
    $endDateTime   = Carbon::now()->addHour()->toDateTimeString();

    $this->actingAs($actingUser);

    $variables = [
        'input' => [
            'title'         => 'test appointment',
            'description'   => 'test description',
            'startDateTime' => $startDateTime,
            'endDateTime'   => $endDateTime,
            'user'          => [
                'connect' => $relatedUser->getKey(),
            ],
            'attendees'     => [
                'syncWithoutDetaching' => [
                    $attendeeUsers[0]->id,
                    $attendeeUsers[1]->id,
                    $attendeeUsers[2]->id,
                ],
            ],
            'status'        => [
                'connect' => $status->getKey(),
            ],
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation createAppointment($input: AppointmentCreateInput!) {
                createAppointment(input: $input) {
                    id
                    title
                    description
                    startDateTime
                    endDateTime
                    createdAt
                    updatedAt
                    user {
                        id
                        name
                    }
                    attendees {
                        id
                        name
                    }
                    status {
                        id
                        name
                    }
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'createAppointment' => [
                'title'         => 'test appointment',
                'description'   => 'test description',
                'startDateTime' => $startDateTime,
                'endDateTime'   => $endDateTime,
                'user'          => [
                    'id'   => $relatedUser->id,
                    'name' => $relatedUser->name,
                ],
                'attendees'     => [
                    [
                        'id'   => (string)$attendeeUsers[0]->id,
                        'name' => $attendeeUsers[0]->name,
                    ],
                    [
                        'id'   => (string)$attendeeUsers[1]->id,
                        'name' => $attendeeUsers[1]->name,
                    ],
                    [
                        'id'   => (string)$attendeeUsers[2]->id,
                        'name' => $attendeeUsers[2]->name,
                    ],
                ],
                'status'        => [
                    'id'   => (string)$status->id,
                    'name' => $status->name,
                ],
            ],
        ],
    ]);

    $this->assertDatabaseHas('appointments', [
        'title'           => 'test appointment',
        'description'     => 'test description',
        'start_date_time' => $startDateTime,
        'end_date_time'   => $endDateTime,
        'user_id'         => $relatedUser->id,
    ]);
});

it('can update a appointment', function () {
    $actingUser    = User::factory()->create();
    $attendeeUsers = User::factory()->count(3)->create();
    $newUser       = User::factory()->create();

    $appointment = Appointment::factory()->create();

    $appointment->attendees()->syncWithoutDetaching([
        $attendeeUsers[0]->id,
        $attendeeUsers[1]->id,
        $attendeeUsers[2]->id,
    ]);

    $this->actingAs($actingUser);

    $variables = [
        'input' => [
            'id'    => $appointment->getKey(),
            'title' => 'updated test appointment',
            'user'  => [
                'connect' => $newUser->getKey(),
            ],
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation updateAppointment($input: AppointmentUpdateInput!) {
                updateAppointment(input: $input) {
                    id
                    title
                    description
                    startDateTime
                    endDateTime
                    createdAt
                    updatedAt
                    user {
                        id
                        name
                    }
                    attendees {
                        id
                        name
                    }
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'updateAppointment' => [
                'id'            => (string)$appointment->getKey(),
                'title'         => 'updated test appointment',
                'description'   => $appointment->description,
                'startDateTime' => $appointment->start_date_time,
                'endDateTime'   => $appointment->end_date_time,
                'user'          => [
                    'id'   => (string)$newUser->getKey(),
                    'name' => $newUser->name,
                ],
                'attendees'     => [
                    [
                        'id'   => (string)$attendeeUsers[0]->getKey(),
                        'name' => $attendeeUsers[0]->name,
                    ],
                    [
                        'id'   => (string)$attendeeUsers[1]->getKey(),
                        'name' => $attendeeUsers[1]->name,
                    ],
                    [
                        'id'   => (string)$attendeeUsers[2]->getKey(),
                        'name' => $attendeeUsers[2]->name,
                    ],
                ],
            ],
        ],
    ]);

    $this->assertDatabaseHas('appointments', [
        'id'    => $appointment->getKey(),
        'title' => 'updated test appointment',
    ]);
});

it('can be deleted', function () {
    $actingUser  = User::factory()->create();
    $appointment = Appointment::factory()->create();

    $this->actingAs($actingUser);

    $variables = [
        'id' => $appointment->getKey(),
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation deleteAppointment($id: ID!) {
                deleteAppointment(id: $id){
                    id
                    __typename
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'deleteAppointment' => [
                'id'         => (string)$appointment->id,
                '__typename' => 'Appointment',
            ],
        ],
    ]);

    $this->assertDatabaseMissing('appointments', [
        'id' => $appointment->getKey(),
    ]);
});

it('can link a appointment to a status', function () {
    $actingUser  = User::factory()->create();
    $this->actingAs($actingUser);

    $appointmentStatus = AppointmentStatus::factory()->create();
    $appointment       = Appointment::factory()->create();

    $variables = [
        'input' => [
            'id'     => $appointment->getKey(),
            'status' => [
                'connect' => $appointmentStatus->getKey(),
            ],
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation updateAppointment($input: AppointmentUpdateInput!) {
                updateAppointment(input: $input) {
                    id
                    title
                    status {
                        id
                        name
                    }
                }
            }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'updateAppointment' => [
                'id'     => (string)$appointment->getKey(),
                'title'  => $appointment->title,
                'status' => [
                    'id'   => (string)$appointmentStatus->getKey(),
                    'name' => $appointmentStatus->name,
                ],
            ],
        ],
    ]);
});

it('can unlink appointments to a status', function () {
    $actingUser  = User::factory()->create();
    $this->actingAs($actingUser);

    $appointmentStatus = AppointmentStatus::factory()->create();
    $appointment       = Appointment::factory()->create();

    $appointment->status()->associate($appointmentStatus);
    $appointment->save();

    $variables = [
        'input' => [
            'id'     => $appointment->getKey(),
            'status' => [
                'disconnect' => true,
            ],
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation updateAppointment($input: AppointmentUpdateInput!) {
                updateAppointment(input: $input) {
                    id
                    title
                    status {
                        id
                        name
                    }
                }
            }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'updateAppointment' => [
                'id'     => (string)$appointment->getKey(),
                'title'  => $appointment->title,
                'status' => null,
            ],
        ],
    ]);

});

