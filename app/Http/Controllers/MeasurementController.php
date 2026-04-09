<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Measurement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeasurementController extends Controller
{
    public function create(Customer $customer): View
    {
        $lastMeasurement = $customer->measurements()->latest()->first();
        return view('measurements.create', compact('customer', 'lastMeasurement'));
    }

    public function store(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'label'          => ['required', 'string', 'max:100'],
            'q_length'       => ['nullable', 'numeric', 'min:0'],
            'q_shoulder'     => ['nullable', 'numeric', 'min:0'],
            'q_chest'        => ['nullable', 'numeric', 'min:0'],
            'q_waist'        => ['nullable', 'numeric', 'min:0'],
            'q_seat'         => ['nullable', 'numeric', 'min:0'],
            'q_sleeve'       => ['nullable', 'numeric', 'min:0'],
            'q_sleeve_width' => ['nullable', 'numeric', 'min:0'],
            'q_collar'       => ['nullable', 'numeric', 'min:0'],
            'q_front'        => ['nullable', 'numeric', 'min:0'],
            'q_back'         => ['nullable', 'numeric', 'min:0'],
            'q_armhole'      => ['nullable', 'numeric', 'min:0'],
            'q_cuff'         => ['nullable', 'numeric', 'min:0'],
            's_length'       => ['nullable', 'numeric', 'min:0'],
            's_waist'        => ['nullable', 'numeric', 'min:0'],
            's_seat'         => ['nullable', 'numeric', 'min:0'],
            's_thigh'        => ['nullable', 'numeric', 'min:0'],
            's_knee'         => ['nullable', 'numeric', 'min:0'],
            's_bottom'       => ['nullable', 'numeric', 'min:0'],
            's_crotch'       => ['nullable', 'numeric', 'min:0'],
            's_ankle'        => ['nullable', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string'],
        ]);

        $data['customer_id'] = $customer->id;
        Measurement::create($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Measurement saved.');
    }

    public function edit(Customer $customer, Measurement $measurement): View
    {
        return view('measurements.edit', compact('customer', 'measurement'));
    }

    public function update(Request $request, Customer $customer, Measurement $measurement): RedirectResponse
    {
        $data = $request->validate([
            'label'          => ['required', 'string', 'max:100'],
            'q_length'       => ['nullable', 'numeric', 'min:0'],
            'q_shoulder'     => ['nullable', 'numeric', 'min:0'],
            'q_chest'        => ['nullable', 'numeric', 'min:0'],
            'q_waist'        => ['nullable', 'numeric', 'min:0'],
            'q_seat'         => ['nullable', 'numeric', 'min:0'],
            'q_sleeve'       => ['nullable', 'numeric', 'min:0'],
            'q_sleeve_width' => ['nullable', 'numeric', 'min:0'],
            'q_collar'       => ['nullable', 'numeric', 'min:0'],
            'q_front'        => ['nullable', 'numeric', 'min:0'],
            'q_back'         => ['nullable', 'numeric', 'min:0'],
            'q_armhole'      => ['nullable', 'numeric', 'min:0'],
            'q_cuff'         => ['nullable', 'numeric', 'min:0'],
            's_length'       => ['nullable', 'numeric', 'min:0'],
            's_waist'        => ['nullable', 'numeric', 'min:0'],
            's_seat'         => ['nullable', 'numeric', 'min:0'],
            's_thigh'        => ['nullable', 'numeric', 'min:0'],
            's_knee'         => ['nullable', 'numeric', 'min:0'],
            's_bottom'       => ['nullable', 'numeric', 'min:0'],
            's_crotch'       => ['nullable', 'numeric', 'min:0'],
            's_ankle'        => ['nullable', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string'],
        ]);

        $measurement->update($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Measurement updated.');
    }
}
