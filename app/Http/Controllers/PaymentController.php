<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Supplier;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $suppliers = Supplier::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view(
            'payments.create',
            compact('suppliers', 'customers')
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

            'supplier_id' => [
                'nullable',
                'required_if:type,supplier_payment',
                'exists:suppliers,id'
            ],

            'customer_id' => [
                'nullable',
                'required_if:type,customer_payment',
                'exists:customers,id'
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

            $supplier = Supplier::findOrFail($data['supplier_id']);

            $paymentService->supplierPayment(
                $supplier,
                (float) $data['amount'],
                $data['payment_method'],
                $data['reference_number'] ?? null,
                $data['notes'] ?? null
            );

        } else {

            $customer = Customer::findOrFail($data['customer_id']);

            $paymentService->customerPayment(
                $customer,
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