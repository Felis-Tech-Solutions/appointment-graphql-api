<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Appointment;

it('can create an appointment', function () {
    $actingUser    = User::factory()->create();
    $relatedUser   = User::factory()->create();
    $attendeeUsers = User::factory()->count(3)->create();

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
                'connect' => $relatedUser->id,
            ],
            'attendees'     => [
                'syncWithoutDetaching' => [
                    $attendeeUsers[0]->id,
                    $attendeeUsers[1]->id,
                    $attendeeUsers[2]->id,
                ],
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
            ],
        ],
    ]);

    $this->assertDatabaseHas('appointments', [
        'title'           => 'test appointment',
        'description'     => 'test description',
        'start_date_time' => Carbon::now()->toDateTimeString(),
        'end_date_time'   => Carbon::now()->addHour()->toDateTimeString(),
        'user_id'         => $relatedUser->id,
    ]);
});

it('can update a appointment', function () {
    $actingUser    = User::factory()->create();
    $attendeeUsers = User::factory()->count(3)->create();

    $appointment = Appointment::factory()->create();

    $appointment->attendees()->syncWithoutDetaching([
        $attendeeUsers[0]->id,
        $attendeeUsers[1]->id,
        $attendeeUsers[2]->id,
    ]);

    $this->actingAs($actingUser);

    $variables = [
        'input' => [
            'id'    => $appointment->id,
            'title' => 'updated test appointment',
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
                'id'            => (string)$appointment->id,
                'title'         => 'updated test appointment',
                'description'   => $appointment->description,
                'startDateTime' => $appointment->start_date_time,
                'endDateTime'   => $appointment->end_date_time,
                'user'          => [
                    'id'   => (string)$appointment->user->id,
                    'name' => $appointment->user->name,
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
            ],
        ],
    ]);

    $this->assertDatabaseHas('appointments', [
        'id'    => $appointment->id,
        'title' => 'updated test appointment',
    ]);
});

it('can be deleted', function () {
    $actingUser = User::factory()->create();
    $appointment = Appointment::factory()->create();

    $this->actingAs($actingUser);

    $variables = [
        'id' => $appointment->id,
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
        'id' => $appointment->id,
    ]);
});
