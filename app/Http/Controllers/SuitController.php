<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Suit;
use App\Models\Worker;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuitController extends Controller
{
    public function index(Request $request): View
    {
        $query = Suit::with(['customer', 'worker', 'order'])
            ->when($request->input('status'), fn($q, $s) => $q->where('status', $s))
            ->when($request->input('search'), function ($q, $s) {
                $q->where('suit_code', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($cq) =>
                      $cq->where('name', 'like', "%{$s}%")
                         ->orWhere('mobile', 'like', "%{$s}%")
                  );
            });

        $suits = $query->latest()->paginate(25)->withQueryString();

        return view('suits.index', compact('suits'));
    }

    public function create(Request $request): View
    {
        $customers    = Customer::orderBy('name')->get();
        $workers      = Worker::where('is_active', true)->get();
        $selectedCustomer = $request->input('customer_id')
            ? Customer::with('measurements')->find($request->input('customer_id'))
            : null;
        $selectedOrder = $request->input('order_id')
            ? Order::find($request->input('order_id'))
            : null;

        return view('suits.create', compact('customers', 'workers', 'selectedCustomer', 'selectedOrder'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id'       => ['required', 'exists:customers,id'],
            'order_id'          => ['nullable', 'exists:orders,id'],
            'measurement_id'    => ['nullable', 'exists:measurements,id'],
            'worker_id'         => ['nullable', 'exists:workers,id'],
            'suit_type'         => ['required', 'string', 'max:100'],
            'fabric_meter'      => ['required', 'numeric', 'min:0.1'],
            'fabric_description'=> ['nullable', 'string', 'max:255'],
            'notes'             => ['nullable', 'string'],
        ]);

        // Generate suit_code atomically
        $suit = DB::transaction(function () use ($data) {
            $customer   = Customer::lockForUpdate()->find($data['customer_id']);
            $suitNumber = $customer->suits()->count() + 1;

            $data['suit_number'] = $suitNumber;
            $data['suit_code']   = $customer->file_number . '-' . $suitNumber;
            $data['status']      = 'pending';

            $suit = Suit::create($data);

            // Generate QR code
            $suit->qr_code_path = $this->generateQrCode($suit, $customer);
            $suit->saveQuietly();

            return $suit;
        });

        return redirect()->route('suits.show', $suit)
            ->with('success', "Suit {$suit->suit_code} created.");
    }

    public function show(Suit $suit): View
    {
        $suit->load(['customer', 'order', 'worker', 'measurement']);
        $workers = Worker::where('is_active', true)->get();

        return view('suits.show', compact('suit', 'workers'));
    }

    public function edit(Suit $suit): View
    {
        $workers     = Worker::where('is_active', true)->get();
        $measurements = $suit->customer->measurements;

        return view('suits.edit', compact('suit', 'workers', 'measurements'));
    }

    public function update(Request $request, Suit $suit): RedirectResponse
    {
        $data = $request->validate([
            'measurement_id'    => ['nullable', 'exists:measurements,id'],
            'worker_id'         => ['nullable', 'exists:workers,id'],
            'suit_type'         => ['required', 'string', 'max:100'],
            'fabric_meter'      => ['required', 'numeric', 'min:0.1'],
            'fabric_description'=> ['nullable', 'string', 'max:255'],
            'notes'             => ['nullable', 'string'],
        ]);

        $suit->update($data);

        return redirect()->route('suits.show', $suit)
            ->with('success', 'Suit updated.');
    }

    public function updateStatus(Request $request, Suit $suit): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Suit::STATUSES)],
        ]);

        // Once delivered, lock the record
        if ($suit->status === 'delivered') {
            return back()->with('error', 'Delivered suits cannot be changed.');
        }

        if ($data['status'] === 'delivered') {
            $suit->delivered_at = now();
        }

        $suit->update($data);

        return back()->with('success', "Status updated to " . ucfirst($data['status']) . ".");
    }

    public function assignWorker(Request $request, Suit $suit): RedirectResponse
    {
        $data = $request->validate([
            'worker_id' => ['nullable', 'exists:workers,id'],
        ]);

        $suit->update($data);

        return back()->with('success', 'Worker assigned.');
    }

    public function tag(Suit $suit): Response
    {
        $suit->load(['customer', 'worker']);

        $qrImage = null;
        if ($suit->qr_code_path && Storage::disk('public')->exists($suit->qr_code_path)) {
            $qrImage = base64_encode(Storage::disk('public')->get($suit->qr_code_path));
        }

        $pdf = Pdf::loadView('suits.tag-pdf', compact('suit', 'qrImage'))
            ->setPaper([0, 0, 226.77, 226.77]); // ~8cm x 8cm

        return $pdf->download("tag-{$suit->suit_code}.pdf");
    }

    public function destroy(Suit $suit): RedirectResponse
    {
        if ($suit->qr_code_path) {
            Storage::disk('public')->delete($suit->qr_code_path);
        }
        $suit->delete();

        return redirect()->route('suits.index')->with('success', 'Suit deleted.');
    }

    private function generateQrCode(Suit $suit, Customer $customer): string
    {
        $content = "{$suit->suit_code} | {$customer->name} | {$customer->mobile}";
        $path    = "qrcodes/{$suit->suit_code}.svg";

        Storage::disk('public')->makeDirectory('qrcodes');

        $svg = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($content);

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
