<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Supplier;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Purchase;
use App\Models\Sale;
class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with([
            'user',
            'supplier',
            'customer',
        ])
            ->latest()
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(): View
{
    $purchases = Purchase::with('supplier')
        ->whereColumn('paid_amount', '<', 'total_amount')
        ->latest()
        ->get();

    $sales = Sale::with('customer')
        ->whereColumn('paid_amount', '<', 'total_amount')
        ->latest()
        ->get();

    return view(
        'payments.create',
        compact('purchases', 'sales')
    );
}

    public function store(
    Request $request,
    PaymentService $paymentService
): RedirectResponse {

    $data = $request->validate([

        'type' => [
            'required',
            'in:supplier_payment,customer_payment'
        ],

        'purchase_id' => [
            'nullable',
            'required_if:type,supplier_payment',
            'exists:purchases,id'
        ],

        'sale_id' => [
            'nullable',
            'required_if:type,customer_payment',
            'exists:sales,id'
        ],

        'amount' => [
            'required',
            'numeric',
            'gt:0'
        ],

        'payment_method' => [
            'required',
            'in:cash,bank,card,online'
        ],

        'reference_number' => [
            'nullable',
            'string',
            'max:100'
        ],

        'notes' => [
            'nullable',
            'string',
            'max:1000'
        ],
    ]);

    if ($data['type'] === 'supplier_payment') {

        $purchase = Purchase::findOrFail(
            $data['purchase_id']
        );

        $paymentService->supplierPaymentForPurchase(
            $purchase,
            (float) $data['amount'],
            $data['payment_method'],
            $data['reference_number'] ?? null,
            $data['notes'] ?? null
        );

    } else {

        $sale = Sale::findOrFail(
            $data['sale_id']
        );

        $paymentService->customerPaymentForSale(
            $sale,
            (float) $data['amount'],
            $data['payment_method'],
            $data['reference_number'] ?? null,
            $data['notes'] ?? null
        );
    }

    return redirect()
        ->route('payments.index')
        ->with('success', 'Payment recorded successfully.');
}
}