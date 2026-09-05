<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();

        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        $customers = Customer::orderBy('name')->get();

        return view('purchases.create', compact(
            'suppliers',
            'products',
            'customers'
        ));
    }

    public function store(
    StorePurchaseRequest $request,
    PurchaseService $purchaseService
): RedirectResponse {
    
    $data = $request->validated();

    // Temporary: use the first user until authentication is implemented
    $data['user_id'] = \App\Models\User::firstOrFail()->id;

    $items = $data['items'];

    unset($data['items']);

    $purchaseService->createPurchase(
        $data,
        $items
    );

    return redirect()
        ->route('purchases.create')
        ->with('success', 'Purchase created successfully.');
}
public function index(): View
{
    $purchases = Purchase::with(['supplier', 'user'])
        ->latest()
        ->paginate(10);

    return view('purchases.index', compact('purchases'));
}

public function show(Purchase $purchase): View
{
  $purchase->load([
    'supplier',
    'user',
    'items.product',
    'payments.user'
]);

    return view('purchases.show', compact('purchase'));
}

}