<?php

use App\Models\User;

it('can create a user', function () {
    $this->actingAs(User::factory()->create());

    $variables = [
        'input' => [
            'name'     => 'test name',
            'email'    => 'test@test.nl',
            'password' => 'securepassword123',
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation createUser($input: UserCreateInput!) {
                createUser(input: $input) {
                    id
                    name
                    email
                    emailVerifiedAt
                    createdAt
                    updatedAt
                    deletedAt
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            'createUser' => [
                'id',
                'name',
                'email',
                'emailVerifiedAt',
                'createdAt',
                'updatedAt',
                'deletedAt',
            ],
        ],
    ]);
});

it('can update a user', function () {
    $this->actingAs(User::factory()->create());
    $user       = User::factory()->create();
    $updateName = 'test update';

    $variables = [
        'input' => [
            'id'   => $user->id,
            'name' => $updateName,
        ],
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation updateUser($input: UserUpdateInput!) {
                updateUser(input: $input) {
                    id
                    name
                    email
                    emailVerifiedAt
                    createdAt
                    updatedAt
                    deletedAt
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'updateUser' => [
                'id'   => $user->id,
                'name' => $updateName,
            ],
        ],
    ]);
});

it('can be soft deleted', function () {
    $this->actingAs(User::factory()->create());
    $user = User::factory()->create();

    $variables = [
        'id' => $user->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation deleteUser($id: ID!) {
                deleteUser(id: $id) {
                    id
                    deletedAt
                     __typename
                }
            }
        ', variables: $variables
    );

    $response->assertStatus(200);
    $this->assertSoftDeleted($user);
    $response->assertJson([
        'data' => [
            'deleteUser' => [
                'id'         => (string)$user->id,
                'deletedAt'  => ! null,
                '__typename' => 'User',
            ],
        ],
    ]);
});

it('can be restored', function () {
    $this->actingAs(User::factory()->create());
    $user = User::factory()->create();

    $user->delete();

    $variables = [
        'id' => $user->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation restoreUser($id: ID!) {
                restoreUser(id: $id) {
                    id
                     __typename
                }
            }
        ', variables: $variables
    );

    $response->assertStatus(200);
    $this->assertDatabaseHas('users', [
        'id'         => $user->id,
        'deleted_at' => null,
    ]);
});
