<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Traits\HasBranchScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $query = Customer::with('branch')->withCount('suits')
            ->withSum(['orders as outstanding_balance' => fn($q) => $q->where('balance_amount', '>', 0)], 'balance_amount');

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

        return redirect()->route('measurements.create', $customer)
            ->with('success', "Customer {$customer->file_number} created. Now add their measurements.");
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

    public function pendingTags(Customer $customer): Response|RedirectResponse
    {
        $customer->load(['suits' => function ($query) {
            $query->where('status', 'pending')->with('worker');
        }]);

        if ($customer->suits->isEmpty()) {
            return back()->with('error', 'No pending suits found to print tags for.');
        }

        // Ensure DomPDF font cache directory exists
        $fontCacheDir = storage_path('fonts');
        if (!is_dir($fontCacheDir)) {
            mkdir($fontCacheDir, 0775, true);
        }

        $suitsWithQr = [];
        foreach ($customer->suits as $suit) {
            $qrImage = null;
            if ($suit->qr_code_path && Storage::disk('public')->exists($suit->qr_code_path)) {
                $qrImage = base64_encode(Storage::disk('public')->get($suit->qr_code_path));
            }
            $suitsWithQr[] = [
                'suit' => $suit,
                'qrImage' => $qrImage
            ];
        }

        $pdf = Pdf::loadView('orders.tags-pdf', compact('suitsWithQr'))
            ->setPaper('a4', 'portrait');

        $filename = "pending-tags-{$customer->file_number}.pdf";

        return env('PDF_MODE', 'download') === 'stream'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }

    public function statementPdf(Customer $customer): Response
    {
        $customer->load([
            'orders' => function ($q) {
                $q->with('suits')->latest('order_date');
            },
            'branch',
        ]);

        $settings = \App\Models\Setting::allKeyed();

        $totalOrdersAmount  = (float) $customer->orders->sum('total_amount');
        $totalBalanceAmount = (float) $customer->orders->sum('balance_amount');
        $totalPaidAmount    = max(0, $totalOrdersAmount - $totalBalanceAmount);

        $pdf = Pdf::loadView('customers.statement-pdf', compact('customer', 'settings', 'totalOrdersAmount', 'totalPaidAmount', 'totalBalanceAmount'))
            ->setPaper('a5', 'portrait');

        $filename = "statement-{$customer->file_number}.pdf";

        return env('PDF_MODE', 'download') === 'stream'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }
}
