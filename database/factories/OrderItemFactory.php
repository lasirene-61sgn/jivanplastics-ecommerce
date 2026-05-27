<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $product->price;
        $total = $quantity * $price;
        
        return [
            'order_id' => Order::inRandomOrder()->first()->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => 'SKU-' . strtoupper($this->faker->lexify('???')) . $this->faker->numerify('####'),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total,
        ];
    }
}