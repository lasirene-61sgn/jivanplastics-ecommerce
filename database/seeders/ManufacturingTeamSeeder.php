<?php

namespace Database\Seeders;

use App\Models\ManufacturingTeam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManufacturingTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create manufacturing team if it doesn't exist
        ManufacturingTeam::firstOrCreate([
            'email' => 'manufacturing@example.com'
        ], [
            'factory_name' => 'Sample Manufacturing Unit',
            'contact_person' => 'John Manager',
            'phone' => '123-456-7890',
            'password' => Hash::make('password'), // Default password
            'address' => '123 Factory Street, Industrial Area',
            'manufacturing_unit_type' => 'Electronics',
            'is_active' => true,
        ]);
    }
}