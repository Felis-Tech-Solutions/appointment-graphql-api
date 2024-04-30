<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use App\Models\AppointmentStatus;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
            'email' => 'contact@felis-ts.nl',
        ], [
            'name'     => 'Felis TS',
            'password' => Hash::make('password'),
        ]);

        $user->email_verified_at = now();

        Group::factory(10)->create()->each(function ($group) use ($user) {
            $group->users()->attach($user);
        });

        $this->call([
            AppointmentStatusSeeder::class,
        ]);

        Appointment::factory(10)->create()->each(function ($appointment) use ($user) {
            $appointment->attendees()->attach($user);
        })->each(function ($appointment) {
            $appointment->status()->associate(AppointmentStatus::query()->first());
        });
    }
}
