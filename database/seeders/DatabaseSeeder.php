<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use App\Models\AppointmentStatus;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Group::factory(10)->create()->each(function ($group) {
            $group->users()->attach(User::factory(3)->create());
        });

        $this->call([
            AppointmentStatusSeeder::class,
        ]);

        Appointment::factory(10)->create()->each(function ($appointment) {
            $appointment->attendees()->attach(User::factory(3)->create());
        })->each(function ($appointment) {
            $appointment->status()->associate(AppointmentStatus::query()->first());
        });
    }
}
