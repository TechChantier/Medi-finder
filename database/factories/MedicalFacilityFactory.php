<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MedicalFacility;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalFacility>
 */
class MedicalFacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a user with user_type = "medical_facility"
        $user = User::factory()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'whatsapp_number' => $this->faker->numerify('##########'),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type' => 'medical_facility',
        ]);

        return [
            'user_id' => $user->id,
            'address' => $this->faker->address(),
            'category' => fake()->randomElement(['Health Care', 'Pharmacy', 'Clinic']),
            'emergency_contact' => $this->faker->numerify('##########'),
            'google_map_url' => 'https://maps.google.com/?q=' . $this->faker->latitude() . ',' . $this->faker->longitude(),
            'description' => $this->faker->paragraph(3),
            'operating_hours' => json_encode([
                'monday' => ['09:00 AM - 05:00 PM'],
                'tuesday' => ['09:00 AM - 05:00 PM'],
                'wednesday' => ['09:00 AM - 05:00 PM'],
                'thursday' => ['09:00 AM - 05:00 PM'],
                'friday' => ['09:00 AM - 05:00 PM'],
                'saturday' => ['10:00 AM - 02:00 PM'],
                'sunday' => ['Closed'],
            ]),
            'status' => fake()->randomElement(['Open', 'Closed']),
            'units' => $this->faker->randomElement([
                json_encode(['General Medicine', 'Pediatrics', 'Cardiology']),
                json_encode(['Emergency', 'Surgery', 'Obstetrics & Gynecology']),
                json_encode(['Orthopedics', 'Neurology', 'Dermatology']),
                json_encode(['Ophthalmology', 'ENT', 'Psychiatry']),
            ]),
        ];
    }

    /**
     * Indicate that the facility is active.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the facility is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
