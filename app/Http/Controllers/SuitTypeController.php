<?php

namespace App\Http\Controllers;

use App\Models\SuitType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuitTypeController extends Controller
{
    public function index(): View
    {
        $suitTypes = SuitType::orderBy('name')->get();
        return view('suit-types.index', compact('suitTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100', 'unique:suit_types,name'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        SuitType::create($data);

        return back()->with('success', "Suit type '{$data['name']}' added.");
    }

    public function update(Request $request, SuitType $suitType): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100', 'unique:suit_types,name,' . $suitType->id],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $suitType->update($data);

        return back()->with('success', 'Suit type updated.');
    }

    public function destroy(SuitType $suitType): RedirectResponse
    {
        $suitType->delete();
        return back()->with('success', "Suit type '{$suitType->name}' deleted.");
    }
}
