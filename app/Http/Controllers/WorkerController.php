<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StitchType;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerSalaryPayment;
use App\Models\WorkerStitchRate;
use App\Traits\HasBranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerController extends Controller
{
    use HasBranchScope;

    public function index(): View
    {
        $query = Worker::with(['user', 'branch']);
        $this->branchQuery($query);
        $workers = $query->latest()->get();
        return view('workers.index', compact('workers'));
    }

    public function create(): View
    {
        $users    = User::where('role', 'worker')->doesntHave('worker')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('workers.create', compact('users', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'mobile'      => ['nullable', 'string', 'max:20'],
            'rate_per_suit' => ['nullable', 'numeric', 'min:0'],
            'is_active'   => ['boolean'],
            'user_id'     => ['nullable', 'exists:users,id'],
            'branch_id'   => ['nullable', 'exists:branches,id'],
        ]);

        if (empty($data['branch_id']) && $branchId = $this->currentBranchId()) {
            $data['branch_id'] = $branchId;
        }

        Worker::create($data);

        return redirect()->route('workers.index')
            ->with('success', 'Worker added successfully.');
    }

    public function edit(Worker $worker): View
    {
        $users = User::where('role', 'worker')
            ->where(function ($q) use ($worker) {
                $q->doesntHave('worker')->orWhere('id', $worker->user_id);
            })->get();
        $branches    = Branch::where('is_active', true)->orderBy('name')->get();
        $stitchTypes = StitchType::where('is_active', true)->orderBy('name')->get();
        // Build map of stitch_type_id => override price for this worker
        $rateMap = $worker->stitchRates()->pluck('price', 'stitch_type_id');

        return view('workers.edit', compact('worker', 'users', 'branches', 'stitchTypes', 'rateMap'));
    }

    public function update(Request $request, Worker $worker): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'mobile'        => ['nullable', 'string', 'max:20'],
            'rate_per_suit' => ['nullable', 'numeric', 'min:0'],
            'is_active'     => ['boolean'],
            'user_id'       => ['nullable', 'exists:users,id'],
            'branch_id'     => ['nullable', 'exists:branches,id'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $worker->update($data);

        // Save per-stitch-type override rates
        $rates = $request->input('stitch_rates', []);
        foreach ($rates as $stitchTypeId => $price) {
            if ($price !== null && $price !== '') {
                WorkerStitchRate::updateOrCreate(
                    ['worker_id' => $worker->id, 'stitch_type_id' => (int) $stitchTypeId],
                    ['price' => (float) $price]
                );
            } else {
                WorkerStitchRate::where('worker_id', $worker->id)
                    ->where('stitch_type_id', (int) $stitchTypeId)
                    ->delete();
            }
        }

        return redirect()->route('workers.index')
            ->with('success', 'Worker updated.');
    }

    public function destroy(Worker $worker): RedirectResponse
    {
        $worker->delete();
        return redirect()->route('workers.index')->with('success', 'Worker deleted.');
    }

    public function report(Request $request, Worker $worker): View
    {
        // Period defaults: current month
        $preset  = $request->input('preset', 'month');
        $from    = $request->input('from');
        $to      = $request->input('to');

        if (! $from || ! $to) {
            [$from, $to] = match($preset) {
                'today'  => [today()->toDateString(), today()->toDateString()],
                'week'   => [today()->startOfWeek()->toDateString(), today()->endOfWeek()->toDateString()],
                'month'  => [today()->startOfMonth()->toDateString(), today()->toDateString()],
                default  => [today()->startOfMonth()->toDateString(), today()->toDateString()],
            };
        }

        // Suits stitched in period
        $stitchedSuits = $worker->suits()
            ->with('stitchType', 'customer', 'order')
            ->whereNotNull('stitching_started_at')
            ->whereBetween('stitching_started_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('stitching_started_at')
            ->get();

        $totalSuits   = $stitchedSuits->count();
        $totalEarned  = $stitchedSuits->sum('worker_earning');

        // Pending suits (assigned, not yet stitching)
        $pendingSuits = $worker->suits()
            ->with('customer', 'order', 'stitchType')
            ->whereIn('status', ['pending', 'cutting'])
            ->get();

        // Salary payment history
        $salaryPayments = $worker->salaryPayments()
            ->with('paidBy')
            ->latest('period_to')
            ->get();

        $totalPaid      = $salaryPayments->sum('amount_paid');
        $balanceDue     = max(0, (float) $totalEarned - (float) $totalPaid);

        return view('workers.report', compact(
            'worker', 'stitchedSuits', 'pendingSuits', 'salaryPayments',
            'totalSuits', 'totalEarned', 'totalPaid', 'balanceDue',
            'from', 'to', 'preset'
        ));
    }
}
