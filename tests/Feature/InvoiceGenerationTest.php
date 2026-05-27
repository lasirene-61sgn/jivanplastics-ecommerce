<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\ManufacturingTeam;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;
    protected $product;
    protected $order;
    protected $orderItem;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = Admin::factory()->create();

        // Create Customer
        $this->customer = Customer::factory()->create();

        // Create Product
        $this->product = Product::factory()->create([
            'price' => 100,
            'per_quantity_pieces' => 1,
        ]);

        // Create Manufacturing Team
        $this->manufacturingTeam = ManufacturingTeam::factory()->create();

        // Create Order
        $this->order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'manufacturing_status' => 'allocated', // Start as allocated for dispatch testing
            'subtotal' => 500,
            'total' => 500,
            'manufacturing_team_id' => $this->manufacturingTeam->id,
        ]);

        // Create Order Item (Quantity 5)
        $this->orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'price' => 100,
            'total' => 500,
            'manufactured_quantity' => 5, // Fully manufactured, ready to dispatch
        ]);

        $this->order->refresh();
    }

    public function test_partial_dispatch_creates_invoice()
    {
        $this->actingAs($this->admin, 'admin');

        // Dispatch 2 items
        $response = $this->put(route('admin.orders.partial-dispatch', $this->order), [
            'dispatched_quantities' => [
                $this->orderItem->id => 2
            ],
            'order_dispatch_description' => 'First batch',
        ]);

        $response->assertRedirect();
        
        // Assert Invoice Created
        $this->assertDatabaseHas('invoices', [
            'order_id' => $this->order->id,
            'invoice_number' => $this->order->order_number . '-INV-01',
        ]);

        $invoice = Invoice::where('order_id', $this->order->id)->first();
        
        // Assert Invoice Items
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'quantity' => 2,
            'unit_price' => 100,
        ]);

        // Assert Order Status
        $this->orderItem->refresh();
        $this->assertEquals(2, $this->orderItem->dispatched_quantity);
    }

    public function test_multiple_partial_dispatches_create_multiple_invoices()
    {
        $this->actingAs($this->admin, 'admin');

        // 1st Dispatch: 2 items
        $this->put(route('admin.orders.partial-dispatch', $this->order), [
            'dispatched_quantities' => [
                $this->orderItem->id => 2
            ],
            'order_dispatch_description' => 'Batch 1',
        ]);

        // 2nd Dispatch: 3 items (Remaining)
        $this->put(route('admin.orders.partial-dispatch', $this->order), [
            'dispatched_quantities' => [
                $this->orderItem->id => 3
            ],
            'order_dispatch_description' => 'Batch 2',
        ]);

        // Assert 2 Invoices Created
        $this->assertEquals(2, Invoice::where('order_id', $this->order->id)->count());

        $invoice1 = Invoice::where('invoice_number', $this->order->order_number . '-INV-01')->first();
        $invoice2 = Invoice::where('invoice_number', $this->order->order_number . '-INV-02')->first();

        $this->assertNotNull($invoice1);
        $this->assertNotNull($invoice2);

        // Assert Invoice 1 Item Quantity
        $item1 = $invoice1->items->where('order_item_id', $this->orderItem->id)->first();
        $this->assertEquals(2, $item1->quantity);

        // Assert Invoice 2 Item Quantity
        $item2 = $invoice2->items->where('order_item_id', $this->orderItem->id)->first();
        $this->assertEquals(3, $item2->quantity);
        
        // Assert Order Fully Dispatched
        $this->orderItem->refresh();
        $this->assertEquals(5, $this->orderItem->dispatched_quantity);
    }

    public function test_mark_as_dispatched_creates_final_invoice()
    {
        $this->actingAs($this->admin, 'admin');
        
        // Set order to completed status which enables the final dispatch button logic in view/controller
        // Note: Controller logic for 'markAsDispatched' (dispatch route) handles pending items.
        
        // Ensure items are manufactured so logic allows dispatch
        $this->order->update(['manufacturing_status' => 'completed']);

        // Call mark as dispatched (final dispatch)
        $response = $this->post(route('admin.orders.dispatch', $this->order), [
            'dispatch_description' => 'Final shipment',
        ]);

        $response->assertRedirect();

        // Assert Invoice Created for all 5 items
        $this->assertDatabaseHas('invoices', [
            'order_id' => $this->order->id,
            'invoice_number' => $this->order->order_number . '-INV-01',
        ]);

        $invoice = Invoice::first();
        $item = $invoice->items->where('order_item_id', $this->orderItem->id)->first();
        $this->assertEquals(5, $item->quantity);
    }

    public function test_mixed_partial_and_final_dispatch()
    {
        $this->actingAs($this->admin, 'admin');

        // 1st Dispatch: 2 items
        $this->put(route('admin.orders.partial-dispatch', $this->order), [
            'dispatched_quantities' => [
                $this->orderItem->id => 2
            ],
            'order_dispatch_description' => 'Batch 1',
        ]);

        // Update status to completed to allow final dispatch
        $this->order->update(['manufacturing_status' => 'completed']);

        // Final Dispatch (Remaining 3 items)
        $this->post(route('admin.orders.dispatch', $this->order), [
            'dispatch_description' => 'Final Remainder',
        ]);

        // Assert 2 Invoices
        $this->assertEquals(2, Invoice::where('order_id', $this->order->id)->count());

        // Check quantities
        $invoices = Invoice::where('order_id', $this->order->id)->get();
        
        $item1 = $invoices[0]->items->where('order_item_id', $this->orderItem->id)->first();
        $this->assertEquals(2, $item1->quantity);
        
        $item2 = $invoices[1]->items->where('order_item_id', $this->orderItem->id)->first();
        $this->assertEquals(3, $item2->quantity);
    }
}
