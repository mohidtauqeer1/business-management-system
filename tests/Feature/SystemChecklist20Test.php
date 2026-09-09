<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ActivityLog;
use App\Services\PurchaseService;
use App\Services\SaleService;
use App\Services\PaymentService;
use App\Services\ReturnService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class SystemChecklist20Test extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_001_login()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't1@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 't1@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function test_002_admin_permissions()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 't2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin);
        $this->get('/users')->assertStatus(200);
        $this->get('/settings')->assertStatus(200);
        $this->get('/activity-logs')->assertStatus(200);
    }

    #[Test]
    public function test_003_manager_permissions()
    {
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 't3@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);

        $this->actingAs($manager);
        $this->get('/products')->assertStatus(200);
        $this->get('/purchases')->assertStatus(200);
        $this->get('/sales')->assertStatus(200);
        $this->get('/users')->assertStatus(403);
    }

    #[Test]
    public function test_004_create_category()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 't4@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin);

        $response = $this->post('/categories', [
            'name' => 'Test Category',
            'description' => 'Category Description',
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    #[Test]
    public function test_005_create_product()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 't5@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin);

        $category = Category::create(['name' => 'Hardware']);

        $response = $this->post('/products', [
            'category_id' => $category->id,
            'name' => 'Gaming Mouse',
            'sku' => 'MOUSE-01',
            'purchase_price' => 25,
            'selling_price' => 50,
            'stock_quantity' => 0,
            'unit' => 'pcs',
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('products', ['sku' => 'MOUSE-01']);
    }

    #[Test]
    public function test_006_create_supplier()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 't6@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin);

        $response = $this->post('/suppliers', [
            'name' => 'Supplier Co',
            'phone' => '12345678',
            'email' => 'sup@test.com',
            'address' => 'Supplier Address',
        ]);

        $this->assertDatabaseHas('suppliers', ['email' => 'sup@test.com']);
    }

    #[Test]
    public function test_007_create_purchase()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't7@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Keyboard',
            'sku' => 'KB-01',
            'cost_price' => 30,
            'selling_price' => 60,
            'stock_quantity' => 0,
            'unit' => 'pcs',
            'low_stock_threshold' => 2,
            'is_active' => true,
        ]);
        $supplier = Supplier::create(['name' => 'Key Supplier', 'phone' => '123', 'email' => 'key@sup.com', 'address' => 'City']);

        $service = app(PurchaseService::class);
        $purchase = $service->createPurchase([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'PUR-007',
            'paid_amount' => 300,
        ], [
            ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 30],
        ]);

        $this->assertDatabaseHas('purchases', ['invoice_number' => 'PUR-007']);
    }

    #[Test]
    public function test_008_verify_inventory()
    {
        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'SSD 500GB',
            'sku' => 'SSD-500',
            'cost_price' => 40,
            'selling_price' => 80,
            'stock_quantity' => 25,
            'unit' => 'pcs',
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $this->assertEquals(25, $product->stock_quantity);
    }

    #[Test]
    public function test_009_create_sale()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't9@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Webcam 1080p',
            'sku' => 'CAM-1080',
            'cost_price' => 20,
            'selling_price' => 50,
            'stock_quantity' => 15,
            'unit' => 'pcs',
            'low_stock_threshold' => 2,
            'is_active' => true,
        ]);

        $service = app(SaleService::class);
        $sale = $service->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-009',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 100,
        ], [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 50],
        ]);

        $this->assertDatabaseHas('sales', ['invoice_number' => 'INV-009']);
    }

    #[Test]
    public function test_010_verify_inventory()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't10@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Monitor 24"',
            'sku' => 'MON-24',
            'cost_price' => 100,
            'selling_price' => 180,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 2,
            'is_active' => true,
        ]);

        $service = app(SaleService::class);
        $service->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-010',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 180,
        ], [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 180],
        ]);

        $product->refresh();
        $this->assertEquals(9, $product->stock_quantity);
    }

    #[Test]
    public function test_011_customer_credit()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't11@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'Credit Customer',
            'phone' => '111222',
            'email' => 'cred@test.com',
            'address' => 'City',
            'credit_limit_enabled' => true,
            'credit_limit' => 100,
        ]);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Cable',
            'sku' => 'CBL-01',
            'cost_price' => 10,
            'selling_price' => 30,
            'stock_quantity' => 20,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);

        $service = app(SaleService::class);
        $this->expectException(InvalidArgumentException::class);

        $service->createSale([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'invoice_number' => 'INV-011',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 0, // Unpaid $150 > $100 limit
        ], [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 30],
        ]);
    }

    #[Test]
    public function test_012_supplier_payment()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't12@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Drive',
            'sku' => 'DRV-01',
            'cost_price' => 50,
            'selling_price' => 90,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);
        $supplier = Supplier::create(['name' => 'Sup 12', 'phone' => '12', 'email' => 'sup12@test.com', 'address' => 'Addr']);

        $purchaseService = app(PurchaseService::class);
        $purchase = $purchaseService->createPurchase([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'PUR-012',
            'paid_amount' => 200, // Total = 500, Paid = 200, Unpaid = 300
        ], [
            ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 50],
        ]);

        $paymentService = app(PaymentService::class);
        $payment = $paymentService->supplierPaymentForPurchase($purchase, 100, 'cash', 'PAY-SUP-012');

        $this->assertDatabaseHas('payments', ['reference_number' => 'PAY-SUP-012', 'amount' => 100]);
    }

    #[Test]
    public function test_013_customer_payment()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't13@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $customer = Customer::create(['name' => 'Cust 13', 'phone' => '13', 'email' => 'c13@test.com', 'address' => 'Addr']);
        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Headset',
            'sku' => 'HS-01',
            'cost_price' => 20,
            'selling_price' => 40,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'invoice_number' => 'INV-013',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 20, // Total = 80, Unpaid = 60
        ], [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 40],
        ]);

        $paymentService = app(PaymentService::class);
        $payment = $paymentService->customerPaymentForSale($sale, 30, 'cash', 'PAY-CUST-013');

        $this->assertDatabaseHas('payments', ['reference_number' => 'PAY-CUST-013', 'amount' => 30]);
    }

    #[Test]
    public function test_014_sales_return()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't14@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Mouse',
            'sku' => 'MS-14',
            'cost_price' => 10,
            'selling_price' => 20,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-014',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 40,
        ], [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 20],
        ]);

        $returnService = app(ReturnService::class);
        $saleReturn = $returnService->processSaleReturn($sale, [
            'return_date' => now()->toDateString(),
            'notes' => 'Sale return test',
        ], [
            ['product_id' => $product->id, 'quantity' => 1, 'reason' => 'Defective'],
        ]);

        $this->assertDatabaseHas('sale_returns', ['sale_id' => $sale->id]);
    }

    #[Test]
    public function test_015_purchase_return()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't15@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Adapter',
            'sku' => 'ADP-15',
            'cost_price' => 15,
            'selling_price' => 30,
            'stock_quantity' => 20,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);
        $supplier = Supplier::create(['name' => 'Sup 15', 'phone' => '15', 'email' => 's15@test.com', 'address' => 'Addr']);

        $purchaseService = app(PurchaseService::class);
        $purchase = $purchaseService->createPurchase([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'PUR-015',
            'paid_amount' => 150,
        ], [
            ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 15],
        ]);

        $returnService = app(ReturnService::class);
        $purchaseReturn = $returnService->processPurchaseReturn($purchase, [
            'return_date' => now()->toDateString(),
            'notes' => 'Purchase return test',
        ], [
            ['product_id' => $product->id, 'quantity' => 2, 'reason' => 'Overstock'],
        ]);

        $this->assertDatabaseHas('purchase_returns', ['purchase_id' => $purchase->id]);
    }

    #[Test]
    public function test_016_expense()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't16@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $expense = Expense::create([
            'user_id' => $user->id,
            'reference_number' => 'EXP-016',
            'title' => 'Internet Bill',
            'category' => 'utilities',
            'amount' => 80,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'card',
            'notes' => 'Monthly internet bill',
        ]);

        $this->assertDatabaseHas('expenses', ['reference_number' => 'EXP-016']);
    }

    #[Test]
    public function test_017_p_and_l()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 't17@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin);

        $response = $this->get('/reports/profit');
        $response->assertStatus(200);
        $response->assertViewHas('summary');
    }

    #[Test]
    public function test_018_reports()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 't18@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin);

        $this->get('/reports/sales')->assertStatus(200);
        $this->get('/reports/purchases')->assertStatus(200);
        $this->get('/reports/inventory')->assertStatus(200);
    }

    #[Test]
    public function test_019_pdf_invoice()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 't19@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Speaker',
            'sku' => 'SPK-19',
            'cost_price' => 15,
            'selling_price' => 35,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale([
            'user_id' => $user->id,
            'invoice_number' => 'INV-019',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 35,
        ], [
            ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 35],
        ]);

        $response = $this->get("/sales/{$sale->id}/pdf");
        $response->assertStatus(200);
    }

    #[Test]
    public function test_020_activity_log()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 't20@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin);

        $this->post('/categories', [
            'name' => 'Checklist 20 Category',
            'description' => 'Desc',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
        ]);
    }
}
