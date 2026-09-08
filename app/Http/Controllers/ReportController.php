<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    // ─── Sales Report ─────────────────────────────────────────────────────────
    public function sales(Request $request): View
    {
        $dateFrom = $request->date_from
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $dateTo = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->payment_status, fn($q) => $q->where('payment_status', $request->payment_status))
            ->latest('sale_date')
            ->get();

        $summary = [
            'total_sales'    => $sales->sum('total_amount'),
            'total_received' => $sales->sum('paid_amount'),
            'outstanding'    => $sales->sum(fn($s) => max(0, $s->total_amount - $s->paid_amount)),
            'count'          => $sales->count(),
        ];

        $customers = Customer::orderBy('name')->get();

        return view('reports.sales', compact('sales', 'summary', 'customers', 'dateFrom', 'dateTo'));
    }

    // ─── Purchases Report ──────────────────────────────────────────────────────
    public function purchases(Request $request): View
    {
        $dateFrom = $request->date_from
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $dateTo = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $purchases = Purchase::with(['supplier', 'user'])
            ->whereBetween('purchase_date', [$dateFrom, $dateTo])
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->payment_status, fn($q) => $q->where('payment_status', $request->payment_status))
            ->latest('purchase_date')
            ->get();

        $summary = [
            'total_purchased' => $purchases->sum('total_amount'),
            'total_paid'      => $purchases->sum('paid_amount'),
            'outstanding'     => $purchases->sum(fn($p) => max(0, $p->total_amount - $p->paid_amount)),
            'count'           => $purchases->count(),
        ];

        $suppliers = Supplier::orderBy('name')->get();

        return view('reports.purchases', compact('purchases', 'summary', 'suppliers', 'dateFrom', 'dateTo'));
    }

    // ─── Inventory Report ──────────────────────────────────────────────────────
    public function inventory(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->stock_status === 'in_stock', fn($q) => $q->whereColumn('stock_quantity', '>', 'reorder_level'))
            ->when($request->stock_status === 'low',      fn($q) => $q->where('stock_quantity', '>', 0)->whereColumn('stock_quantity', '<=', 'reorder_level'))
            ->when($request->stock_status === 'out',      fn($q) => $q->where('stock_quantity', '<=', 0))
            ->orderBy('name')
            ->get();

        $summary = [
            'total_products'  => $products->count(),
            'total_stock_qty' => $products->sum('stock_quantity'),
            'total_value'     => $products->sum(fn($p) => $p->stock_quantity * $p->purchase_price),
            'low_stock_count' => $products->filter(fn($p) => $p->stock_quantity <= $p->reorder_level && $p->stock_quantity > 0)->count(),
            'out_of_stock'    => $products->filter(fn($p) => $p->stock_quantity <= 0)->count(),
        ];

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('reports.inventory', compact('products', 'summary', 'categories'));
    }

    // ─── Profit Report ─────────────────────────────────────────────────────────
    public function profit(Request $request): View
    {
        $dateFrom = $request->date_from
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $dateTo = $request->date_to
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        // Sales in period
        $sales = Sale::with('items.product')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->get();

        // Purchases in period (cost of goods procured)
        $purchases = Purchase::whereBetween('purchase_date', [$dateFrom, $dateTo])->get();

        $totalRevenue   = $sales->sum('total_amount');
        $totalDiscounts = $sales->sum('discount');
        $netRevenue     = $totalRevenue - $totalDiscounts;

        // COGS: sum of (qty × purchase_price) for each sale item
        $cogs = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $cogs += $item->quantity * ($item->product?->purchase_price ?? 0);
            }
        }

        $grossProfit  = $netRevenue - $cogs;
        $grossMargin  = $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0;

        $summary = compact(
            'totalRevenue',
            'totalDiscounts',
            'netRevenue',
            'cogs',
            'grossProfit',
            'grossMargin'
        );

        // Daily breakdown
        $daily = $sales->groupBy(fn($s) => $s->sale_date)
            ->map(fn($daySales) => [
                'date'     => $daySales->first()->sale_date,
                'revenue'  => $daySales->sum('total_amount'),
                'count'    => $daySales->count(),
            ])
            ->sortByDesc('date')
            ->values();

        return view('reports.profit', compact('summary', 'daily', 'sales', 'dateFrom', 'dateTo'));
    }
}
