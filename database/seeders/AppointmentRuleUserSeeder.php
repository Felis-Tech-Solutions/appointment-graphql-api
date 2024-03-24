<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SlotRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentRuleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointmentSlotRules = SlotRule::all();

        /* @var User $user */
        $user = User::find(1);

        foreach ($appointmentSlotRules as $appointmentRule) {
            $user->slotRules()->attach($appointmentRule->id);
        }
    }
}
