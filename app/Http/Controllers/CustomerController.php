<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Traits\HasBranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $query = Customer::with('branch')->withCount('suits');

        $this->branchQuery($query);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('file_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(20)->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('customers.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'mobile'    => ['required', 'string', 'max:20'],
            'address'   => ['nullable', 'string', 'max:500'],
            'notes'     => ['nullable', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $fileData = Customer::nextFileNumber();
        $data = array_merge($data, $fileData);

        // Auto-stamp branch for branch managers
        if (empty($data['branch_id']) && $branchId = $this->currentBranchId()) {
            $data['branch_id'] = $branchId;
        }

        $customer = Customer::create($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', "Customer {$customer->file_number} created successfully.");
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'measurements',
            'orders.suits.worker',
            'suits.worker',
            'suits.measurement',
            'branch',
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('customers.edit', compact('customer', 'branches'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'mobile'    => ['required', 'string', 'max:20'],
            'address'   => ['nullable', 'string', 'max:500'],
            'notes'     => ['nullable', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted.');
    }
}
