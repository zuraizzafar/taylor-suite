<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Traits\HasBranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $query = Expense::with('branch');

        $query = $this->branchQuery($query);

        if ($from = $request->input('from')) {
            $query->whereDate('date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('date', '<=', $to);
        }
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $expenses  = $query->latest('date')->paginate(25)->withQueryString();
        $total     = $query->sum('amount');
        $branches  = Branch::where('is_active', true)->get();

        return view('expenses.index', compact('expenses', 'total', 'branches'));
    }

    public function create(): View
    {
        $branches = Branch::where('is_active', true)->get();
        return view('expenses.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id'   => ['nullable', 'exists:branches,id'],
            'category'    => ['required', 'string', 'max:100'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:500'],
            'date'        => ['required', 'date'],
        ]);

        // Auto-assign branch for branch managers
        if (! $data['branch_id'] && $this->currentBranchId()) {
            $data['branch_id'] = $this->currentBranchId();
        }

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }

    public function edit(Expense $expense): View
    {
        $branches = Branch::where('is_active', true)->get();
        return view('expenses.edit', compact('expense', 'branches'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $data = $request->validate([
            'branch_id'   => ['nullable', 'exists:branches,id'],
            'category'    => ['required', 'string', 'max:100'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:500'],
            'date'        => ['required', 'date'],
        ]);

        $expense->update($data);
        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
