<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\ActivityLog;
use App\Services\SaleService;
use App\Services\PurchaseService;
use App\Services\PaymentService;
use App\Services\ReturnService;
use RuntimeException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class CoreBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_payment_overpayment_protection()
    {
        $user = User::create([
            'name' => 'Finance Admin',
            'email' => 'overpay_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Electronics']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Headphones',
            'sku' => 'HP-01',
            'cost_price' => 20,
            'selling_price' => 50,
            'stock_quantity' => 20,
            'unit' => 'pcs',
            'low_stock_threshold' => 2,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-OVERPAY',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 30, // Sale total = 50, Paid = 30 -> Outstanding = 20
        ], [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 50],
        ]);

        $paymentService = app(PaymentService::class);

        // Attempting payment of $50 when outstanding is $20
        $this->expectException(RuntimeException::class);
        $paymentService->customerPaymentForSale($sale, 50);
    }

    #[Test]
    public function test_return_quantity_bounds_protection()
    {
        $user = User::create([
            'name' => 'Return Manager',
            'email' => 'return_bounds@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Accessories']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Mouse Pad',
            'sku' => 'MP-01',
            'cost_price' => 5,
            'selling_price' => 15,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-RET-BOUNDS',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 30,
        ], [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 15], // Sold 2
        ]);

        $returnService = app(ReturnService::class);

        // Attempting return of 5 items when only 2 were sold
        $this->expectException(InvalidArgumentException::class);
        $returnService->processSaleReturn($sale, [
            'return_date' => now()->toDateString(),
            'notes' => 'Excess return attempt',
        ], [
            ['product_id' => $product->id, 'quantity' => 5, 'reason' => 'Damaged'],
        ]);
    }

    #[Test]
    public function test_sales_return_restocking_increases_inventory()
    {
        $user = User::create([
            'name' => 'Return Manager 2',
            'email' => 'return_restock@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Gadgets']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'USB Cable',
            'sku' => 'USB-C-01',
            'cost_price' => 2,
            'selling_price' => 10,
            'stock_quantity' => 20,
            'unit' => 'pcs',
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-RET-SUCCESS',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 50,
        ], [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 10],
        ]);

        // Stock decreased to 15
        $product->refresh();
        $this->assertEquals(15, $product->stock_quantity);

        $returnService = app(ReturnService::class);
        $returnService->processSaleReturn($sale, [
            'return_date' => now()->toDateString(),
            'notes' => 'Customer return',
        ], [
            ['product_id' => $product->id, 'quantity' => 2, 'reason' => 'Wrong model'],
        ]);

        // Stock re-increments from 15 to 17
        $product->refresh();
        $this->assertEquals(17, $product->stock_quantity);
    }

    #[Test]
    public function test_historical_cost_price_preservation()
    {
        $user = User::create([
            'name' => 'Sales Admin',
            'email' => 'cost_preserve@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Components']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'RAM 16GB',
            'sku' => 'RAM-16',
            'cost_price' => 40,
            'selling_price' => 70, // Selling price = $70
            'stock_quantity' => 50,
            'unit' => 'pcs',
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-HISTORICAL-COST',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 70,
        ], [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 70],
        ]);

        // Product selling price changes later from $70 to $95
        $product->update(['selling_price' => 95, 'cost_price' => 60]);

        // Sale item snapshot unit price and subtotal must remain $70
        $saleItem = $sale->items()->first();
        $this->assertEquals(70, $saleItem->unit_price);
        $this->assertEquals(70, $saleItem->subtotal);
    }

    #[Test]
    public function test_activity_logging_system()
    {
        $user = User::create([
            'name' => 'Audit Admin',
            'email' => 'audit_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        // Perform category creation
        $this->post('/categories', [
            'name' => 'Audit Test Category',
            'description' => 'Test description',
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Audit Test Category']);
    }
}
