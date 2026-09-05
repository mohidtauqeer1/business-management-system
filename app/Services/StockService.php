<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function increase(
        Product $product,
        float $quantity,
        string $type,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use (
            $product,
            $quantity,
            $type,
            $referenceType,
            $referenceId,
            $userId,
            $notes
        ) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            $stockBefore = (float) $product->stock_quantity;

            $stockAfter = $stockBefore + $quantity;

            $product->update([
                'stock_quantity' => $stockAfter,
            ]);

            return StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => $type,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    public function decrease(
        Product $product,
        float $quantity,
        string $type,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use (
            $product,
            $quantity,
            $type,
            $referenceType,
            $referenceId,
            $userId,
            $notes
        ) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            $stockBefore = (float) $product->stock_quantity;

            if ($stockBefore < $quantity) {
                throw new RuntimeException(
                    "Insufficient stock for product: {$product->name}"
                );
            }

            $stockAfter = $stockBefore - $quantity;

            $product->update([
                'stock_quantity' => $stockAfter,
            ]);

            return StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => $type,
                'quantity' => -$quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }
}