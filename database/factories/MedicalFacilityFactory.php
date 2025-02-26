<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MedicalFacility;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalFacility>
 */
class MedicalFacilityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MedicalFacility::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => $this->faker->address(),
            'emergency_contact' => $this->faker->phoneNumber(),
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
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
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



    /**
     * Define a hospital with 24/7 emergency services.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function emergencyHospital()
    {
        return $this->state(function (array $attributes) {
            return [
                'description' => 'Full-service hospital with 24/7 emergency care.',
                'operating_hours' => json_encode([
                    'monday' => ['Open 24 hours'],
                    'tuesday' => ['Open 24 hours'],
                    'wednesday' => ['Open 24 hours'],
                    'thursday' => ['Open 24 hours'],
                    'friday' => ['Open 24 hours'],
                    'saturday' => ['Open 24 hours'],
                    'sunday' => ['Open 24 hours'],
                ]),
                'units' => json_encode([
                    'Emergency',
                    'General Medicine',
                    'Surgery',
                    'ICU',
                    'Pediatrics',
                    'Cardiology',
                    'Orthopedics'
                ]),
            ];
        });
    }
}
