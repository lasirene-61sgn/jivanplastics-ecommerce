<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestDealerAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-dealer-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test dealer authentication fix';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create a test dealer
        $customerData = [
            'name' => 'Test Dealer',
            'email' => 'testdealer@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'gst_number' => 'GST1234567890',
            'customer_type' => 'dealer',
            'is_active' => true,
        ];

        $password = 'password123';

        // Create customer record
        $customer = Customer::create(array_merge($customerData, [
            'password' => Hash::make($password)
        ]));

        // Create corresponding user record
        $user = User::updateOrCreate(
            ['email' => $customerData['email']],
            [
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'password' => Hash::make($password)
            ]
        );

        $this->info("Created customer: " . $customer->name . " (" . $customer->email . ")");
        $this->info("Created user: " . $user->name . " (" . $user->email . ")");

        // Test authentication
        $credentials = [
            'email' => 'testdealer@example.com',
            'password' => 'password123'
        ];

        // Simulate login attempt
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            $this->info("Authentication successful!");
            
            // Check customer type
            $customer = Customer::where('email', $user->email)->first();
            if ($customer && $customer->customer_type === 'dealer') {
                $this->info("Customer is a dealer. Redirect to B2B dashboard.");
            } else {
                $this->info("Customer is not a dealer.");
            }
        } else {
            $this->error("Authentication failed!");
        }
    }
}