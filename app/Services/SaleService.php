<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function createSale(array $data, array $items): Sale
    {
        return DB::transaction(function () use ($data, $items) {

            $itemsTotal = 0;
            $processedItems = [];

            /*
             * 1. Process every product
             */
            foreach ($items as $item) {

                $product = Product::findOrFail($item['product_id']);

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $itemDiscount = (float) ($item['discount'] ?? 0);

                /*
                 * 2. Check stock
                 */
                if ($quantity <= 0) {
                    throw new InvalidArgumentException(
                        'Quantity must be greater than zero.'
                    );
                }

                if ($product->stock_quantity < $quantity) {
                    throw new InvalidArgumentException(
                        "Insufficient stock for {$product->name}."
                    );
                }

                /*
                 * 3. Calculate subtotal
                 */
                $grossSubtotal = $quantity * $unitPrice;

                if ($itemDiscount < 0 || $itemDiscount > $grossSubtotal) {
                    throw new InvalidArgumentException(
                        "Invalid discount for {$product->name}."
                    );
                }

                $subtotal = $grossSubtotal - $itemDiscount;

                $itemsTotal += $subtotal;

                $processedItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $itemDiscount,
                    'subtotal' => $subtotal,
                ];
            }

            /*
             * 4. Overall discount and tax
             */
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);

            if ($discount < 0 || $discount > $itemsTotal) {
                throw new InvalidArgumentException(
                    'Invalid overall discount.'
                );
            }

            if ($tax < 0) {
                throw new InvalidArgumentException(
                    'Tax cannot be negative.'
                );
            }

            /*
             * 5. Calculate final total
             */
            $totalAmount = $itemsTotal - $discount + $tax;

            /*
             * 6. Validate payment
             */
            $paidAmount = (float) ($data['paid_amount'] ?? 0);

            if ($paidAmount < 0) {
                throw new InvalidArgumentException(
                    'Paid amount cannot be negative.'
                );
            }

            if ($paidAmount > $totalAmount) {
                throw new InvalidArgumentException(
                    'Paid amount cannot be greater than the sale total.'
                );
            }

            /*
             * 7. Determine payment status
             */
            if ($paidAmount <= 0) {

                $paymentStatus = 'unpaid';

            } elseif ($paidAmount >= $totalAmount) {

                $paymentStatus = 'paid';

            } else {

                $paymentStatus = 'partial';
            }

            $data['total_amount'] = $totalAmount;
            $data['discount'] = $discount;
            $data['tax'] = $tax;
            $data['paid_amount'] = $paidAmount;
            $data['payment_status'] = $paymentStatus;

            /*
             * 8. Create Sale
             */
            $sale = Sale::create($data);

            /*
             * 9. Create SaleItems and decrease stock
             */
            foreach ($processedItems as $itemData) {

                $itemData['sale_id'] = $sale->id;

                SaleItem::create($itemData);

                Product::where('id', $itemData['product_id'])
                    ->decrement(
                        'stock_quantity',
                        $itemData['quantity']
                    );
            }

            /*
             * 10. Return sale with relationships
             */
            return $sale->load([
                'customer',
                'user',
                'items.product'
            ]);
        });
    }
}