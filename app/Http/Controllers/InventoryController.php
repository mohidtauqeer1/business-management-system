<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        if ($request->filled('stock_status')) {

            if ($request->stock_status === 'low') {
                $query->whereColumn(
                    'stock_quantity',
                    '<=',
                    'reorder_level'
                );
            }

            if ($request->stock_status === 'out') {
                $query->where(
                    'stock_quantity',
                    '<=',
                    0
                );
            }

            if ($request->stock_status === 'in_stock') {
                $query->whereColumn(
                    'stock_quantity',
                    '>',
                    'reorder_level'
                );
            }
        }

        $products = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = \App\Models\Category::orderBy('name')
            ->get();

        return view(
            'inventory.index',
            compact('products', 'categories')
        );
    }
    public function lowStock(): View
{
    $products = Product::with('category')
        ->whereColumn('stock_quantity', '<=', 'reorder_level')
        ->where('status', 'active')
        ->orderBy('stock_quantity')
        ->get();

    return view('inventory.low-stock', compact('products'));
}
}