<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::inRandomOrder()->first();
        
        return [
            'order_number' => 'ORD-' . strtoupper($this->faker->lexify('??????')),
            'customer_id' => $customer->id,
            'customer_type' => $customer->customer_type,
            'subtotal' => 0,
            'tax' => 0,
            'shipping' => 0,
            'total' => 0,
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'billing_address' => $this->faker->streetAddress(),
            'billing_city' => $this->faker->city(),
            'billing_state' => $this->faker->state(),
            'billing_zip' => $this->faker->postcode(),
            'billing_country' => $this->faker->country(),
            'shipping_address' => $this->faker->streetAddress(),
            'shipping_city' => $this->faker->city(),
            'shipping_state' => $this->faker->state(),
            'shipping_zip' => $this->faker->postcode(),
            'shipping_country' => $this->faker->country(),
        ];
    }
    
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Only create order items if they don't already exist
            if ($order->items->count() == 0) {
                // Create 1-5 order items for each order
                $itemsCount = $this->faker->numberBetween(1, 5);
                $subtotal = 0;
                
                for ($i = 0; $i < $itemsCount; $i++) {
                    $product = Product::inRandomOrder()->first();
                    $quantity = $this->faker->numberBetween(1, 5);
                    $price = $product->price;
                    $total = $quantity * $price;
                    $subtotal += $total;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => 'SKU-' . strtoupper($this->faker->lexify('???')) . $this->faker->numerify('####'),
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $total,
                    ]);
                }
                
                // Calculate tax and shipping
                $tax = $subtotal * 0.1;
                $shipping = $this->faker->randomFloat(2, 10, 50);
                $total = $subtotal + $tax + $shipping;
                
                // Update order with calculated values
                $order->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => $total,
                ]);
            }
        });
    }
}