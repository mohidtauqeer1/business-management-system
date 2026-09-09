<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'phone'                => ['nullable', 'string', 'max:20'],
            'email'                => ['nullable', 'email', 'max:255'],
            'address'              => ['nullable', 'string'],
            'credit_limit'         => ['nullable', 'numeric', 'min:0'],
            'credit_limit_enabled' => ['nullable', 'boolean'],
        ]);

        $validated['credit_balance']        = 0;
        $validated['credit_limit']          = $validated['credit_limit'] ?? 0;
        $validated['credit_limit_enabled']  = $request->boolean('credit_limit_enabled');

        $customer = Customer::create($validated);

        ActivityLogger::created('Customer', $customer->id, "Customer '{$customer->name}' created");

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'sales' => function ($query) {
                $query->latest();
            }
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(
        Request $request,
        Customer $customer
    ): RedirectResponse {
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'phone'                => ['nullable', 'string', 'max:20'],
            'email'                => ['nullable', 'email', 'max:255'],
            'address'              => ['nullable', 'string'],
            'credit_limit'         => ['nullable', 'numeric', 'min:0'],
            'credit_limit_enabled' => ['nullable', 'boolean'],
        ]);

        $validated['credit_limit']         = $validated['credit_limit'] ?? 0;
        $validated['credit_limit_enabled'] = $request->boolean('credit_limit_enabled');

        $old = $customer->only(['name', 'credit_limit', 'credit_limit_enabled']);
        $customer->update($validated);

        ActivityLogger::updated(
            'Customer', $customer->id,
            "Customer '{$customer->name}' updated",
            $old,
            $customer->fresh()->only(['name', 'credit_limit', 'credit_limit_enabled'])
        );

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists()) {
            return back()->with(
                'error',
                'Cannot delete a customer that has sales history.'
            );
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}