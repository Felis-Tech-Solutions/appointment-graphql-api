<?php

namespace Database\Seeders;

use App\Models\SlotRule;
use Illuminate\Database\Seeder;

class AppointmentRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weekDays = [
            ['day' => 'monday', 'dayOfTheWeek' => 'Monday'],
            ['day' => 'tuesday', 'dayOfTheWeek' => 'Tuesday'],
            ['day' => 'wednesday', 'dayOfTheWeek' => 'Wednesday'],
            ['day' => 'thursday', 'dayOfTheWeek' => 'Thursday'],
            ['day' => 'friday', 'dayOfTheWeek' => 'Friday'],
        ];

        foreach ($weekDays as $weekDay) {
            SlotRule::updateOrCreate([
                'rule_name'              => ucfirst($weekDay['day']),
                'day_of_the_week'        => $weekDay['dayOfTheWeek'],
                'start_time'             => '08:30:00',
                'end_time'               => '17:00:00',
                'slot_duration'          => 30 * 60 * 1000,
                'time_after_appointment' => 0,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }
}
