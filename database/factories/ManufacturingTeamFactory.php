<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ManufacturingTeam>
 */
class ManufacturingTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'factory_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'password' => Hash::make('password'), // Default password
            'address' => fake()->address(),
            'manufacturing_unit_type' => fake()->randomElement(['Textile', 'Plastic', 'Metal', 'Wood', 'Electronics', null]),
            'is_active' => fake()->boolean(80), // 80% chance of being active
            'remember_token' => Str::random(10),
        ];
    }
}