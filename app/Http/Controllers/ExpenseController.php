<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::with('user')->latest('expense_date');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses      = $query->paginate(15)->withQueryString();
        $totalFiltered = $query->sum('amount');
        $categories    = Expense::categories();

        // Monthly summary
        $monthTotal = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $categoryTotals = Expense::selectRaw('category, SUM(amount) as total')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('expenses.index', compact(
            'expenses', 'categories', 'monthTotal', 'categoryTotals', 'totalFiltered'
        ));
    }

    public function create(): View
    {
        $categories = Expense::categories();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'expense_date'   => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,card'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id']          = auth()->id();
        $data['reference_number'] = 'EXP-' . now()->year . '-' . str_pad(
            Expense::whereYear('created_at', now()->year)->count() + 1, 5, '0', STR_PAD_LEFT
        );

        $expense = Expense::create($data);

        ActivityLogger::created(
            'Expense',
            $expense->id,
            "Expense '{$expense->title}' recorded — Rs. " . number_format($expense->amount, 0)
        );

        return redirect()->route('expenses.index')
            ->with('success', "Expense {$expense->reference_number} recorded successfully.");
    }

    public function show(Expense $expense): View
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        $categories = Expense::categories();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'expense_date'   => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,card'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $expense->only(['title', 'amount', 'category']);
        $expense->update($data);

        ActivityLogger::updated(
            'Expense',
            $expense->id,
            "Expense '{$expense->title}' updated",
            $old,
            $expense->fresh()->only(['title', 'amount', 'category'])
        );

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        ActivityLogger::deleted('Expense', $expense->id, "Expense '{$expense->title}' deleted");
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted.');
    }
}
