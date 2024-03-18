<?php

use App\Models\User;
use App\Models\AppointmentStatus;

it('can create an appointment status', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $variables = [
        'input' => [
            'name' => 'test appointment status',
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        mutation createAppointmentStatus($input: AppointmentStatusCreateInput!) {
            createAppointmentStatus(input: $input) {
                id
                name
                createdAt
                updatedAt
            }
        }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'createAppointmentStatus' => [
                'name' => 'test appointment status',
            ],
        ],
    ]);
});


it('can update a appointment type', function () {
    $appointmentStatus = AppointmentStatus::factory()->create();
    $user              = User::factory()->create();

    $this->actingAs($user);

    $updateName = 'test update';

    $variables = [
        'input' => [
            'id'   => $appointmentStatus->getKey(),
            'name' => $updateName,
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        mutation updateAppointmentStatus($input: AppointmentStatusUpdateInput!) {
            updateAppointmentStatus(input: $input) {
                id
                name
                createdAt
                updatedAt
            }
        }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'updateAppointmentStatus' => [
                'name' => $updateName,
            ],
        ],
    ]);
});

it('can delete a appointment status', function () {
    $appointmentStatus = AppointmentStatus::factory()->create();

    $user = User::factory()->create();

    $this->actingAs($user);

    $variables = [
        'id' => $appointmentStatus->getKey(),
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
        mutation deleteAppointmentStatus($id: ID!) {
            deleteAppointmentStatus(id: $id) {
                __typename
            }
        }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'deleteAppointmentStatus' => [
                '__typename' => 'AppointmentStatus',
            ],
        ],
    ]);
});
