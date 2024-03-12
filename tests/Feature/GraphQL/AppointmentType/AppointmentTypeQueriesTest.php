<?php

use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentType;

it('can retrieve a single appointment type', function () {
    $user            = User::factory()->create();
    $appointmentType = AppointmentType::factory()->create();

    $this->actingAs($user);

    $variables = [
        'id' => $appointmentType->getKey(),
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        query ($id: ID!) {
            appointmentType(id: $id) {
                id
                title
                description
            }
        }
    ', $variables);

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'appointmentType' => [
                'id'          => (string)$appointmentType->id,
                'title'       => $appointmentType->title,
                'description' => $appointmentType->description,
            ],
        ],
    ]);
});

it('can retrieve several appointment types', function () {
    $count            = 10;
    $user             = User::factory()->create();
    $appointmentTypes = AppointmentType::factory()->count($count)->create();

    $this->actingAs($user);

    $variables = [
        'first' => $count,
        'page'  => 1,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        query appointmentTypes(
            $first: Int!
            $page: Int
            $id: [ID!]
        ) {
            appointmentTypes(
                first: $first
                page: $page
                id: $id
            ) {
                data {
                    title
                    description
                    duration
                    price
                    user {
                           id
                           name
                       }
                }
             }
        }
    ', $variables);

    $response->assertSuccessful();

    $response->assertJsonCount($count, 'data.appointmentTypes.data');
    $response->assertJson([
        'data' => [
            'appointmentTypes' => [
                'data' => $appointmentTypes->map(function (AppointmentType $appointmentType) {
                    return [
                        'title'       => $appointmentType->title,
                        'description' => $appointmentType->description,
                        'duration'    => $appointmentType->duration,
                        'price'       => $appointmentType->price,
                    ];
                })->all(),
            ],
        ],
    ]);

});

it('can get all appointment types', function () {
    $user             = User::factory()->create();
    $appointmentTypes = AppointmentType::factory()->count(10)->create();

    $this->actingAs($user);

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        query allAppointmentTypes {
            allAppointmentTypes {
                    title
                    description
                    duration
                    price
                    user {
                        id
                        name
                    }
            }
        }
    ');

    $response->assertSuccessful();
    $response->assertJsonCount(10, 'data.allAppointmentTypes');
    $response->assertJson([
        'data' => [
            'allAppointmentTypes' => $appointmentTypes->map(function (AppointmentType $appointmentType) {
                return [
                    'title'       => $appointmentType->title,
                    'description' => $appointmentType->description,
                    'duration'    => $appointmentType->duration,
                    'price'       => $appointmentType->price,
                    'user'        => null,
                ];
            })->all(),
        ],
    ]);
});

it('can get all appointment types with users', function () {
    $user             = User::factory()->create();
    $appointmentTypes = AppointmentType::factory()->count(10)->create();

    $appointmentTypes->each(function (AppointmentType $appointmentType) use ($user) {
        $appointmentType->user()->associate($user);
        $appointmentType->save();
    });

    $this->actingAs($user);

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        query allAppointmentTypes {
            allAppointmentTypes {
                    title
                    description
                    duration
                    price
                    user {
                        id
                        name
                    }
            }
        }
    ');

    $response->assertSuccessful();
    $response->assertJsonCount(10, 'data.allAppointmentTypes');
    $response->assertJson([
        'data' => [
            'allAppointmentTypes' => $appointmentTypes->map(function (AppointmentType $appointmentType) {
                return [
                    'title'       => $appointmentType->title,
                    'description' => $appointmentType->description,
                    'duration'    => $appointmentType->duration,
                    'price'       => $appointmentType->price,
                    'user'        => [
                        'id'   => (string)$appointmentType->user->id,
                        'name' => $appointmentType->user->name,
                    ],
                ];
            })->all(),
        ],
    ]);
});

it('can get appointment type by name with user and appointments', function () {
    $user              = User::factory()->create();
    $appointmentsCount = 10;
    $appointments      = Appointment::factory()->count($appointmentsCount)->create();
    $appointmentType   = AppointmentType::factory()->create();

    $appointmentType->user()->associate($user);

    $appointmentType->appointments()->attach($appointments->pluck('id'));
    $appointmentType->save();

    $this->actingAs($user);

    $variables = [
        'title' => $appointmentType->title,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        query ($title: String) {
            appointmentType(title: $title) {
                id
                title
                description
                user {
                    id
                    name
                }
                appointments(first: 10, page: 1) {
                    data {
                        id
                        title
                        description
                    }
                }
            }
        }
    ', $variables);

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'appointmentType' => [
                'id'           => (string)$appointmentType->id,
                'title'        => $appointmentType->title,
                'description'  => $appointmentType->description,
                'user'         => [
                    'id'   => (string)$appointmentType->user->id,
                    'name' => $appointmentType->user->name,
                ],
                'appointments' => [
                    'data' => $appointments->map(function (Appointment $appointment) {
                        return [
                            'id'          => (string)$appointment->id,
                            'title'       => $appointment->title,
                            'description' => $appointment->description,
                        ];
                    })->toArray(),
                ],
            ],
        ],
    ]);
});

it('can get appointment type by name with user and appointments with pagination', function () {
    $user              = User::factory()->create();
    $appointmentsCount = 10;
    $appointments      = Appointment::factory()->count($appointmentsCount)->create();
    $appointmentType   = AppointmentType::factory()->create();

    $appointmentType->user()->associate($user);

    $appointmentType->appointments()->attach($appointments->pluck('id'));
    $appointmentType->save();

    $this->actingAs($user);

    $variables = [
        'title' => $appointmentType->title,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        query ($title: String) {
            appointmentType(title: $title) {
                id
                title
                description
                price
                user {
                    id
                    name
                }
                appointments(first: 5, page: 1) {
                    data {
                        id
                        title
                        description
                    }
                     paginatorInfo {
                        total
                        currentPage
                        lastPage
                      }
                }
            }
        }
    ', $variables);

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'appointmentType' => [
                'id'           => (string)$appointmentType->id,
                'title'        => $appointmentType->title,
                'description'  => $appointmentType->description,
                'user'         => [
                    'id'   => (string)$appointmentType->user->id,
                    'name' => $appointmentType->user->name,
                ],
                'appointments' => [
                    'data'          => $appointments->take(5)->map(function (Appointment $appointment) {
                        return [
                            'id'          => (string)$appointment->id,
                            'title'       => $appointment->title,
                            'description' => $appointment->description,
                        ];
                    })->toArray(),
                    'paginatorInfo' => [
                        'total'       => $appointmentsCount,
                        'currentPage' => 1,
                        'lastPage'    => 2,
                    ],
                ],
            ],
        ],
    ]);
});
