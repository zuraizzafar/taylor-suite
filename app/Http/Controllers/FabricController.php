<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Fabric;
use App\Traits\HasBranchScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FabricController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $search = $request->input('search', '');

        $query = Fabric::with('branch')
            ->when($request->input('search'), function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('roll_number', 'like', "%{$s}%")
                        ->orWhere('color', 'like', "%{$s}%")
                        ->orWhere('fabric_type', 'like', "%{$s}%")
                        ->orWhere('design_code', 'like', "%{$s}%");
                });
            });

        $this->branchQuery($query);

        $fabrics = $query->latest()->paginate(20)->withQueryString();

        $statsBase = fn() => $this->branchQuery(Fabric::query());

        $stats = [
            'total_rolls'       => (clone $statsBase())->count(),
            'total_meters'      => (float) (clone $statsBase())->sum('available_meter'),
            'low_stock_items'   => (clone $statsBase())->whereIn('status', ['low_stock', 'out_of_stock'])->count(),
            'today_sales'       => (float) $this->branchQuery(\App\Models\FabricSale::query())->whereDate('created_at', today())->sum('total_amount'),
            'total_stock_value' => (float) (clone $statsBase())->get()->sum(fn($f) => $f->available_meter * $f->cost_price),
            'profit'            => (float) $this->branchQuery(\App\Models\FabricSale::query())->get()->sum(fn($s) => ($s->rate - ($s->fabric?->cost_price ?? 0)) * $s->meter),
        ];

        return view('fabrics.index', compact('fabrics', 'stats', 'search'));
    }

    public function create(): View
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('fabrics.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fabric_type'    => ['required', 'string', 'max:100'],
            'brand'          => ['nullable', 'string', 'max:100'],
            'color'          => ['required', 'string', 'max:50'],
            'design_code'    => ['nullable', 'string', 'max:50'],
            'roll_number'    => ['required', 'string', 'max:50', 'unique:fabrics,roll_number'],
            'total_meter'    => ['required', 'numeric', 'min:0.1'],
            'cost_price'     => ['required', 'numeric', 'min:0'],
            'sale_price'     => ['required', 'numeric', 'min:0'],
            'supplier'       => ['nullable', 'string', 'max:150'],
            'purchase_date'  => ['nullable', 'date'],
            'branch_id'      => ['nullable', 'exists:branches,id'],
        ]);

        if ($branchId = $this->currentBranchId()) {
            $data['branch_id'] = $branchId;
        }

        $data['available_meter'] = $data['total_meter'];

        $fabric = Fabric::create($data);

        $fabric->qr_code_path = $this->generateQrCode($fabric);
        $fabric->saveQuietly();

        $fabric->movements()->create([
            'type'  => 'added',
            'meter' => $data['total_meter'],
            'note'  => 'Initial stock',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('fabrics.index')->with('success', "Fabric roll {$fabric->roll_number} added.");
    }

    public function edit(Fabric $fabric): View
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('fabrics.edit', compact('fabric', 'branches'));
    }

    public function update(Request $request, Fabric $fabric): RedirectResponse
    {
        $data = $request->validate([
            'fabric_type'    => ['required', 'string', 'max:100'],
            'brand'          => ['nullable', 'string', 'max:100'],
            'color'          => ['required', 'string', 'max:50'],
            'design_code'    => ['nullable', 'string', 'max:50'],
            'roll_number'    => ['required', 'string', 'max:50', 'unique:fabrics,roll_number,' . $fabric->id],
            'cost_price'     => ['required', 'numeric', 'min:0'],
            'sale_price'     => ['required', 'numeric', 'min:0'],
            'supplier'       => ['nullable', 'string', 'max:150'],
            'purchase_date'  => ['nullable', 'date'],
            'branch_id'      => ['nullable', 'exists:branches,id'],
        ]);

        $fabric->update($data);

        return redirect()->route('fabrics.index')->with('success', 'Fabric updated.');
    }

    public function destroy(Fabric $fabric): RedirectResponse
    {
        $fabric->delete();
        return redirect()->route('fabrics.index')->with('success', 'Fabric archived.');
    }

    public function addMeter(Request $request, Fabric $fabric): RedirectResponse
    {
        $data = $request->validate([
            'meter' => ['required', 'numeric', 'min:0.1'],
            'note'  => ['nullable', 'string', 'max:255'],
        ]);

        $fabric->total_meter = (float) $fabric->total_meter + $data['meter'];
        $fabric->available_meter = (float) $fabric->available_meter + $data['meter'];
        $fabric->save();

        $fabric->movements()->create([
            'type'  => 'added',
            'meter' => $data['meter'],
            'note'  => $data['note'] ?? 'Manual stock addition',
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', "Added {$data['meter']}m to {$fabric->roll_number}.");
    }

    public function reduceMeter(Request $request, Fabric $fabric): RedirectResponse
    {
        $data = $request->validate([
            'meter' => ['required', 'numeric', 'min:0.1'],
            'reason' => ['required', 'in:damage,adjustment'],
            'note'  => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['meter'] > (float) $fabric->available_meter) {
            return back()->with('error', "Only {$fabric->available_meter}m available — cannot reduce by {$data['meter']}m.");
        }

        $fabric->available_meter = (float) $fabric->available_meter - $data['meter'];
        $fabric->save();

        $fabric->movements()->create([
            'type'  => $data['reason'],
            'meter' => $data['meter'],
            'note'  => $data['note'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', "Reduced {$fabric->roll_number} by {$data['meter']}m.");
    }

    public function history(Fabric $fabric): View
    {
        $movements = $fabric->movements()->with('user')->latest()->paginate(30);
        return view('fabrics.history', compact('fabric', 'movements'));
    }

    public function sticker(Fabric $fabric): Response
    {
        $qrImage = null;
        if ($fabric->qr_code_path && Storage::disk('public')->exists($fabric->qr_code_path)) {
            $qrImage = base64_encode(Storage::disk('public')->get($fabric->qr_code_path));
        }

        $pdf = Pdf::loadView('fabrics.sticker', compact('fabric', 'qrImage'))
            ->setPaper([0, 0, 226.77, 226.77]); // ~80mm square sticker

        $filename = "fabric-{$fabric->roll_number}.pdf";

        return env('PDF_MODE', 'download') === 'stream'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }

    public function lookup(Request $request)
    {
        $q = $request->input('q', '');
        $fabric = Fabric::where('roll_number', $q)->orWhere('id', $q)->first();

        if (! $fabric) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'           => true,
            'id'              => $fabric->id,
            'roll_number'     => $fabric->roll_number,
            'fabric_type'     => $fabric->fabric_type,
            'color'           => $fabric->color,
            'available_meter' => (float) $fabric->available_meter,
            'sale_price'      => (float) $fabric->sale_price,
        ]);
    }

    private function generateQrCode(Fabric $fabric): string
    {
        $content = route('fabric-sales.create') . '?roll=' . $fabric->roll_number;
        $path    = "qrcodes/fabric-{$fabric->roll_number}.svg";

        Storage::disk('public')->makeDirectory('qrcodes');

        $svg = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($content);

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
