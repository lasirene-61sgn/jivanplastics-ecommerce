<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesTeam;
use Illuminate\Support\Facades\Hash;

class SalesTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalesTeam::create([
            'name' => 'John Sales',
            'email' => 'john@sales.com',
            'password' => Hash::make('password123'),
            'department' => 'Sales',
            'is_active' => true,
        ]);
    }
}