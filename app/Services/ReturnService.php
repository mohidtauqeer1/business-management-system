<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReturnService
{
    public function __construct(
        protected StockService $stockService,
        protected InvoiceNumberService $invoiceNumberService
    ) {}

    // ═══════════════════════════════════════════════════════════════
    // Sale Return — Customer returns item(s) → stock increases
    // ═══════════════════════════════════════════════════════════════

    public function processSaleReturn(Sale $sale, array $data, array $items): SaleReturn
    {
        return DB::transaction(function () use ($sale, $data, $items) {

            $totalAmount = 0;
            $processedItems = [];

            foreach ($items as $item) {
                if (empty($item['quantity']) || (float) $item['quantity'] <= 0) {
                    continue; // skip rows with 0 qty
                }

                // Find the original sale item
                $saleItem = $sale->items()->where('product_id', $item['product_id'])->firstOrFail();

                $qty = (float) $item['quantity'];

                // Cannot return more than was sold
                if ($qty > $saleItem->quantity) {
                    throw new InvalidArgumentException(
                        "Return quantity ({$qty}) exceeds sold quantity ({$saleItem->quantity}) for {$saleItem->product->name}."
                    );
                }

                $unitPrice = (float) $saleItem->unit_price;
                $subtotal  = $qty * $unitPrice;
                $totalAmount += $subtotal;

                $processedItems[] = [
                    'product_id'  => $saleItem->product_id,
                    'sale_item_id' => $saleItem->id,
                    'quantity'    => $qty,
                    'unit_price'  => $unitPrice,
                    'subtotal'    => $subtotal,
                    'reason'      => $item['reason'] ?? null,
                ];
            }

            if (empty($processedItems)) {
                throw new InvalidArgumentException('No valid items to return.');
            }

            // Create the return header
            $saleReturn = SaleReturn::create([
                'return_number' => $this->invoiceNumberService->nextSaleReturn(),
                'sale_id'       => $sale->id,
                'user_id'       => auth()->id(),
                'return_date'   => $data['return_date'] ?? today()->toDateString(),
                'total_amount'  => $totalAmount,
                'refund_amount' => (float) ($data['refund_amount'] ?? $totalAmount),
                'refund_status' => $data['refund_status'] ?? 'refunded',
                'refund_method' => $data['refund_method'] ?? 'cash',
                'reason'        => $data['reason'] ?? null,
                'notes'         => $data['notes'] ?? null,
            ]);

            // Create items and increase stock
            foreach ($processedItems as $itemData) {
                $itemData['sale_return_id'] = $saleReturn->id;
                SaleReturnItem::create($itemData);

                $this->stockService->increase(
                    \App\Models\Product::findOrFail($itemData['product_id']),
                    $itemData['quantity'],
                    'sale_return',
                    SaleReturn::class,
                    $saleReturn->id,
                    auth()->id(),
                    "Stock returned via {$saleReturn->return_number}"
                );
            }

            return $saleReturn->load(['items.product', 'sale.customer']);
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // Purchase Return — We return item(s) to supplier → stock decreases
    // ═══════════════════════════════════════════════════════════════

    public function processPurchaseReturn(Purchase $purchase, array $data, array $items): PurchaseReturn
    {
        return DB::transaction(function () use ($purchase, $data, $items) {

            $totalAmount = 0;
            $processedItems = [];

            foreach ($items as $item) {
                if (empty($item['quantity']) || (float) $item['quantity'] <= 0) {
                    continue;
                }

                $purchaseItem = $purchase->items()->where('product_id', $item['product_id'])->firstOrFail();

                $qty = (float) $item['quantity'];

                if ($qty > $purchaseItem->quantity) {
                    throw new InvalidArgumentException(
                        "Return quantity ({$qty}) exceeds purchased quantity ({$purchaseItem->quantity}) for {$purchaseItem->product->name}."
                    );
                }

                $unitPrice = (float) $purchaseItem->unit_price;
                $subtotal  = $qty * $unitPrice;
                $totalAmount += $subtotal;

                $processedItems[] = [
                    'product_id'      => $purchaseItem->product_id,
                    'purchase_item_id' => $purchaseItem->id,
                    'quantity'        => $qty,
                    'unit_price'      => $unitPrice,
                    'subtotal'        => $subtotal,
                    'reason'          => $item['reason'] ?? null,
                ];
            }

            if (empty($processedItems)) {
                throw new InvalidArgumentException('No valid items to return.');
            }

            $purchaseReturn = PurchaseReturn::create([
                'return_number' => $this->invoiceNumberService->nextPurchaseReturn(),
                'purchase_id'   => $purchase->id,
                'user_id'       => auth()->id(),
                'return_date'   => $data['return_date'] ?? today()->toDateString(),
                'total_amount'  => $totalAmount,
                'credit_amount' => (float) ($data['credit_amount'] ?? $totalAmount),
                'credit_status' => $data['credit_status'] ?? 'pending',
                'reason'        => $data['reason'] ?? null,
                'notes'         => $data['notes'] ?? null,
            ]);

            foreach ($processedItems as $itemData) {
                $itemData['purchase_return_id'] = $purchaseReturn->id;
                PurchaseReturnItem::create($itemData);

                $this->stockService->decrease(
                    \App\Models\Product::findOrFail($itemData['product_id']),
                    $itemData['quantity'],
                    'purchase_return',
                    PurchaseReturn::class,
                    $purchaseReturn->id,
                    auth()->id(),
                    "Stock returned to supplier via {$purchaseReturn->return_number}"
                );
            }

            return $purchaseReturn->load(['items.product', 'purchase.supplier']);
        });
    }
}
