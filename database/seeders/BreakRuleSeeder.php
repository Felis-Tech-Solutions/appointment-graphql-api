<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BreakRule;
use Illuminate\Database\Seeder;

class BreakRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $weekDays = [
            ['dayOfTheWeek' => 'Monday'],
            ['dayOfTheWeek' => 'Tuesday'],
            ['dayOfTheWeek' => 'Wednesday'],
            ['dayOfTheWeek' => 'Thursday'],
            ['dayOfTheWeek' => 'Friday'],
        ];

        foreach ($weekDays as $weekDay) {
            BreakRule::updateOrCreate(
                [
                    'day_of_the_week' => $weekDay['dayOfTheWeek'],
                ],
                [
                    'rule_name'  => 'Lunch Break',
                    'start_time' => '12:00:00',
                    'end_time'   => '13:00:00',
                    'user_id'    => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }
}
