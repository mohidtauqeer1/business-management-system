<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Services\SaleService;
use App\Services\PurchaseService;
use App\Services\StockService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class BlockBInventoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function purchase_increases_stock_and_sale_decreases_stock()
    {
        $user = User::create([
            'name' => 'Manager User',
            'email' => 'mgr@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Hardware']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Hard Drive 1TB',
            'sku' => 'HDD-1TB',
            'cost_price' => 50,
            'selling_price' => 80,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'low_stock_threshold' => 2,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Tech Wholesale',
            'phone' => '1234567890',
            'email' => 'tech@supplier.com',
            'address' => 'City Center',
        ]);

        // Purchase +5 units -> Stock should become 15
        $purchaseService = app(PurchaseService::class);
        $purchaseData = [
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'PUR-TEST-001',
            'paid_amount' => 250,
        ];
        $purchaseItems = [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 50],
        ];

        $purchase = $purchaseService->createPurchase($purchaseData, $purchaseItems);

        $product->refresh();
        $this->assertEquals(15, $product->stock_quantity);

        // Sale -3 units -> Stock should become 12
        $saleService = app(SaleService::class);
        $saleData = [
            'user_id' => $user->id,
            'invoice_number' => 'INV-TEST-001',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 240,
            'discount' => 0,
            'tax' => 0,
        ];
        $saleItems = [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 80, 'discount' => 0],
        ];

        $sale = $saleService->createSale($saleData, $saleItems);

        $product->refresh();
        $this->assertEquals(12, $product->stock_quantity);
    }

    #[Test]
    public function negative_stock_protection_blocks_overselling()
    {
        $user = User::create([
            'name' => 'Manager User',
            'email' => 'mgr2@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Laptops']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'MacBook Air',
            'sku' => 'MBA-M2',
            'cost_price' => 1000,
            'selling_price' => 1200,
            'stock_quantity' => 2, // Only 2 in stock
            'unit' => 'pcs',
            'low_stock_threshold' => 1,
            'is_active' => true,
        ]);

        $saleService = app(SaleService::class);
        $saleData = [
            'user_id' => $user->id,
            'invoice_number' => 'INV-OVERSELL',
            'sale_date' => now()->toDateString(),
            'paid_amount' => 0,
        ];
        $saleItems = [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 1200], // Requesting 5!
        ];

        $this->expectException(InvalidArgumentException::class);
        $saleService->createSale($saleData, $saleItems);
    }
}
