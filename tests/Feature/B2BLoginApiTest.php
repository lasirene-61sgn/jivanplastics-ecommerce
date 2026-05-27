<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;

class B2BLoginApiTest extends TestCase
{
    public function test_b2b_login_with_valid_credentials()
    {
        // Create a test dealer user
        $user = User::factory()->create([
            'email' => 'b2b@example.com',
            'password' => Hash::make('password')
        ]);

        $customer = Customer::create([
            'name' => 'Test Dealer',
            'email' => 'b2b@example.com',
            'customer_type' => 'dealer',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/b2b/login', [
            'email' => 'b2b@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user',
                     'token',
                     'token_type'
                 ]);
    }

    public function test_b2b_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/b2b/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(422);
    }

    public function test_b2b_login_with_non_dealer_account()
    {
        // Create a regular user (not a dealer)
        $user = User::factory()->create([
            'email' => 'regular@example.com',
            'password' => Hash::make('password')
        ]);

        $customer = Customer::create([
            'name' => 'Regular Customer',
            'email' => 'regular@example.com',
            'customer_type' => 'individual', // Not a dealer
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/b2b/login', [
            'email' => 'regular@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_b2b_logout()
    {
        // Create and authenticate a dealer user
        $user = User::factory()->create([
            'email' => 'b2b@example.com',
            'password' => Hash::make('password')
        ]);

        $customer = Customer::create([
            'name' => 'Test Dealer',
            'email' => 'b2b@example.com',
            'customer_type' => 'dealer',
            'is_active' => true,
        ]);

        // Login first to get a token
        $loginResponse = $this->postJson('/api/b2b/login', [
            'email' => 'b2b@example.com',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        // Logout using the token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/b2b/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout successful']);
    }

    public function test_get_authenticated_b2b_user()
    {
        // Create and authenticate a dealer user
        $user = User::factory()->create([
            'email' => 'b2b@example.com',
            'password' => Hash::make('password')
        ]);

        $customer = Customer::create([
            'name' => 'Test Dealer',
            'email' => 'b2b@example.com',
            'customer_type' => 'dealer',
            'is_active' => true,
        ]);

        // Login first to get a token
        $loginResponse = $this->postJson('/api/b2b/login', [
            'email' => 'b2b@example.com',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        // Get authenticated user details
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/b2b/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'customer_type'
                     ]
                 ]);
    }
}