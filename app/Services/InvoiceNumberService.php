<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;

/**
 * Generates sequential, race-condition-safe invoice numbers.
 *
 * Format:
 *   Sales      → INV-2026-00001
 *   Purchases  → PUR-2026-00001
 *   Sale returns → SRET-2026-00001
 *   Purchase returns → PRET-2026-00001
 */
class InvoiceNumberService
{
    /**
     * Next sales invoice number.
     */
    public function nextSale(): string
    {
        return $this->generate('INV', Sale::class, 'invoice_number');
    }

    /**
     * Next purchase invoice number.
     */
    public function nextPurchase(): string
    {
        return $this->generate('PUR', Purchase::class, 'invoice_number');
    }

    /**
     * Next sale return number.
     */
    public function nextSaleReturn(): string
    {
        return $this->generate('SRET', SaleReturn::class, 'return_number');
    }

    /**
     * Next purchase return number.
     */
    public function nextPurchaseReturn(): string
    {
        return $this->generate('PRET', PurchaseReturn::class, 'return_number');
    }

    /**
     * Core generator — reads the last number from the DB within a
     * shared-lock transaction to prevent duplicate numbers under
     * concurrent requests.
     */
    private function generate(string $prefix, string $model, string $column): string
    {
        return DB::transaction(function () use ($prefix, $model, $column) {
            $year = now()->year;
            $likePattern = "{$prefix}-{$year}-%";

            // Lock the last row for this prefix/year to prevent races
            $last = $model::where($column, 'like', $likePattern)
                ->lockForUpdate()
                ->orderByDesc($column)
                ->value($column);

            if ($last) {
                // Extract the numeric part after the last dash
                $lastNumber = (int) substr($last, strrpos($last, '-') + 1);
                $next = $lastNumber + 1;
            } else {
                $next = 1;
            }

            return sprintf('%s-%d-%05d', $prefix, $year, $next);
        });
    }
}
