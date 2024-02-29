<?php

use App\Models\User;
use App\Models\Group;

it('can create a group', function () {
    $this->actingAs(User::factory()->create());

    $testGroupName = 'test group';
    $response      = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation createGroup($input: GroupCreateInput!) {
                createGroup(input: $input) {
                    id
                    name
                    active
                    createdAt
                    updatedAt
                    deletedAt
                }
            }
        ', [
        'input' => [
            'name'   => $testGroupName,
            'active' => true,
        ],
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            'createGroup' => [
                'id',
                'name',
                'active',
                'createdAt',
                'updatedAt',
                'deletedAt',
            ],
        ],
    ]);
    $response->assertJson([
        'data' => [
            'createGroup' => [
                'name'   => $testGroupName,
                'active' => true,
            ],
        ],
    ]);
    $this->assertDatabaseHas('groups', [
        'name'   => $testGroupName,
        'active' => true,
    ]);
});

it('can update a group', function () {
    $this->actingAs(User::factory()->create());

    $group      = Group::factory()->create();
    $updateName = 'test update';

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation updateGroup($input: GroupUpdateInput!) {
                updateGroup(input: $input) {
                    id
                    name
                    active
                    createdAt
                    updatedAt
                    deletedAt
                }
            }
        ', [
        'input' => [
            'id'     => $group->id,
            'name'   => $updateName,
            'active' => false,
        ],
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'updateGroup' => [
                'id'     => (string)$group->id,
                'name'   => $updateName,
                'active' => false,
            ],
        ],
    ]);
    $this->assertDatabaseHas('groups', [
        'id'     => $group->id,
        'name'   => $updateName,
        'active' => false,
    ]);
});

it('can be soft deleted', function () {
    $this->actingAs(User::factory()->create());
    $group = Group::factory()->create();

    $variables = [
        'id' => $group->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation deleteGroup($id: ID!) {
                deleteGroup(id: $id) {
                    id
                    deletedAt
                    __typename
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();
    $this->assertSoftDeleted($group);
    $response->assertJson([
        'data' => [
            'deleteGroup' => [
                'id'         => $group->id,
                'deletedAt'  => ! null,
                '__typename' => 'Group',
            ],
        ],
    ]);
});

it('can be restored', function () {
    $this->actingAs(User::factory()->create());

    $group = Group::factory()->create();

    $group->delete();

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            mutation restoreGroup($id: ID!) {
                restoreGroup(id: $id) {
                    id
                    name
                    deletedAt
                }
            }
        ', [
        'id' => $group->id,
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'restoreGroup' => [
                'id'        => (string)$group->id,
                'name'      => $group->name,
                'deletedAt' => null,
            ],
        ],
    ]);

    $this->assertDatabaseHas('groups', [
        'id'         => $group->id,
        'deleted_at' => null,
    ]);
});
