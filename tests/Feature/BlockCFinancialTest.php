<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Expense;
use App\Services\SaleService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class BlockCFinancialTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function customer_credit_limit_enforcement()
    {
        $user = User::create([
            'name' => 'Manager User',
            'email' => 'fin_mgr@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'John Retailer',
            'phone' => '03009998877',
            'email' => 'john@test.com',
            'address' => 'Main Market',
            'credit_limit_enabled' => true,
            'credit_limit' => 500, // Limit = 500
        ]);

        $category = Category::create(['name' => 'General']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Power Bank',
            'sku' => 'PB-10K',
            'cost_price' => 150,
            'selling_price' => 300,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);

        // Attempting sale of 3 units ($900 total) as unpaid when credit limit is 500
        $saleData = [
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'invoice_number' => 'INV-CREDIT-EXCEED',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 0, // Unpaid amount = 900 > 500 limit
            'discount' => 0,
            'tax' => 0,
        ];
        $saleItems = [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 300, 'discount' => 0],
        ];

        $this->expectException(InvalidArgumentException::class);
        $saleService->createSale($saleData, $saleItems);
    }

    #[Test]
    public function expense_creation_and_category_logging()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'fin_admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $expense = Expense::create([
            'user_id' => $user->id,
            'reference_number' => 'EXP-20260909-0001',
            'title' => 'Office Rent',
            'category' => 'rent',
            'amount' => 1500,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'bank',
            'notes' => 'Monthly rental payment',
        ]);

        $this->assertDatabaseHas('expenses', [
            'reference_number' => 'EXP-20260909-0001',
            'amount' => 1500.00,
            'category' => 'rent',
        ]);
    }
}
