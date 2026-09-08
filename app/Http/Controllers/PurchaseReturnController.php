<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseReturnController extends Controller
{
    public function __construct(protected ReturnService $returnService) {}

    public function index(): View
    {
        $returns = PurchaseReturn::with(['purchase', 'user'])
            ->latest()
            ->paginate(15);

        return view('returns.purchases.index', compact('returns'));
    }

    public function create(Purchase $purchase): View
    {
        $purchase->load('items.product', 'supplier');
        return view('returns.purchases.create', compact('purchase'));
    }

    public function store(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $request->validate([
            'return_date'   => ['required', 'date'],
            'credit_amount' => ['required', 'numeric', 'min:0'],
            'credit_status' => ['required', 'in:credited,pending,no_credit'],
            'reason'        => ['nullable', 'string', 'max:500'],
            'notes'         => ['nullable', 'string', 'max:1000'],
            'items'         => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'numeric', 'min:0'],
            'items.*.reason'       => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $return = $this->returnService->processPurchaseReturn($purchase, $data, $data['items']);
            return redirect()
                ->route('returns.purchases.show', $return)
                ->with('success', "Purchase return {$return->return_number} processed successfully.");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(PurchaseReturn $purchaseReturn): View
    {
        $purchaseReturn->load(['items.product', 'purchase.supplier', 'user']);
        return view('returns.purchases.show', compact('purchaseReturn'));
    }
}
