<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StitchType;
use App\Traits\HasBranchScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StitchTypeController extends Controller
{
    use HasBranchScope;

    public function index(): View
    {
        $stitchTypes = StitchType::with('branch')->orderBy('name')->get();
        $branches    = Branch::where('is_active', true)->orderBy('name')->get();
        return view('stitch-types.index', compact('stitchTypes', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'branch_id'  => ['nullable', 'exists:branches,id'],
            'is_active'  => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        StitchType::create($data);

        return back()->with('success', "Stitch type '{$data['name']}' added.");
    }

    public function update(Request $request, StitchType $stitchType): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'branch_id'  => ['nullable', 'exists:branches,id'],
            'is_active'  => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $stitchType->update($data);

        return back()->with('success', "Stitch type updated.");
    }

    public function destroy(StitchType $stitchType): RedirectResponse
    {
        if ($stitchType->suits()->exists()) {
            return back()->with('error', "Cannot delete '{$stitchType->name}' — it is assigned to one or more suits.");
        }

        $stitchType->delete();
        return back()->with('success', "Stitch type deleted.");
    }
}
