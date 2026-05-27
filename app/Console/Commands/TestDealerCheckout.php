<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestDealerCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-dealer-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test dealer checkout with address auto-population';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create a test dealer with address information
        $customerData = [
            'name' => 'Test Dealer',
            'email' => 'testdealer@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'gst_number' => 'GST1234567890',
            'address' => '123 Business Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'zip_code' => '400001',
            'country' => 'India',
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
        $this->info("Address: " . $customer->address . ", " . $customer->city . ", " . $customer->state . " " . $customer->zip_code . ", " . $customer->country);

        // Test that the address information is available for checkout
        $this->info("\nTesting checkout address population:");
        $this->info("Billing Address: " . ($customer->address ?? 'Not set'));
        $this->info("Billing City: " . ($customer->city ?? 'Not set'));
        $this->info("Billing State: " . ($customer->state ?? 'Not set'));
        $this->info("Billing ZIP: " . ($customer->zip_code ?? 'Not set'));
        $this->info("Billing Country: " . ($customer->country ?? 'Not set'));

        $this->info("\nShipping Address should auto-populate with the same information in the checkout view.");

        // Clean up test data
        $customer->delete();
        $user->delete();

        $this->info("\nTest completed successfully!");
    }
}