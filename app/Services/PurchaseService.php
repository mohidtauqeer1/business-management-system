<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class PurchaseService
{
    public function __construct(
    protected StockService $stockService
) {
}
    public function createPurchase(array $data, array $items): Purchase
    {
        return DB::transaction(function () use ($data, $items) {

            if (empty($items)) {
                throw new \InvalidArgumentException(
                    'A purchase must contain at least one item.'
                );
            }

            $calculatedTotal = 0;
            $processedItems = [];

            // Calculate subtotals and total on the server
            foreach ($items as $item) {

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $subtotal = $quantity * $unitPrice;

                $calculatedTotal += $subtotal;

                $processedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            // Server-controlled total
            $data['total_amount'] = $calculatedTotal;

            // Create purchase
            $purchase = Purchase::create($data);

            // Create items and update stock
            foreach ($processedItems as $itemData) {

                $itemData['purchase_id'] = $purchase->id;

                PurchaseItem::create($itemData);

                // Lock product row while updating stock
                $product = Product::where('id', $itemData['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->stockService->increase(
    $product,
    $itemData['quantity'],
    'purchase',
    Purchase::class,
    $purchase->id,
    auth()->id(),
    'Stock received from purchase.'
);
            }

            return $purchase->load([
                'supplier',
                'user',
                'items.product'
            ]);
        });
    foreach ($items as $item) {

    $quantity = (float) $item['quantity'];
    $unitPrice = (float) $item['unit_price'];

    $subtotal = $quantity * $unitPrice;

    $calculatedTotal += $subtotal;

    $processedItems[] = [
        'product_id' => $item['product_id'],
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'subtotal' => $subtotal,
    ];
}

$paidAmount = (float) ($data['paid_amount'] ?? 0);

if ($paidAmount > $calculatedTotal) {
    throw new \InvalidArgumentException(
        'Paid amount cannot be greater than the purchase total.'
    );
}

if ($paidAmount <= 0) {
    $data['payment_status'] = 'unpaid';
} elseif ($paidAmount >= $calculatedTotal) {
    $data['payment_status'] = 'paid';
} else {
    $data['payment_status'] = 'partial';
}

$data['total_amount'] = $calculatedTotal;
    }
}