<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchases;
use App\Models\PurchaseItem;

class PurchaseChainRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_tests_the_full_relationship_chain_with_user_dataset()
    {
        // 1. Electronics Category & Laptops Subcategory
        $electronics = Category::create([
            'name' => 'Electronics',
            'description' => 'Electronic products',
        ]);

        $laptops = Category::create([
            'name' => 'Laptops',
            'description' => 'Laptop computers',
            'parent_id' => $electronics->id,
        ]);

        // 2. Product: HP ProBook 450
        $product = Product::create([
            'category_id' => $electronics->id,
            'name' => 'HP ProBook 450',
            'sku' => 'HP-PB450-001',
            'purchase_price' => 85000,
            'selling_price' => 95000,
            'stock_quantity' => 10,
            'unit' => 'pcs',
            'reorder_level' => 3,
            'status' => 'active',
        ]);

        // 3. User & Supplier
        $user = User::create([
            'name' => 'Ali Manager',
            'email' => 'ali@example.com',
            'password' => bcrypt('password123'),
        ]);

        $supplier = Supplier::create([
            'name' => 'ABC Electronics',
            'contact_person' => 'Ali Khan',
            'phone' => '03001234567',
            'email' => 'abc@example.com',
            'address' => 'Lahore, Pakistan',
        ]);

        // 4. Purchase: ABC-INV-001
        $purchase = Purchases::create([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'ABC-INV-001',
            'total_amount' => 425000,
            'paid_amount' => 425000,
            'payment_status' => 'paid',
        ]);

        // 5. PurchaseItem
        $item = PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 85000,
            'subtotal' => 425000,
        ]);

        // -------------------------------------------------------------
        // VERIFY FORWARD CHAIN
        // -------------------------------------------------------------

        // Supplier -> Purchases
        $this->assertCount(1, $supplier->purchases);
        $this->assertEquals('ABC-INV-001', $supplier->purchases->first()->invoice_number);

        // Purchase -> User
        $this->assertEquals('Ali Manager', $purchase->user->name);

        // Purchase -> PurchaseItems
        $this->assertCount(1, $purchase->purchaseItems);
        $this->assertEquals(425000, $purchase->purchaseItems->first()->subtotal);

        // PurchaseItem -> Product
        $this->assertEquals('HP ProBook 450', $item->product->name);

        // Product -> Category
        $this->assertEquals('Electronics', $product->category->name);

        // Category -> Children
        $this->assertCount(1, $electronics->children);
        $this->assertEquals('Laptops', $electronics->children->first()->name);

        // -------------------------------------------------------------
        // VERIFY REVERSE CHAIN
        // -------------------------------------------------------------

        // Subcategory -> Parent Category
        $this->assertEquals('Electronics', $laptops->parent->name);

        // Product -> PurchaseItems
        $this->assertCount(1, $product->purchaseItems);

        // PurchaseItem -> Purchase
        $this->assertEquals('ABC-INV-001', $item->purchase->invoice_number);

        // Purchase -> Supplier & User
        $this->assertEquals('ABC Electronics', $item->purchase->supplier->name);
        $this->assertEquals('Ali Manager', $item->purchase->user->name);

        // BelongsToMany Pivot Direct Access
        $this->assertEquals('HP ProBook 450', $purchase->products->first()->name);
        $this->assertEquals(5, $purchase->products->first()->pivot->quantity);
    }
}
