<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class B2BDiscountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function b2b_customers_see_discounted_prices()
    {
        // Create a category with a B2B discount
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% discount for B2B
        ]);

        // Create a product in that category
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Create a B2B customer
        $user = User::factory()->create([
            'email' => 'b2b@example.com',
        ]);

        $customer = Customer::factory()->create([
            'email' => 'b2b@example.com',
            'customer_type' => 'dealer',
        ]);

        // Login as the B2B customer
        $this->actingAs($user);

        // Visit the product page
        $response = $this->get(route('products.show', $product));

        // Assert that the response contains the discounted price
        $response->assertSee('₹90.00'); // 10% discount on ₹100 = ₹90
        $response->assertSee('10.00% OFF for B2B Customers');
    }

    /** @test */
    public function b2c_customers_see_regular_prices()
    {
        // Create a category with a B2B discount
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% discount for B2B
        ]);

        // Create a product in that category
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Create a B2C customer
        $user = User::factory()->create([
            'email' => 'b2c@example.com',
        ]);

        $customer = Customer::factory()->create([
            'email' => 'b2c@example.com',
            'customer_type' => 'individual',
        ]);

        // Login as the B2C customer
        $this->actingAs($user);

        // Visit the product page
        $response = $this->get(route('products.show', $product));

        // Assert that the response contains the regular price (no discount)
        $response->assertSee('₹100.00');
        $response->assertDontSee('OFF for B2B Customers');
    }

    /** @test */
    public function category_without_discount_shows_regular_price_for_b2b()
    {
        // Create a category without a B2B discount
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => null,
        ]);

        // Create a product in that category
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Create a B2B customer
        $user = User::factory()->create([
            'email' => 'b2b2@example.com',
        ]);

        $customer = Customer::factory()->create([
            'email' => 'b2b2@example.com',
            'customer_type' => 'dealer',
        ]);

        // Login as the B2B customer
        $this->actingAs($user);

        // Visit the product page
        $response = $this->get(route('products.show', $product));

        // Assert that the response contains the regular price (no discount)
        $response->assertSee('₹100.00');
        $response->assertDontSee('OFF for B2B Customers');
    }
}