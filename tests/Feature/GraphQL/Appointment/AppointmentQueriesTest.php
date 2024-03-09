<?php

use App\Models\User;
use App\Models\Appointment;

it('can retrieve single appointment', function () {
    $appointment = Appointment::factory()->create();

    $variables = [
        'id' => $appointment->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query getAppointment($id: ID!) {
                appointment(id: $id) {
                    id
                    title
                    description
                    startDateTime
                    endDateTime
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
            'appointment' => [
                'id'            => (string)$appointment->id,
                'title'         => $appointment->title,
                'description'   => $appointment->description,
                'startDateTime' => $appointment->start_date_time,
                'endDateTime'   => $appointment->end_date_time,
                'user'          => [
                    'id'   => (string)$appointment->user->id,
                    'name' => $appointment->user->name,
                ],
                'attendees'     => $appointment->attendees->map(function ($attendee) {
                    return [
                        'id'   => (string)$attendee->id,
                        'name' => $attendee->name,
                    ];
                })->toArray(),
            ],
        ],
    ]);
});

it('can retrieve several appointments', function () {
    $count        = 10;
    $appointments = Appointment::factory()->count($count)->create();

    $variables = [
        'first' => $count,
        'page'  => 1,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query getAllAppointments{
                allAppointments {
                    id
                    title
                    description
                    startDateTime
                    endDateTime
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
            'allAppointments' => $appointments->map(function ($appointment) {
                return [
                    'id'            => (string)$appointment->id,
                    'title'         => $appointment->title,
                    'description'   => $appointment->description,
                    'startDateTime' => $appointment->start_date_time,
                    'endDateTime'   => $appointment->end_date_time,
                    'user'          => [
                        'id'   => (string)$appointment->user->id,
                        'name' => $appointment->user->name,
                    ],
                    'attendees'     => $appointment->attendees->map(function ($attendee) {
                        return [
                            'id'   => (string)$attendee->id,
                            'name' => $attendee->name,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ],
    ]);
});

it('can retrieve all appointments', function () {
    $count        = 10;
    $appointments = Appointment::factory()->count($count)->create();

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query getAppointments(
                $first: Int!
                $page: Int
                $id: [ID!]

            ) {
                appointments(
                    first: $first
                    page: $page
                    id: $id
                ) {
                    data {
                        id
                        title
                        description
                        startDateTime
                        endDateTime
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
            }
        ', variables: [
        'first' => $count,
        'page'  => 1,
    ]
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'appointments' => [
                'data' => $appointments->map(function ($appointment) {
                    return [
                        'id'            => (string)$appointment->id,
                        'title'         => $appointment->title,
                        'description'   => $appointment->description,
                        'startDateTime' => $appointment->start_date_time,
                        'endDateTime'   => $appointment->end_date_time,
                        'user'          => [
                            'id'   => (string)$appointment->user->id,
                            'name' => $appointment->user->name,
                        ],
                        'attendees'     => $appointment->attendees->map(function ($attendee) {
                            return [
                                'id'   => (string)$attendee->id,
                                'name' => $attendee->name,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ],
        ],
    ]);
});

it('can retrieve a appointments by user', function () {
    $user = User::factory()->create();

    $appointments = Appointment::factory()->count(10)->create([
        'user_id' => $user->id,
    ]);

    $variables = [
        'first'  => 10,
        'page'   => 1,
        'userId' => $user->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query appointments(
                $first: Int!
                $page: Int
                $userId: ID
            ) {
                appointments(
                    first: $first
                    page: $page
                    userId: $userId
                ) {
                    data {
                        id
                        title
                        description
                        startDateTime
                        endDateTime
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
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'appointments' => [
                'data' => $appointments->map(function ($appointment) {
                    return [
                        'id'            => (string)$appointment->id,
                        'title'         => $appointment->title,
                        'description'   => $appointment->description,
                        'startDateTime' => $appointment->start_date_time,
                        'endDateTime'   => $appointment->end_date_time,
                        'user'          => [
                            'id'   => (string)$appointment->user->id,
                            'name' => $appointment->user->name,
                        ],
                        'attendees'     => $appointment->attendees->map(function ($attendee) {
                            return [
                                'id'   => (string)$attendee->id,
                                'name' => $attendee->name,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ],
        ],
    ]);
});

it('can retrieve a appointments by user attendee ', function () {
    $user = User::factory()->create();

    $appointments = Appointment::factory()->count(10)->create();

    $appointments->each(function ($appointment) use ($user) {
        $appointment->attendees()->attach($user->id);
    });

    $variables = [
        'first'      => 10,
        'page'       => 1,
        'attendeeId' => $user->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query appointments(
                $first: Int!
                $page: Int
                $userId: ID
            ) {
                appointments(
                    first: $first
                    page: $page
                    userId: $userId
                ) {
                    data {
                        id
                        title
                        description
                        startDateTime
                        endDateTime
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
            }
        ', variables: $variables
    );

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'appointments' => [
                'data' => $appointments->map(function ($appointment) {
                    return [
                        'id'            => (string)$appointment->id,
                        'title'         => $appointment->title,
                        'description'   => $appointment->description,
                        'startDateTime' => $appointment->start_date_time,
                        'endDateTime'   => $appointment->end_date_time,
                        'user'          => [
                            'id'   => (string)$appointment->user->id,
                            'name' => $appointment->user->name,
                        ],
                        'attendees'     => $appointment->attendees->map(function ($attendee) {
                            return [
                                'id'   => (string)$attendee->id,
                                'name' => $attendee->name,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ],
        ],
    ]);
});
