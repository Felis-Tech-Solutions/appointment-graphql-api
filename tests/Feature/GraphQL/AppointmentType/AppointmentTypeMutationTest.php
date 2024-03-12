<?php

use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentType;

it('can create a appointment type', function () {
    $actingUser = User::factory()->create();

    $this->actingAs($actingUser);

    $variables = [
        'input' => [
            'title'       => 'test appointment type',
            'description' => 'test description',
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation createAppointmentType($input: AppointmentTypeCreateInput!) {
                createAppointmentType(input: $input) {
                    id
                    title
                    description
                    createdAt
                    updatedAt
                }
            }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'createAppointmentType' => [
                'title'       => 'test appointment type',
                'description' => 'test description',
            ],
        ],
    ]);
});

it('can be update a appointment type', function () {
    $actingUser = User::factory()->create();

    $this->actingAs($actingUser);

    $appointmentType = AppointmentType::factory()->create();

    $variables = [
        'input' => [
            'id'          => $appointmentType->id,
            'title'       => 'test appointment type updated',
            'description' => 'test description updated',
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation updateAppointmentType($input: AppointmentTypeUpdateInput!) {
                updateAppointmentType(input: $input) {
                    id
                    title
                    description
                }
            }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'updateAppointmentType' => [
                'id'          => (string)$appointmentType->id,
                'title'       => 'test appointment type updated',
                'description' => 'test description updated',
            ],
        ],
    ]);
});

it('can soft delete a appointment type', function () {
    $actingUser = User::factory()->create();

    $this->actingAs($actingUser);

    $appointmentType = AppointmentType::factory()->create();

    $variables = [
        'id' => $appointmentType->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation deleteAppointmentType($id: ID!) {
                deleteAppointmentType(id: $id){
                    id
                    __typename
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'deleteAppointmentType' => [
                'id'         => (string)$appointmentType->id,
                '__typename' => 'AppointmentType',
            ],
        ],
    ]);

});

it('can restore a appointment type', function () {
    $actingUser = User::factory()->create();

    $this->actingAs($actingUser);

    $appointmentType = AppointmentType::factory()->create();
    $appointmentType->delete();

    $variables = [
        'id' => $appointmentType->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation restoreAppointmentType($id: ID!) {
                restoreAppointmentType(id: $id){
                    id
                    __typename
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'restoreAppointmentType' => [
                'id'         => (string)$appointmentType->id,
                '__typename' => 'AppointmentType',
            ],
        ],
    ]);
});

it('can link appointments to a appointment type', function () {
    $actingUser = User::factory()->create();

    $this->actingAs($actingUser);

    $appointmentType = AppointmentType::factory()->create();
    $appointments    = Appointment::factory(3)->create();

    $variables = [
        'input' => [
            'id'           => $appointmentType->id,
            'title'        => 'test appointment type updated',
            'appointments' => [
                'syncWithoutDetaching' => [
                    (string)$appointments[0]->id,
                    (string)$appointments[1]->id,
                    (string)$appointments[2]->id,
                ],
            ],
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation updateAppointmentType($input: AppointmentTypeUpdateInput!) {
                updateAppointmentType(input: $input) {
                    id
                    title
                    description
                    appointments(first: 10, page: 1) {
                    data {
                        id
                        title
                        description
                    }
                 }
                }
            }
        ', variables: $variables
    );

    $response->assertJson([
        'data' => [
            'updateAppointmentType' => [
                'id'           => (string)$appointmentType->id,
                'title'        => 'test appointment type updated',
                'description'  => $appointmentType->description,
                'appointments' => [
                    'data' => [
                        [
                            'id'    => (string)$appointments[0]->id,
                            'title' => $appointments[0]->title,
                        ],
                        [
                            'id'    => (string)$appointments[1]->id,
                            'title' => $appointments[1]->title,
                        ],
                        [
                            'id'    => (string)$appointments[2]->id,
                            'title' => $appointments[2]->title,
                        ],
                    ],
                ],
            ],
        ],
    ]);
});
