<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function createPurchase(array $data, array $items): Purchase
    {
        return DB::transaction(function () use ($data, $items) {

            if (empty($items)) {
                throw new \InvalidArgumentException('A purchase must contain at least one item.');
            }

            $calculatedTotal = 0;
            $processedItems  = [];

            // 1. Calculate subtotals server-side
            foreach ($items as $item) {
                $quantity  = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $subtotal  = $quantity * $unitPrice;

                $calculatedTotal += $subtotal;

                $processedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                ];
            }

            // 2. Validate paid amount
            $paidAmount = (float) ($data['paid_amount'] ?? 0);

            if ($paidAmount > $calculatedTotal) {
                throw new \InvalidArgumentException('Paid amount cannot be greater than the purchase total.');
            }

            // 3. Determine payment status
            $data['total_amount']   = $calculatedTotal;
            $data['paid_amount']    = $paidAmount;
            $data['payment_status'] = match (true) {
                $paidAmount <= 0                => 'unpaid',
                $paidAmount >= $calculatedTotal => 'paid',
                default                         => 'partial',
            };

            // 4. Create purchase header
            $purchase = Purchase::create($data);

            // 5. Create items and update stock
            foreach ($processedItems as $itemData) {
                $itemData['purchase_id'] = $purchase->id;

                PurchaseItem::create($itemData);

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

            // 6. Log activity
            ActivityLogger::created(
                'Purchase',
                $purchase->id,
                "Purchase {$purchase->invoice_number} from " .
                ($purchase->supplier?->name ?? 'Unknown') .
                " — Rs. " . number_format($calculatedTotal, 0)
            );

            return $purchase->load(['supplier', 'user', 'items.product']);
        });
    }
}