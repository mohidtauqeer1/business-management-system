<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function createSale(array $data, array $items): Sale
    {
        return DB::transaction(function () use ($data, $items) {

            $itemsTotal     = 0;
            $processedItems = [];

            // 1. Process every product
            foreach ($items as $item) {
                $product      = Product::findOrFail($item['product_id']);
                $quantity     = (float) $item['quantity'];
                $unitPrice    = (float) $item['unit_price'];
                $itemDiscount = (float) ($item['discount'] ?? 0);

                if ($quantity <= 0) {
                    throw new InvalidArgumentException('Quantity must be greater than zero.');
                }

                if ($product->stock_quantity < $quantity) {
                    throw new InvalidArgumentException("Insufficient stock for {$product->name}.");
                }

                $grossSubtotal = $quantity * $unitPrice;

                if ($itemDiscount < 0 || $itemDiscount > $grossSubtotal) {
                    throw new InvalidArgumentException("Invalid discount for {$product->name}.");
                }

                $subtotal     = $grossSubtotal - $itemDiscount;
                $itemsTotal  += $subtotal;

                $processedItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'discount'   => $itemDiscount,
                    'subtotal'   => $subtotal,
                ];
            }

            // 2. Overall discount and tax
            $discount = (float) ($data['discount'] ?? 0);
            $tax      = (float) ($data['tax'] ?? 0);

            if ($discount < 0 || $discount > $itemsTotal) {
                throw new InvalidArgumentException('Invalid overall discount.');
            }
            if ($tax < 0) {
                throw new InvalidArgumentException('Tax cannot be negative.');
            }

            // 3. Final total
            $totalAmount = $itemsTotal - $discount + $tax;

            // 4. Validate payment
            $paidAmount = (float) ($data['paid_amount'] ?? 0);

            if ($paidAmount < 0) {
                throw new InvalidArgumentException('Paid amount cannot be negative.');
            }
            if ($paidAmount > $totalAmount) {
                throw new InvalidArgumentException('Paid amount cannot exceed the sale total.');
            }

            // 5. ── CREDIT LIMIT CHECK ─────────────────────────────────────────
            if (!empty($data['customer_id'])) {
                $customer = Customer::lockForUpdate()->find($data['customer_id']);

                if ($customer && $customer->credit_limit_enabled) {
                    $outstanding       = max(0, $customer->balance ?? 0); // outstanding = unpaid balance
                    $newOutstanding    = $outstanding + ($totalAmount - $paidAmount);
                    $limit             = $customer->credit_limit > 0
                        ? $customer->credit_limit
                        : (float) Setting::get('customer_credit_limit', 0);

                    if ($limit > 0 && $newOutstanding > $limit) {
                        $currency = Setting::get('currency_symbol', 'Rs.');
                        throw new InvalidArgumentException(
                            "Credit limit exceeded for {$customer->name}. " .
                            "Outstanding: {$currency} " . number_format($outstanding, 0) . " | " .
                            "This sale adds: {$currency} " . number_format($totalAmount - $paidAmount, 0) . " | " .
                            "Limit: {$currency} " . number_format($limit, 0) . "."
                        );
                    }
                }
            }

            // 6. Payment status
            $paymentStatus = match (true) {
                $paidAmount <= 0             => 'unpaid',
                $paidAmount >= $totalAmount  => 'paid',
                default                      => 'partial',
            };

            $data['total_amount']   = $totalAmount;
            $data['discount']       = $discount;
            $data['tax']            = $tax;
            $data['paid_amount']    = $paidAmount;
            $data['payment_status'] = $paymentStatus;

            // 7. Create Sale
            $sale = Sale::create($data);

            // 8. Create items and decrease stock
            foreach ($processedItems as $itemData) {
                $itemData['sale_id'] = $sale->id;
                SaleItem::create($itemData);

                $product = Product::findOrFail($itemData['product_id']);

                $this->stockService->decrease(
                    $product,
                    $itemData['quantity'],
                    'sale',
                    Sale::class,
                    $sale->id,
                    auth()->id(),
                    'Stock sold through sale.'
                );
            }

            // 9. Log activity
            ActivityLogger::created(
                'Sale',
                $sale->id,
                "Sale {$sale->invoice_number} created for " .
                ($sale->customer?->name ?? 'Walk-in') .
                " — Rs. " . number_format($totalAmount, 0)
            );

            return $sale->load(['customer', 'user', 'items.product']);
        });
    }
}