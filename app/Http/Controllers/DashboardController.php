<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        // ── Counts ───────────────────────────────────────────
        $totalProducts  = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();
        $totalUsers     = User::count();

        // ── Today's Transactions ──────────────────────────────
        $todaySalesTotal  = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todaySalesCount  = Sale::whereDate('sale_date', $today)->count();
        $todayPurchTotal  = Purchase::whereDate('purchase_date', $today)->sum('total_amount');
        $todayPurchCount  = Purchase::whereDate('purchase_date', $today)->count();

        // ── Low Stock ─────────────────────────────────────────
        $lowStockCount = Product::where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->count();

        // ── Outstanding (unpaid/partial) ─────────────────────
        $unpaidSalesCount     = Sale::whereIn('payment_status', ['unpaid', 'partial'])->count();
        $unpaidPurchasesCount = Purchase::whereIn('payment_status', ['unpaid', 'partial'])->count();

        $totalReceivables = Sale::whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('SUM(total_amount - paid_amount) as balance')
            ->value('balance') ?? 0;

        $totalPayables = Purchase::whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('SUM(total_amount - paid_amount) as balance')
            ->value('balance') ?? 0;

        // ── Recent Transactions ───────────────────────────────
        $recentSales = Sale::with(['customer', 'user'])
            ->latest()
            ->take(6)
            ->get();

        $recentPurchases = Purchase::with(['supplier', 'user'])
            ->latest()
            ->take(6)
            ->get();

        // ── Low Stock Products ────────────────────────────────
        $lowStockProducts = Product::with('category')
            ->where('status', 'active')
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->orderBy('stock_quantity')
            ->take(6)
            ->get();

        // ── This Month Revenue ────────────────────────────────
        $monthStart = Carbon::now()->startOfMonth();
        $monthSalesTotal = Sale::where('sale_date', '>=', $monthStart)->sum('total_amount');
        $monthPurchTotal = Purchase::where('purchase_date', '>=', $monthStart)->sum('total_amount');

        return view('dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalCustomers',
            'totalSuppliers',
            'totalUsers',
            'todaySalesTotal',
            'todaySalesCount',
            'todayPurchTotal',
            'todayPurchCount',
            'lowStockCount',
            'unpaidSalesCount',
            'unpaidPurchasesCount',
            'totalReceivables',
            'totalPayables',
            'recentSales',
            'recentPurchases',
            'lowStockProducts',
            'monthSalesTotal',
            'monthPurchTotal'
        ));
    }
}
