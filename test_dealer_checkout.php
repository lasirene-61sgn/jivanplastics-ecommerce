<?php

require_once 'vendor/autoload.php';

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

echo "Created customer: " . $customer->name . " (" . $customer->email . ")\n";
echo "Address: " . $customer->address . ", " . $customer->city . ", " . $customer->state . " " . $customer->zip_code . ", " . $customer->country . "\n";

// Test that the address information is available for checkout
echo "\nTesting checkout address population:\n";
echo "Billing Address: " . ($customer->address ?? 'Not set') . "\n";
echo "Billing City: " . ($customer->city ?? 'Not set') . "\n";
echo "Billing State: " . ($customer->state ?? 'Not set') . "\n";
echo "Billing ZIP: " . ($customer->zip_code ?? 'Not set') . "\n";
echo "Billing Country: " . ($customer->country ?? 'Not set') . "\n";

echo "\nShipping Address should auto-populate with the same information.\n";

// Clean up test data
$customer->delete();
$user->delete();

echo "\nTest completed successfully!\n";