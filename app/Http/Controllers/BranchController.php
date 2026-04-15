<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Traits\HasBranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    use HasBranchScope;

    public function index(): View
    {
        $branches = Branch::withCount(['customers', 'orders', 'expenses'])
            ->with(['managers'])
            ->latest()->get();
        return view('branches.index', compact('branches'));
    }

    public function create(): View
    {
        $managers = User::where('role', 'branch_manager')->orderBy('name')->get();
        return view('branches.create', compact('managers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['nullable', 'string', 'max:500'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'is_active'   => ['boolean'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['exists:users,id'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $managerIds = $data['manager_ids'] ?? [];
        unset($data['manager_ids']);

        $branch = Branch::create($data);

        // Assign selected managers to this branch
        if ($managerIds) {
            User::whereIn('id', $managerIds)->update(['branch_id' => $branch->id]);
        }

        return redirect()->route('branches.index')->with('success', 'Branch created.');
    }

    public function edit(Branch $branch): View
    {
        $managers        = User::where('role', 'branch_manager')->orderBy('name')->get();
        $assignedIds     = $branch->managers->pluck('id')->toArray();
        return view('branches.edit', compact('branch', 'managers', 'assignedIds'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:500'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'manager_ids'   => ['nullable', 'array'],
            'manager_ids.*' => ['exists:users,id'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $managerIds = $data['manager_ids'] ?? [];
        unset($data['manager_ids']);

        $branch->update($data);

        // Remove managers who were previously assigned but now de-selected
        User::where('branch_id', $branch->id)
            ->where('role', 'branch_manager')
            ->whereNotIn('id', $managerIds)
            ->update(['branch_id' => null]);

        // Assign newly selected managers
        if ($managerIds) {
            User::whereIn('id', $managerIds)->update(['branch_id' => $branch->id]);
        }

        return redirect()->route('branches.index')->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        // Unlink managers before deletion
        User::where('branch_id', $branch->id)
            ->where('role', 'branch_manager')
            ->update(['branch_id' => null]);

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted.');
    }
}
