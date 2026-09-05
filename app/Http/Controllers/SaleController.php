<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class SaleController extends Controller
{
    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();

        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('sales.create', compact(
            'customers',
            'products'
        ));
    }

    public function store(
        StoreSaleRequest $request,
        SaleService $saleService
    ): RedirectResponse {

        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $sale = $saleService->createSale(
            $data,
            $data['items']
        );

        return redirect()
    ->route('sales.show', $sale)
    ->with('success', 'Sale created successfully.');
    }
    public function index(): View
{
    $sales = Sale::with(['customer', 'user'])
        ->latest()
        ->paginate(10);

    return view('sales.index', compact('sales'));
}
public function show(Sale $sale): View
{
   $sale->load([
    'customer',
    'user',
    'items.product',
    'payments.user'
]);

    return view('sales.show', compact('sale'));
}
}