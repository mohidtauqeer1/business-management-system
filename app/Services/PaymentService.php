<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function supplierPaymentForPurchase(
    \App\Models\Purchase $purchase,
    float $amount,
    string $paymentMethod = 'cash',
    ?string $referenceNumber = null,
    ?string $notes = null
): Payment {
    if ($amount <= 0) {
        throw new RuntimeException(
            'Payment amount must be greater than zero.'
        );
    }

    return DB::transaction(function () use (
        $purchase,
        $amount,
        $paymentMethod,
        $referenceNumber,
        $notes
    ) {

        $purchase = \App\Models\Purchase::lockForUpdate()
            ->findOrFail($purchase->id);

        $remaining = (float) $purchase->total_amount
            - (float) $purchase->paid_amount;

        if ($amount > $remaining) {
            throw new RuntimeException(
                "Payment exceeds outstanding amount of {$remaining}."
            );
        }

        $newPaidAmount =
            (float) $purchase->paid_amount + $amount;

        $purchase->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $newPaidAmount >= (float) $purchase->total_amount
                ? 'paid'
                : 'partial',
        ]);

        return Payment::create([
            'user_id' => auth()->id(),
            'supplier_id' => $purchase->supplier_id,
            'customer_id' => null,
            'purchase_id' => $purchase->id,
            'sale_id' => null,
            'type' => 'supplier_payment',
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
        ]);
    });
}

   public function customerPaymentForSale(
    \App\Models\Sale $sale,
    float $amount,
    string $paymentMethod = 'cash',
    ?string $referenceNumber = null,
    ?string $notes = null
): Payment {
    if ($amount <= 0) {
        throw new RuntimeException(
            'Payment amount must be greater than zero.'
        );
    }

    return DB::transaction(function () use (
        $sale,
        $amount,
        $paymentMethod,
        $referenceNumber,
        $notes
    ) {

        $sale = \App\Models\Sale::lockForUpdate()
            ->findOrFail($sale->id);

        $remaining = (float) $sale->total_amount
            - (float) $sale->paid_amount;

        if ($amount > $remaining) {
            throw new RuntimeException(
                "Payment exceeds outstanding amount of {$remaining}."
            );
        }

        $newPaidAmount =
            (float) $sale->paid_amount + $amount;

        $sale->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $newPaidAmount >= (float) $sale->total_amount
                ? 'paid'
                : 'partial',
        ]);

        $sale->customer?->decrement(
            'credit_balance',
            $amount
        );

        if ($sale->customer) {
            $sale->customer->refresh();

            if ($sale->customer->credit_balance < 0) {
                $sale->customer->update([
                    'credit_balance' => 0,
                ]);
            }
        }

        return Payment::create([
            'user_id' => auth()->id(),
            'supplier_id' => null,
            'customer_id' => $sale->customer_id,
            'purchase_id' => null,
            'sale_id' => $sale->id,
            'type' => 'customer_payment',
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
        ]);
    });
}
}