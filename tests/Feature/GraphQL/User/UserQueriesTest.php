<?php

use App\Models\User;

it('can retrieve single users', closure: function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $variables = [
        'id' => $user->id,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query getUsers($id: ID!) {
                user(id: $id) {
                    id
                    name
                    email
                    emailVerifiedAt
                    createdAt
                    updatedAt
                    deletedAt
                     groups {
                        name
                        active
                    }
                    slotRules{
                        id
                        ruleName
                    }
                  }
            }
        ', variables: $variables
    );

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'user' => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'emailVerifiedAt' => $user->email_verified_at,
                'createdAt'       => $user->created_at,
                'updatedAt'       => $user->updated_at,
                'deletedAt'       => $user->deleted_at,
                'groups'          => [],
                'slotRules'       => []
            ],
        ],
    ]);
});

it('can retrieve several users', function () {
    $count = 10;
    $users = User::factory()->count($count)->create();

    $variables = [
        'first' => $count,
        'page'  => 1,
    ];

    $this->actingAs($users[0]);
    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query users(
                $first: Int!
                $page: Int
                $id: [ID!]
                $email: [Email!]
                $name: String
                $emailVerified: Boolean
                $orderBy: [QueryUsersOrderByOrderByClause!]
            ) {
                users(
                    first: $first
                    page: $page
                    id: $id
                    email: $email
                    name: $name
                    emailVerified: $emailVerified
                    orderBy: $orderBy
                ) {
                    data {
                        id
                        name
                        email
                        emailVerifiedAt
                        createdAt
                        updatedAt
                        deletedAt
                    }
                }
            }
       ', variables: $variables
    );

    $response->assertSuccessful();
    $data = $response->json('data.users.data');

    foreach ($users as $index => $user) {
        $this->assertEquals($user->id, $data[$index]['id']);
        $this->assertEquals($user->name, $data[$index]['name']);
        $this->assertEquals($user->email, $data[$index]['email']);
        $this->assertEquals($user->email_verified_at, $data[$index]['emailVerifiedAt']);
        $this->assertEquals($user->created_at, $data[$index]['createdAt']);
        $this->assertEquals($user->updated_at, $data[$index]['updatedAt']);
        $this->assertEquals($user->deleted_at, $data[$index]['deletedAt']);
    }
});

it('can get a user by name', function () {
    $testName = "Test user name";

    $user = User::factory([
        'name' => $testName,
    ])->create();

    $this->actingAs($user);

    $variables = [
        'first' => 1,
        'page'  => 1,
        'name'  => $user->name,
    ];

    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query users(
                $first: Int!
                $page: Int
                $id: [ID!]
                $email: [Email!]
                $name: String
                $emailVerified: Boolean
                $orderBy: [QueryUsersOrderByOrderByClause!]
            ) {
                users(
                    first: $first
                    page: $page
                    id: $id
                    email: $email
                    name: $name
                    emailVerified: $emailVerified
                    orderBy: $orderBy
                ) {
                    data {
                        id
                        name
                        email
                        emailVerifiedAt
                        createdAt
                        updatedAt
                        deletedAt
                    }
                }
            }
       ', variables: $variables
    );

    $response->assertSuccessful();
    $response->assertJson([
        'data' => [
            'users' => [
                'data' => [
                    [
                        'id'              => $user->id,
                        'name'            => $testName,
                        'email'           => $user->email,
                        'emailVerifiedAt' => $user->email_verified_at,
                        'createdAt'       => $user->created_at,
                        'updatedAt'       => $user->updated_at,
                        'deletedAt'       => $user->deleted_at,
                    ],
                ],

            ],
        ],
    ]);
});

it('can get all users ', function () {
    $count = 10;
    $users = User::factory()->count($count)->create();

    $this->actingAs($users[0]);
    $response = $this->graphQL(
    /** @lang GraphQL */
        '
            query allUsers
            {
                allUsers {
                    id
                    name
                    email
                    emailVerifiedAt
                    createdAt
                    updatedAt
                    deletedAt
                }
            }
        ');

    $response->assertSuccessful();
    $data = $response->json('data.allUsers');

    foreach ($users as $index => $user) {
        $this->assertEquals($user->id, $data[$index]['id']);
        $this->assertEquals($user->name, $data[$index]['name']);
        $this->assertEquals($user->email, $data[$index]['email']);
        $this->assertEquals($user->email_verified_at, $data[$index]['emailVerifiedAt']);
        $this->assertEquals($user->created_at, $data[$index]['createdAt']);
        $this->assertEquals($user->updated_at, $data[$index]['updatedAt']);
        $this->assertEquals($user->deleted_at, $data[$index]['deletedAt']);
    }
});

