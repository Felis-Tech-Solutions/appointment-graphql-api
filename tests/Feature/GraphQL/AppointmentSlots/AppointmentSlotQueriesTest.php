<?php

use App\Models\User;
use App\Models\SlotRule;

it('can fetch appointment slots per week', function () {
    $this->actingAs(User::factory()->create());
    $this->seed('AppointmentRuleSeeder');

    $appointmentSlotRules = SlotRule::all();

    $user = User::factory()->create();

    foreach ($appointmentSlotRules as $appointmentRule) {
        $user->slotRules()->attach($appointmentRule->id);
    }

    $variables = [
        'user_id'        => $user->getKey(),
        "reference_date" => "2024-4-26",
    ];

    $response = $this->graphQL('
       query getAvailableSlots($user_id: ID!, $reference_date: String!) {
          getAvailableSlots(user_id: $user_id, reference_date: $reference_date) {
            day
            date
            slots {
              startTime
              endTime
            }
          }
        }
    ', variables: $variables);

    $response->assertSuccessful();

    $response->assertJsonStructure([
        'data' => [
            'getAvailableSlots' => [
                '*' => [
                    'day',
                    'date',
                    'slots' => [
                        '*' => [
                            'startTime',
                            'endTime',
                        ],
                    ],
                ],
            ],
        ],
    ]);
});
