<?php

namespace Database\Factories;

use App\Models\Thermostat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThermostatFactory extends Factory
{
    protected $model = Thermostat::class;

    public function definition(): array
    {
        return [
            'online' => $this->faker->boolean,
            'mode' => $this->faker->randomElement([
                'off',
                'heat',
                'cool',
            ]),
            'current_temperature' => $this->faker->numberBetween(0, 60),
            'expected_temperature' => $this->faker->numberBetween(15, 30),
            'humidity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
