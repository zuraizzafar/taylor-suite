<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('suits');

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
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'mobile'  => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes'   => ['nullable', 'string'],
        ]);

        $fileData = Customer::nextFileNumber();
        $data = array_merge($data, $fileData);

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
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'mobile'  => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes'   => ['nullable', 'string'],
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
