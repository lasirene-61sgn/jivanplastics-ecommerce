<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 sample users
        User::factory()->count(10)->create();
        
        // Create 50 sample products
        Product::factory()->count(50)->create();
        
        $this->call([
            AdminSeeder::class,
            CustomerSeeder::class,
            ManufacturingTeamSeeder::class,
        ]);
    }
}