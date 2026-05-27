<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function b2b_customers_earn_loyalty_points_on_orders_over_2000()
    {
        // Create a B2B customer
        $customer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'loyalty_points' => 0,
        ]);

        // Create an order over ₹2000
        $order = new Order([
            'customer_id' => $customer->id,
            'customer_type' => 'dealer',
            'total' => 5000,
        ]);

        // Check that the order calculates the correct loyalty points
        $this->assertEquals(5, $order->calculateLoyaltyPoints()); // 5000 / 1000 = 5 points
    }

    /** @test */
    public function b2b_customers_dont_earn_points_on_orders_under_2000()
    {
        // Create a B2B customer
        $customer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'loyalty_points' => 0,
        ]);

        // Create an order under ₹2000
        $order = new Order([
            'customer_id' => $customer->id,
            'customer_type' => 'dealer',
            'total' => 1500,
        ]);

        // Check that the order calculates zero loyalty points
        $this->assertEquals(0, $order->calculateLoyaltyPoints());
    }

    /** @test */
    public function b2c_customers_dont_earn_loyalty_points()
    {
        // Create a B2C customer
        $customer = Customer::factory()->create([
            'customer_type' => 'individual',
            'loyalty_points' => 0,
        ]);

        // Create an order over ₹2000
        $order = new Order([
            'customer_id' => $customer->id,
            'customer_type' => 'individual',
            'total' => 5000,
        ]);

        // Check that the order calculates zero loyalty points
        $this->assertEquals(0, $order->calculateLoyaltyPoints());
    }

    /** @test */
    public function loyalty_points_are_displayed_in_b2b_dashboard()
    {
        // Create a B2B customer with some loyalty points
        $user = User::factory()->create([
            'email' => 'b2b3@example.com',
        ]);

        $customer = Customer::factory()->create([
            'email' => 'b2b3@example.com',
            'customer_type' => 'dealer',
            'loyalty_points' => 15,
        ]);

        // Login as the B2B customer
        $this->actingAs($user);

        // Visit the B2B dashboard
        $response = $this->get(route('b2b.dashboard'));

        // Assert that the loyalty points are displayed
        $response->assertSee('LOYALTY POINTS');
        $response->assertSee('15');
    }

    /** @test */
    public function awarding_loyalty_points_increases_customer_balance()
    {
        // Create a B2B customer
        $customer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'loyalty_points' => 10,
        ]);

        // Create an order over ₹2000
        $order = Order::factory()->make([
            'customer_id' => $customer->id,
            'customer_type' => 'dealer',
            'total' => 5000,
        ]);
        
        // Set the order's customer relationship
        $order->setRelation('customer', $customer);

        // Award loyalty points
        $order->awardLoyaltyPoints();

        // Check that the customer's loyalty points increased
        $customer->refresh();
        $this->assertEquals(15, $customer->loyalty_points); // 10 + 5 points
    }
}