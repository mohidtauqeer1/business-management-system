<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $lastMonth  = Carbon::now()->subMonth()->startOfMonth();

        // ── Counts ───────────────────────────────────────────────────────────
        $totalProducts  = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();
        $totalUsers     = User::count();

        // ── Today's Transactions ──────────────────────────────────────────────
        $todaySalesTotal = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todaySalesCount = Sale::whereDate('sale_date', $today)->count();
        $todayPurchTotal = Purchase::whereDate('purchase_date', $today)->sum('total_amount');
        $todayPurchCount = Purchase::whereDate('purchase_date', $today)->count();

        // ── Low Stock ─────────────────────────────────────────────────────────
        $lowStockCount = Product::where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->count();

        // ── Outstanding ───────────────────────────────────────────────────────
        $unpaidSalesCount     = Sale::whereIn('payment_status', ['unpaid', 'partial'])->count();
        $unpaidPurchasesCount = Purchase::whereIn('payment_status', ['unpaid', 'partial'])->count();

        $totalReceivables = Sale::whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('SUM(total_amount - paid_amount) as balance')
            ->value('balance') ?? 0;

        $totalPayables = Purchase::whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('SUM(total_amount - paid_amount) as balance')
            ->value('balance') ?? 0;

        // ── Recent Transactions ───────────────────────────────────────────────
        $recentSales = Sale::with(['customer', 'user'])->latest()->take(6)->get();
        $recentPurchases = Purchase::with(['supplier', 'user'])->latest()->take(6)->get();

        // ── Low Stock Products ────────────────────────────────────────────────
        $lowStockProducts = Product::with('category')
            ->where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->orderBy('stock_quantity')
            ->take(6)->get();

        // ── This Month Totals ─────────────────────────────────────────────────
        $monthSalesTotal = Sale::where('sale_date', '>=', $monthStart)->sum('total_amount');
        $monthPurchTotal = Purchase::where('purchase_date', '>=', $monthStart)->sum('total_amount');
        $monthExpenses   = Expense::where('expense_date', '>=', $monthStart)->sum('amount');
        $monthNetProfit  = $monthSalesTotal - $monthPurchTotal - $monthExpenses;

        // ── Chart 1: Last 30 days sales vs purchases (daily) ─────────────────
        $salesLast30 = Sale::selectRaw('DATE(sale_date) as date, SUM(total_amount) as total')
            ->where('sale_date', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')->orderBy('date')->pluck('total', 'date');

        $purchaseLast30 = Purchase::selectRaw('DATE(purchase_date) as date, SUM(total_amount) as total')
            ->where('purchase_date', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')->orderBy('date')->pluck('total', 'date');

        // Build complete 30-day date array
        $chartDates     = [];
        $chartSales     = [];
        $chartPurchases = [];
        for ($i = 29; $i >= 0; $i--) {
            $dateKey          = now()->subDays($i)->format('Y-m-d');
            $chartDates[]     = now()->subDays($i)->format('d M');
            $chartSales[]     = (float) ($salesLast30[$dateKey] ?? 0);
            $chartPurchases[] = (float) ($purchaseLast30[$dateKey] ?? 0);
        }

        // ── Chart 2: Top 10 products by revenue this month ────────────────────
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw('products.name, SUM(sale_items.subtotal) as revenue, SUM(sale_items.quantity) as qty_sold')
            ->where('sales.sale_date', '>=', $monthStart)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // ── Chart 3: Payment status breakdown ─────────────────────────────────
        $paymentBreakdown = [
            'paid'    => Sale::where('payment_status', 'paid')->count(),
            'partial' => Sale::where('payment_status', 'partial')->count(),
            'unpaid'  => Sale::where('payment_status', 'unpaid')->count(),
        ];

        // ── Chart 4: Expenses by category (this month) ────────────────────────
        $expenseByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->where('expense_date', '>=', $monthStart)
            ->groupBy('category')
            ->pluck('total', 'category');

        // ── Credit limit alerts ────────────────────────────────────────────────
        // Note: 'balance' is a PHP accessor (computed from sales), not a DB column.
        // We filter in PHP after loading to avoid the SQL error.
        $nearLimitCustomers = Customer::where('credit_limit_enabled', true)
            ->where('credit_limit', '>', 0)
            ->get()
            ->filter(fn($c) => $c->balance >= $c->credit_limit * 0.8)
            ->sortByDesc('balance')
            ->take(5)
            ->values();

        return view('dashboard', compact(
            'totalProducts', 'activeProducts', 'totalCustomers', 'totalSuppliers', 'totalUsers',
            'todaySalesTotal', 'todaySalesCount', 'todayPurchTotal', 'todayPurchCount',
            'lowStockCount', 'unpaidSalesCount', 'unpaidPurchasesCount',
            'totalReceivables', 'totalPayables',
            'recentSales', 'recentPurchases', 'lowStockProducts',
            'monthSalesTotal', 'monthPurchTotal', 'monthExpenses', 'monthNetProfit',
            'chartDates', 'chartSales', 'chartPurchases',
            'topProducts', 'paymentBreakdown', 'expenseByCategory',
            'nearLimitCustomers'
        ));
    }
}
