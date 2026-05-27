<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealerDiscountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dealer_specific_discount_applies_to_products()
    {
        // Create a dealer customer
        $dealer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'company_name' => 'Test Dealer Company',
        ]);

        // Create a user for the dealer
        $user = User::factory()->create([
            'email' => $dealer->email,
        ]);

        // Create category, subcategory, and sub-subcategory
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% general B2B discount
        ]);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mobile Phones',
        ]);

        $subSubcategory = SubSubcategory::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Smartphones',
        ]);

        // Create a product
        $product = Product::factory()->create([
            'name' => 'iPhone 15',
            'price' => 1000.00,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'sub_subcategory_id' => $subSubcategory->id,
            'is_active' => true,
        ]);

        // Create a dealer-specific discount for the product (15%)
        $product->dealerDiscounts()->create([
            'customer_id' => $dealer->id,
            'discount_percentage' => 15.00,
            'is_active' => true,
        ]);

        // Login as the dealer
        $this->actingAs($user);

        // Check that the dealer-specific discount is applied (15%)
        $discountedPrice = $product->getB2BDiscountedPrice($dealer);
        $this->assertEquals(850.00, $discountedPrice); // 1000 - 15% = 850
    }

    /** @test */
    public function sub_subcategory_dealer_discount_applies_when_no_product_discount_exists()
    {
        // Create a dealer customer
        $dealer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'company_name' => 'Test Dealer Company',
        ]);

        // Create a user for the dealer
        $user = User::factory()->create([
            'email' => $dealer->email,
        ]);

        // Create category, subcategory, and sub-subcategory
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% general B2B discount
        ]);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mobile Phones',
        ]);

        $subSubcategory = SubSubcategory::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Smartphones',
        ]);

        // Create a dealer-specific discount for the sub-subcategory (12%)
        $subSubcategory->dealerDiscounts()->create([
            'customer_id' => $dealer->id,
            'discount_percentage' => 12.00,
            'is_active' => true,
        ]);

        // Create a product without a specific dealer discount
        $product = Product::factory()->create([
            'name' => 'iPhone 15',
            'price' => 1000.00,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'sub_subcategory_id' => $subSubcategory->id,
            'is_active' => true,
        ]);

        // Login as the dealer
        $this->actingAs($user);

        // Check that the sub-subcategory dealer discount is applied (12%)
        $discountedPrice = $product->getB2BDiscountedPrice($dealer);
        $this->assertEquals(880.00, $discountedPrice); // 1000 - 12% = 880
    }

    /** @test */
    public function subcategory_dealer_discount_applies_when_no_lower_level_discounts_exist()
    {
        // Create a dealer customer
        $dealer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'company_name' => 'Test Dealer Company',
        ]);

        // Create a user for the dealer
        $user = User::factory()->create([
            'email' => $dealer->email,
        ]);

        // Create category, subcategory, and sub-subcategory
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% general B2B discount
        ]);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mobile Phones',
        ]);

        // Create a dealer-specific discount for the subcategory (8%)
        $subcategory->dealerDiscounts()->create([
            'customer_id' => $dealer->id,
            'discount_percentage' => 8.00,
            'is_active' => true,
        ]);

        // Create a sub-subcategory without a specific dealer discount
        $subSubcategory = SubSubcategory::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Smartphones',
        ]);

        // Create a product without a specific dealer discount
        $product = Product::factory()->create([
            'name' => 'iPhone 15',
            'price' => 1000.00,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'sub_subcategory_id' => $subSubcategory->id,
            'is_active' => true,
        ]);

        // Login as the dealer
        $this->actingAs($user);

        // Check that the subcategory dealer discount is applied (8%)
        $discountedPrice = $product->getB2BDiscountedPrice($dealer);
        $this->assertEquals(920.00, $discountedPrice); // 1000 - 8% = 920
    }

    /** @test */
    public function category_dealer_discount_applies_when_no_lower_level_discounts_exist()
    {
        // Create a dealer customer
        $dealer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'company_name' => 'Test Dealer Company',
        ]);

        // Create a user for the dealer
        $user = User::factory()->create([
            'email' => $dealer->email,
        ]);

        // Create category with dealer-specific discount (5%)
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% general B2B discount
        ]);

        $category->dealerDiscounts()->create([
            'customer_id' => $dealer->id,
            'discount_percentage' => 5.00,
            'is_active' => true,
        ]);

        // Create subcategory without a specific dealer discount
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mobile Phones',
        ]);

        // Create sub-subcategory without a specific dealer discount
        $subSubcategory = SubSubcategory::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Smartphones',
        ]);

        // Create a product without a specific dealer discount
        $product = Product::factory()->create([
            'name' => 'iPhone 15',
            'price' => 1000.00,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'sub_subcategory_id' => $subSubcategory->id,
            'is_active' => true,
        ]);

        // Login as the dealer
        $this->actingAs($user);

        // Check that the category dealer discount is applied (5%)
        $discountedPrice = $product->getB2BDiscountedPrice($dealer);
        $this->assertEquals(950.00, $discountedPrice); // 1000 - 5% = 950
    }

    /** @test */
    public function general_b2b_discount_applies_when_no_dealer_specific_discounts_exist()
    {
        // Create a dealer customer
        $dealer = Customer::factory()->create([
            'customer_type' => 'dealer',
            'company_name' => 'Test Dealer Company',
        ]);

        // Create a user for the dealer
        $user = User::factory()->create([
            'email' => $dealer->email,
        ]);

        // Create category with general B2B discount (10%)
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% general B2B discount
        ]);

        // Create subcategory without a specific dealer discount
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mobile Phones',
        ]);

        // Create sub-subcategory without a specific dealer discount
        $subSubcategory = SubSubcategory::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => 'Smartphones',
        ]);

        // Create a product without a specific dealer discount
        $product = Product::factory()->create([
            'name' => 'iPhone 15',
            'price' => 1000.00,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'sub_subcategory_id' => $subSubcategory->id,
            'is_active' => true,
        ]);

        // Login as the dealer
        $this->actingAs($user);

        // Check that the general B2B discount is applied (10%)
        $discountedPrice = $product->getB2BDiscountedPrice($dealer);
        $this->assertEquals(900.00, $discountedPrice); // 1000 - 10% = 900
    }

    /** @test */
    public function no_discount_applies_for_non_dealer_customers()
    {
        // Create an individual customer
        $customer = Customer::factory()->create([
            'customer_type' => 'individual',
        ]);

        // Create a user for the customer
        $user = User::factory()->create([
            'email' => $customer->email,
        ]);

        // Create category with general B2B discount (10%)
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'b2b_discount' => 10.00, // 10% general B2B discount
        ]);

        // Create a product
        $product = Product::factory()->create([
            'name' => 'iPhone 15',
            'price' => 1000.00,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Login as the individual customer
        $this->actingAs($user);

        // Check that no discount is applied
        $discountedPrice = $product->getB2BDiscountedPrice($customer);
        $this->assertEquals(1000.00, $discountedPrice); // No discount for B2C
    }
}