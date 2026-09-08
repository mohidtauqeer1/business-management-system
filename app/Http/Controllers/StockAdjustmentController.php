<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function create(): View
    {
        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('inventory.adjustment', compact('products'));
    }

    public function store(
        Request $request,
        StockService $stockService
    ): RedirectResponse {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type'       => ['required', 'in:in,out'],
            'quantity'   => ['required', 'numeric', 'gt:0'],
            'reason'     => ['nullable', 'string', 'max:255'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($data['type'] === 'in') {
            $stockService->increase(
                $product,
                (float) $data['quantity'],
                'manual_adjustment',
                null,
                null,
                auth()->id(),
                $data['notes'] ?? ('Manual stock increase. Reason: ' . ($data['reason'] ?? 'Not specified'))
            );
        } else {
            $stockService->decrease(
                $product,
                (float) $data['quantity'],
                'manual_adjustment',
                null,
                null,
                auth()->id(),
                $data['notes'] ?? ('Manual stock decrease. Reason: ' . ($data['reason'] ?? 'Not specified'))
            );
        }


        return redirect()
            ->route('inventory.index')
            ->with('success', 'Stock adjusted successfully.');
    }
}