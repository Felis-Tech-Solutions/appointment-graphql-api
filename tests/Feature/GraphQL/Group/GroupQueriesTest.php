<?php

use App\Models\User;
use App\Models\Group;

it('can retrieve single group', function () {
    $group = Group::factory()->create();

    $variables = [
        'id' => $group->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query getGroups($id: ID!) {
                group(id: $id) {
                    id
                    name
                    active
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'group' => [
                'id'     => (string)$group->id,
                'name'   => $group->name,
                'active' => $group->active,
            ],
        ],
    ]);
});

it('can retrieve several groups', function () {
    $count  = 10;
    $groups = Group::factory()->count($count)->create();

    $variables = [
        'first' => $count,
        'page'  => 1,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query getGroups(
                $first: Int!
                $page: Int
                $id: [ID!]
                $name: String
                $active: Boolean
            ) {
                groups(
                    first: $first
                    page: $page
                    id: $id
                    name: $name
                    active: $active
                ) {
                    data {
                        id
                        name
                        active
                    }
                }
            }
          ', variables: $variables
    );

    $response->assertSuccessful();
    $data = $response->json('data.groups.data');
    foreach ($groups as $index => $group) {
        $this->assertEquals($group->id, $data[$index]['id']);
        $this->assertEquals($group->name, $data[$index]['name']);
        $this->assertEquals($group->active, $data[$index]['active']);

    }
});

it('can get a user by name', function () {
    $groupName = "Test group name";

    $group = Group::factory([
        'name' => 'Test group name',
    ])->create();

    $variables = [
        'first' => 1,
        'page'  => 1,
        'name'  => $groupName,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
           query getGroups(
                $first: Int!
                $page: Int
                $id: [ID!]
                $name: String
                $active: Boolean
            ) {
                groups(
                    first: $first
                    page: $page
                    id: $id
                    name: $name
                    active: $active
                ) {
                    data {
                        id
                        name
                        active
                    }
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'groups' => [
                'data' => [
                    [
                        'id'     => (string)$group->id,
                        'name'   => $groupName,
                        'active' => $group->active,
                    ],
                ],
            ],
        ],
    ]);

});

it('can retrieve all groups', function () {
    $count  = 10;
    $groups = Group::factory()->count($count)->create();

    $variables = [
        'first' => $count,
        'page'  => 1,
    ];

    $this->actingAs(User::factory()->create());
    $response = $this->graphQL(
    /** @lang GraphQL */
        '
           query getGroups {
                allGroups {
                    id
                    name
                    active
                    users {
                        id
                        name
                        email
                        emailVerifiedAt
                        createdAt
                        updatedAt
                    }
                }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();

    $response->assertJson([
        'data' => [
            'allGroups' => $groups->map(function ($group) {
                return [
                    'id'     => (string)$group->id,
                    'name'   => $group->name,
                    'active' => $group->active,
                    'users'  => $group->users->map(function ($user) {
                        return [
                            'id'              => (string)$user->id,
                            'name'            => $user->name,
                            'email'           => $user->email,
                            'emailVerifiedAt' => $user->email_verified_at,
                            'createdAt'       => $user->created_at,
                            'updatedAt'       => $user->updated_at,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ],
    ]);
});
