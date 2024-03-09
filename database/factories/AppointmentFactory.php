<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'           => $this->faker->sentence,
            'description'     => $this->faker->paragraph,
            'start_date_time' => $this->faker->dateTimeThisMonth->format('Y-m-d H:i:s'),
            'end_date_time'   => $this->faker->dateTimeThisMonth->format('Y-m-d H:i:s'),
            'user_id'         => User::factory()->create()->getKey(),
        ];
    }
}
