<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\View\View;

class SupplierBalanceController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::select(
                'suppliers.id           as supplier_id',
                'suppliers.name         as supplier_name',
                'suppliers.contact_person',
                'suppliers.phone'
            )
            ->selectRaw('COALESCE(SUM(purchases.total_amount), 0) as total_purchases')
            ->selectRaw('COALESCE(SUM(purchases.paid_amount),  0) as total_paid')
            ->selectRaw('COALESCE(SUM(purchases.total_amount - purchases.paid_amount), 0) as outstanding')
            ->leftJoin('purchases', 'purchases.supplier_id', '=', 'suppliers.id')
            ->groupBy(
                'suppliers.id',
                'suppliers.name',
                'suppliers.contact_person',
                'suppliers.phone'
            )
            ->orderByDesc('outstanding')
            ->get();

        return view('balances.suppliers', compact('suppliers'));
    }
}