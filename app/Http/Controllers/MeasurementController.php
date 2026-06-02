<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Measurement;
use App\Models\Suit;
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
        $data['meta']        = $this->extractMeta($request);

        $measurement = Measurement::create($data);

        $this->attachMeasurementToSuit($request, $customer, $measurement);

        return $this->redirectAfterSave($request, $customer, 'Measurement saved.');
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

        $data['meta'] = $this->extractMeta($request);
        $measurement->update($data);

        $this->attachMeasurementToSuit($request, $customer, $measurement);

        return $this->redirectAfterSave($request, $customer, 'Measurement updated.');
    }

    private function attachMeasurementToSuit(Request $request, Customer $customer, Measurement $measurement): void
    {
        $suitId = $request->integer('suit_id');
        if (! $suitId) {
            return;
        }

        $suit = Suit::whereKey($suitId)
            ->where('customer_id', $customer->id)
            ->first();

        if ($suit) {
            $suit->update(['measurement_id' => $measurement->id]);
        }
    }

    private function redirectAfterSave(Request $request, Customer $customer, string $message): RedirectResponse
    {
        $redirectTo = $request->input('redirect_to');

        if (is_string($redirectTo) && $redirectTo !== '' && $this->isLocalRedirect($redirectTo)) {
            return redirect()->to($redirectTo)->with('success', $message);
        }

        return redirect()->route('customers.show', $customer)
            ->with('success', $message);
    }

    private function isLocalRedirect(string $redirectTo): bool
    {
        return str_starts_with($redirectTo, '/') || str_starts_with($redirectTo, url('/'));
    }

    private function extractMeta(Request $request): array
    {
        return array_filter([
            'collar_style'    => $request->input('meta_collar_style'),
            'button_type'     => $request->input('meta_button_type'),
            'ghera_style'     => $request->input('meta_ghera_style'),
            'stitching_style' => $request->input('meta_stitching_style'),
            'chak_patti'      => $request->input('meta_chak_patti'),
            'kaj_hale'        => $request->input('meta_kaj_hale'),
            'pahuncha_style'  => $request->input('meta_pahuncha_style'),
            'front_patti_size'=> $request->input('meta_front_patti_size'),
            'design_number'   => $request->input('meta_design_number'),
        ], fn($v) => $v !== null && $v !== '');
    }
}
