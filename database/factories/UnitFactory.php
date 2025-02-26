<?php

namespace Database\Factories;

use App\Models\MedicalFacility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'facility_id' => fake()->numberBetween(1, MedicalFacility::count()),
            'name' => fake()->name(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['Active', 'Inactive']),
        ];
    }
}
