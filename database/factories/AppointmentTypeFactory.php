<?php

namespace Database\Factories;

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppointmentType>
 */
class AppointmentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => $this->faker->word,
            'description' => $this->faker->sentence,
            'duration'    => $this->faker->numberBetween(5 * 60000, 360 * 60000),
            'price'       => $this->faker->numberBetween(1000, 100000),
        ];
    }
}
