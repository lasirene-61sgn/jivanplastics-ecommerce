<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 sample customers
        Customer::factory()->count(20)->create();
        
        // Create 30 sample orders
        Order::factory()->count(30)->create();
    }
}