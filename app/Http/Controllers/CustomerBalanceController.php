<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;

class CustomerBalanceController extends Controller
{
    public function index(): View
    {
        $customers = Customer::select(
                'customers.id           as customer_id',
                'customers.name         as customer_name',
                'customers.phone',
                'customers.email'
            )
            ->selectRaw('COALESCE(SUM(sales.total_amount), 0)  as total_sales')
            ->selectRaw('COALESCE(SUM(sales.paid_amount),  0)  as total_received')
            ->selectRaw('COALESCE(SUM(sales.total_amount - sales.paid_amount), 0) as outstanding')
            ->leftJoin('sales', 'sales.customer_id', '=', 'customers.id')
            ->groupBy(
                'customers.id',
                'customers.name',
                'customers.phone',
                'customers.email'
            )
            ->orderByDesc('outstanding')
            ->get();

        return view('balances.customers', compact('customers'));
    }
}
