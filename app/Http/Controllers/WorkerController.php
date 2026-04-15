<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Worker;
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
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('workers.edit', compact('worker', 'users', 'branches'));
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

        return redirect()->route('workers.index')
            ->with('success', 'Worker updated.');
    }

    public function destroy(Worker $worker): RedirectResponse
    {
        $worker->delete();
        return redirect()->route('workers.index')->with('success', 'Worker deleted.');
    }
}
