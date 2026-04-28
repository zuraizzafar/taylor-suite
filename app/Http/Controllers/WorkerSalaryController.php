<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\WorkerSalaryPayment;
use App\Traits\HasBranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkerSalaryController extends Controller
{
    use HasBranchScope;

    public function store(Request $request, Worker $worker): RedirectResponse
    {
        $data = $request->validate([
            'period_from'  => ['required', 'date'],
            'period_to'    => ['required', 'date', 'after_or_equal:period_from'],
            'total_suits'  => ['required', 'integer', 'min:0'],
            'total_earned' => ['required', 'numeric', 'min:0'],
            'amount_paid'  => ['required', 'numeric', 'min:0'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $data['worker_id'] = $worker->id;
        $data['branch_id'] = $worker->branch_id;
        $data['paid_by']   = auth()->id();
        $data['paid_at']   = now();

        WorkerSalaryPayment::create($data);

        return back()->with('success', 'Salary payment of Rs ' . number_format($data['amount_paid']) . ' recorded.');
    }

    public function destroy(WorkerSalaryPayment $salaryPayment): RedirectResponse
    {
        $worker = $salaryPayment->worker;
        $salaryPayment->delete();

        return redirect()->route('workers.report', $worker)->with('success', 'Salary payment removed.');
    }
}
