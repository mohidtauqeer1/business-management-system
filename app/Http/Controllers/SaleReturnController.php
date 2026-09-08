<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleReturnController extends Controller
{
    public function __construct(protected ReturnService $returnService) {}

    public function index(): View
    {
        $returns = SaleReturn::with(['sale', 'user'])
            ->latest()
            ->paginate(15);

        return view('returns.sales.index', compact('returns'));
    }

    public function create(Sale $sale): View
    {
        $sale->load('items.product', 'customer');
        return view('returns.sales.create', compact('sale'));
    }

    public function store(Request $request, Sale $sale): RedirectResponse
    {
        $data = $request->validate([
            'return_date'   => ['required', 'date'],
            'refund_amount' => ['required', 'numeric', 'min:0'],
            'refund_status' => ['required', 'in:refunded,pending,no_refund'],
            'refund_method' => ['required', 'in:cash,bank,card,credit'],
            'reason'        => ['nullable', 'string', 'max:500'],
            'notes'         => ['nullable', 'string', 'max:1000'],
            'items'         => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'numeric', 'min:0'],
            'items.*.reason'     => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $return = $this->returnService->processSaleReturn($sale, $data, $data['items']);
            return redirect()
                ->route('returns.sales.show', $return)
                ->with('success', "Sale return {$return->return_number} processed successfully.");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(SaleReturn $saleReturn): View
    {
        $saleReturn->load(['items.product', 'sale.customer', 'user']);
        return view('returns.sales.show', compact('saleReturn'));
    }
}
