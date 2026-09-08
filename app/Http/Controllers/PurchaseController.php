<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\InvoiceNumberService;
use App\Services\PurchaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;


class PurchaseController extends Controller
{
    public function create(InvoiceNumberService $invoiceNumbers): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products  = Product::where('status', 'active')->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $nextInvoiceNumber = $invoiceNumbers->nextPurchase();

        return view('purchases.create', compact('suppliers', 'products', 'customers', 'nextInvoiceNumber'));
    }

    public function store(
        StorePurchaseRequest $request,
        PurchaseService $purchaseService
    ): RedirectResponse {

        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $items = $data['items'];

        unset($data['items']);

        $purchaseService->createPurchase($data, $items);

        return redirect()
            ->route('purchases.index')
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
        $purchase->load(['supplier', 'user', 'items.product', 'payments.user']);
        return view('purchases.show', compact('purchase'));
    }

    public function downloadPdf(Purchase $purchase): Response
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        $pdf = Pdf::loadView('pdf.purchase-invoice', compact('purchase'))
            ->setPaper('a4', 'portrait');
        return $pdf->download("purchase-{$purchase->invoice_number}.pdf");
    }
}