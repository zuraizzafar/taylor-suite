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
            'tax_amount'  => ['nullable', 'numeric', 'min:0', 'lte:amount'],
            'supplier_name'       => ['nullable', 'string', 'max:150'],
            'supplier_ntn'        => ['nullable', 'string', 'max:50'],
            'supplier_invoice_no' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'date'        => ['required', 'date'],
        ]);

        $data['tax_amount'] = $data['tax_amount'] ?? 0;

        // Auto-assign branch for branch managers
        if (empty($data['branch_id']) && $this->currentBranchId()) {
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
            'tax_amount'  => ['nullable', 'numeric', 'min:0', 'lte:amount'],
            'supplier_name'       => ['nullable', 'string', 'max:150'],
            'supplier_ntn'        => ['nullable', 'string', 'max:50'],
            'supplier_invoice_no' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'date'        => ['required', 'date'],
        ]);

        $data['tax_amount'] = $data['tax_amount'] ?? 0;
        $expense->update($data);
        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
