<?php

use App\Models\Appointment;
use App\Models\AppointmentStatus;

it('can retrieve a single appointment status', function () {
    $appointmentStatus = AppointmentStatus::factory()->create();

    $response = $this->graphQL(
    /** @lang GraphQL */ '
        query ($id: ID!) {
            appointmentStatus(id: $id) {
                id
                name
                createdAt
                updatedAt
            }
        }
    ', [
        'id' => $appointmentStatus->id,
    ]);

    $response->assertSuccessful()->assertJson([
        'data' => [
            'appointmentStatus' => [
                'id'   => $appointmentStatus->id,
                'name' => $appointmentStatus->name,
            ],
        ],
    ]);
});

it('can retrieve several appointment statuses', function () {
    $appointmentStatus = AppointmentStatus::factory()->count(10)->create();

    $variables = [
        'first' => 10,
        'page'  => 1,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */ '
        query ($first: Int!, $page: Int) {
            appointmentStatuses(first: $first, page: $page) {
                data {
                    id
                    name
                }
            }
        }
    ', variables: $variables);

    $response->assertSuccessful()->assertJson([
        'data' => [
            'appointmentStatuses' => [
                'data' => $appointmentStatus->map(function ($status) {
                    return [
                        'id'   => (string)$status->id,
                        'name' => $status->name,
                    ];
                })->toArray(),
            ],
        ],
    ]);
});

it('can retrieve all appointment statuses', function () {
    $appointmentStatuses = AppointmentStatus::factory()->count(5)->create();

    $response = $this->graphQL(
    /** @lang GraphQL */ '
        query {
            allAppointmentStatuses {
                id
                name
            }
        }
    ');

    $response->assertSuccessful()->assertJson([
        'data' => [
            'allAppointmentStatuses' => $appointmentStatuses->map(function ($status) {
                return [
                    'id'   => (string)$status->id,
                    'name' => $status->name,
                ];
            })->toArray(),
        ],
    ]);
});

it('can retrieve an appointment status by name with appointments linked', function () {
    $appointmentStatus = AppointmentStatus::factory()->create();
    $appointmentStatus->appointments()->createMany(
        Appointment::factory()->count(5)->make()->toArray()
    );

    $variables = [
        'first' => 10,
        'page'  => 1,
        'name'  => $appointmentStatus->name,
    ];
    $response  = $this->graphQL(
    /** @lang GraphQL */ '
        query (
           $first: Int!
            $page: Int
            $name: String!
            ) {
            appointmentStatuses(
                name: $name
                first: $first
                page: $page
            ) {
                data {
                    id
                    name
                    appointments {
                        id
                        statusId
                    }
                }
            }
        }
    ', variables: $variables
    );

    $response->assertSuccessful()->assertJson([
        'data' => [
            'appointmentStatuses' => [
                'data' => [
                    [
                        'id'           => (string)$appointmentStatus->id,
                        'name'         => $appointmentStatus->name,
                        'appointments' => $appointmentStatus->appointments->map(fn ($appointment) => [
                            'id'       => (string)$appointment->id,
                            'statusId' => (string)$appointment->status_id,
                        ])->all(),
                    ],
                ],
            ],
        ],
    ]);
});
