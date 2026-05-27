<?php

require_once 'vendor/autoload.php';

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

echo "Created customer: " . $customer->name . " (" . $customer->email . ")\n";
echo "Created user: " . $user->name . " (" . $user->email . ")\n";

// Test authentication
$credentials = [
    'email' => 'testdealer@example.com',
    'password' => 'password123'
];

// Simulate login attempt
$user = User::where('email', $credentials['email'])->first();

if ($user && Hash::check($credentials['password'], $user->password)) {
    echo "Authentication successful!\n";
    
    // Check customer type
    $customer = Customer::where('email', $user->email)->first();
    if ($customer && $customer->customer_type === 'dealer') {
        echo "Customer is a dealer. Redirect to B2B dashboard.\n";
    } else {
        echo "Customer is not a dealer.\n";
    }
} else {
    echo "Authentication failed!\n";
}