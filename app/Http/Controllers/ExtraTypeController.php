<?php

namespace App\Http\Controllers;

use App\Models\ExtraType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtraTypeController extends Controller
{
    public function index(): View
    {
        $extraTypes = ExtraType::orderBy('name')->get();
        return view('extra-types.index', compact('extraTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:extra_types,name'],
            'default_price' => ['required', 'numeric', 'min:0'],
            'is_active'     => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        ExtraType::create($data);

        return back()->with('success', "Extra type '{$data['name']}' added.");
    }

    public function update(Request $request, ExtraType $extraType): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:extra_types,name,' . $extraType->id],
            'default_price' => ['required', 'numeric', 'min:0'],
            'is_active'     => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $extraType->update($data);

        return back()->with('success', 'Extra type updated.');
    }

    public function destroy(ExtraType $extraType): RedirectResponse
    {
        $extraType->delete();
        return back()->with('success', "Extra type '{$extraType->name}' deleted.");
    }
}
