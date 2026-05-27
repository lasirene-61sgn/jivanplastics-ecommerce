<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class B2BPaymentOptionsTest extends TestCase
{
    use RefreshDatabase;

    protected $dealer;
    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dealer = Customer::create([
            'name' => 'Test Dealer',
            'email' => 'dealer@test.com',
            'password' => bcrypt('password'),
            'customer_type' => 'dealer',
            'is_active' => true,
            'is_cod_allowed' => true,
            'bank_transfer_discount' => 5.00,
        ]);

        $this->user = User::create([
            'name' => 'Test Dealer',
            'email' => 'dealer@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->product = Product::create([
            'name' => 'Test Product',
            'price' => 1000.00,
            'gst_percentage' => 18,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function cod_is_rejected_when_not_allowed()
    {
        $this->dealer->update(['is_cod_allowed' => false]);
        $this->actingAs($this->user);

        // Add to cart
        $cart = [
            $this->product->id => [
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => 1000.00,
            ]
        ];
        session(['cart' => $cart]);

        $response = $this->post(route('checkout.process'), [
            'billing_address' => '123 Test St',
            'billing_city' => 'Test City',
            'billing_state' => 'Test State',
            'billing_zip' => '12345',
            'billing_country' => 'India',
            'use_same_address' => 1,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cash on Delivery is not available for your account. Please use Bank Transfer.');
        $this->assertEquals(0, Order::count());
    }

    /** @test */
    public function bank_transfer_discount_is_applied_correctly()
    {
        $this->actingAs($this->user);

        // Add to cart
        $cart = [
            $this->product->id => [
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => 1000.00,
            ]
        ];
        session(['cart' => $cart]);

        // total = 1000 (subtotal) + 180 (GST) = 1180
        // discount = 5% of 1180 = 59
        // final total = 1180 - 59 = 1121

        $response = $this->post(route('checkout.process'), [
            'billing_address' => '123 Test St',
            'billing_city' => 'Test City',
            'billing_state' => 'Test State',
            'billing_zip' => '12345',
            'billing_country' => 'India',
            'use_same_address' => 1,
            'payment_method' => 'bank_transfer',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('bank_transfer', $order->payment_method);
        $this->assertEquals(59.00, $order->bank_transfer_discount_amount);
        $this->assertEquals(1121.00, $order->total);
        
        $response->assertRedirect(route('checkout.success', $order));
    }
}
