<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\PurchaseService;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use Exception;

class PurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_purchase_calculates_totals_server_side_and_increments_stock()
    {
        // 1. Initial Setup: Create User, Category, Product, Supplier
        $user = User::create([
            'name' => 'Ali Manager',
            'email' => 'ali@example.com',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::create([
            'name' => 'Electronics',
            'description' => 'Electronic products',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'HP ProBook 450',
            'sku' => 'HP-PB450-001',
            'purchase_price' => 85000,
            'selling_price' => 95000,
            'stock_quantity' => 10, // Initial Stock = 10
            'unit' => 'pcs',
            'reorder_level' => 3,
            'status' => 'active',
        ]);

        $supplier = Supplier::create([
            'name' => 'ABC Electronics',
            'contact_person' => 'Ali Khan',
            'phone' => '03001234567',
            'email' => 'abc@example.com',
            'address' => 'Lahore, Pakistan',
        ]);

        // 2. Input Data without trusting client totals (even if client attempts total_amount = 1)
        $data = [
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'ABC-INV-002',
            'paid_amount' => 425000,
            'payment_status' => 'paid',
            'total_amount' => 1, // Bogus client total to test server-side recalculation override
        ];

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 5,
                'unit_price' => 85000,
            ],
        ];

        // 3. Execute Service Method
        $service = new PurchaseService();
        $purchase = $service->createPurchase($data, $items);

        // 4. Assertions
        
        // A. Server-Side Calculated Totals Verification (5 x 85000 = 425000)
        $this->assertEquals(425000, $purchase->total_amount);
        $this->assertEquals(425000, $purchase->items->first()->subtotal);

        // B. Stock Increment Verification (10 + 5 = 15)
        $product->refresh();
        $this->assertEquals(15, $product->stock_quantity);

        // C. DB Record Verification
        $this->assertDatabaseHas('purchases', [
            'invoice_number' => 'ABC-INV-002',
            'total_amount' => 425000,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'subtotal' => 425000,
        ]);
    }

    /** @test */
    public function it_rolls_back_transaction_if_an_error_occurs()
    {
        $user = User::create([
            'name' => 'Manager',
            'email' => 'mgr@example.com',
            'password' => bcrypt('password123'),
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier XYZ',
            'contact_person' => 'John',
            'phone' => '123',
            'email' => 'xyz@example.com',
            'address' => 'City',
        ]);

        $category = Category::create(['name' => 'Hardware']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Monitor',
            'sku' => 'MON-001',
            'purchase_price' => 20000,
            'selling_price' => 25000,
            'stock_quantity' => 5,
            'unit' => 'pcs',
            'reorder_level' => 1,
            'status' => 'active',
        ]);

        $data = [
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'FAIL-INV-001',
            'paid_amount' => 20000,
            'payment_status' => 'paid',
        ];

        // Item with non-existent product ID to trigger exception during transaction
        $items = [
            [
                'product_id' => 9999, // Invalid product ID
                'quantity' => 10,
                'unit_price' => 20000,
            ],
        ];

        $service = new PurchaseService();

        try {
            $service->createPurchase($data, $items);
            $this->fail('Expected Exception was not thrown.');
        } catch (Exception $e) {
            // Expected exception
        }

        // DB Rollback verification: No purchase should exist in DB
        $this->assertDatabaseMissing('purchases', [
            'invoice_number' => 'FAIL-INV-001',
        ]);

        // Product stock should remain unchanged at 5
        $product->refresh();
        $this->assertEquals(5, $product->stock_quantity);
    }
}
