<?php

namespace App\Http\Controllers;

use App\Models\BaselineData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BaselineDataController extends Controller
{
    // Constants for stove types and fuel types
    protected const STOVE_TYPES = [
        '3-Stone Fire',
        'Charcoal Brazier',
        'Kerosene Stove',
        'LPG Stove',
        'Electric Stove',
        'Improved Biomass Stove'
    ];

    protected const FUEL_TYPES = [
        'Wood',
        'Charcoal',
        'LPG',
        'Electricity',
        'Ethanol',
        'Other'
    ];

    protected const EMISSION_FACTORS = [
        'Wood' => 0.001747,
        'Charcoal' => 0.00674,
        'LPG' => 0.002983,
        'Electricity' => 0.00085,
        'Ethanol' => 0.0015,
        'Other' => 0.0020
    ];

    protected const DEFAULT_EFFICIENCIES = [
        '3-Stone Fire' => 0.10,
        'Charcoal Brazier' => 0.10,
        'Kerosene Stove' => 0.35,
        'LPG Stove' => 0.55,
        'Electric Stove' => 0.75,
        'Improved Biomass Stove' => 0.25,
    ];

    /**
     * Show the form for creating baseline cooking data
     */
    public function create()
    {
        return view('baseline.create', [
            'stoveTypes' => self::STOVE_TYPES,
            'fuelTypes' => self::FUEL_TYPES,
            'defaultEfficiencies' => self::DEFAULT_EFFICIENCIES
        ]);
    }

    /**
     * Store newly created baseline cooking data
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            $emissions = $this->calculateEmissions(
                $validated['fuel_type'],
                $validated['daily_fuel_use'],
                $validated['efficiency'] ?? null,
                $validated['stove_type']
            );

            $baselineData = BaselineData::create([
                'user_id' => Auth::id(),
                'stove_type' => $validated['stove_type'],
                'fuel_type' => $validated['fuel_type'],
                'daily_fuel_use' => $validated['daily_fuel_use'],
                'daily_hours' => $validated['daily_hours'],
                'stove_efficiency' => $validated['efficiency'] ?? $this->getDefaultEfficiency($validated['stove_type']),
                'household_size' => $validated['household_size'],
                'emission_total' => $emissions['monthly'],
                'annual_emission' => $emissions['annual']
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Baseline cooking data saved successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error saving baseline data: ' . $e->getMessage());
        }
    }

    /**
     * Show the edit form for baseline data
     */
    public function edit($id)
    {
        $baselineData = BaselineData::where('user_id', Auth::id())->findOrFail($id);

        return view('baseline.edit', [
            'baseline' => $baselineData,
            'stoveTypes' => self::STOVE_TYPES,
            'fuelTypes' => self::FUEL_TYPES,
            'defaultEfficiencies' => self::DEFAULT_EFFICIENCIES
        ]);
    }

    /**
     * Update the specified baseline data
     */
    public function update(Request $request, $id)
    {
        $baselineData = BaselineData::where('user_id', Auth::id())->findOrFail($id);

        $validated = $this->validateRequest($request);

        try {
            $emissions = $this->calculateEmissions(
                $validated['fuel_type'],
                $validated['daily_fuel_use'],
                $validated['efficiency'] ?? null,
                $validated['stove_type']
            );

            $baselineData->update([
                'stove_type' => $validated['stove_type'],
                'fuel_type' => $validated['fuel_type'],
                'daily_fuel_use' => $validated['daily_fuel_use'],
                'daily_hours' => $validated['daily_hours'],
                'stove_efficiency' => $validated['efficiency'] ?? $this->getDefaultEfficiency($validated['stove_type']),
                'household_size' => $validated['household_size'],
                'emission_total' => $emissions['monthly'],
                'annual_emission' => $emissions['annual']
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Baseline cooking data updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating baseline data: ' . $e->getMessage());
        }
    }

    /**
     * Validate the incoming request data
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'stove_type' => [
                'required',
                'string',
                Rule::in(self::STOVE_TYPES)
            ],
            'fuel_type' => [
                'required',
                'string',
                Rule::in(self::FUEL_TYPES)
            ],
            'daily_hours' => 'required|numeric|min:0|max:24',
            'daily_fuel_use' => 'required|numeric|min:0',
            'efficiency' => 'nullable|numeric|min:0.01|max:1',
            'household_size' => 'required|integer|min:1|max:20',
        ], [
            'stove_type.in' => 'Please select a valid stove type',
            'fuel_type.in' => 'Please select a valid fuel type',
            'daily_hours.max' => 'Daily cooking hours cannot exceed 24 hours'
        ]);
    }

    /**
     * Calculate emissions using Verra VM0042 methodology
     */
    protected function calculateEmissions($fuelType, $dailyFuelUse, $efficiency, $stoveType)
    {
        $emissionFactor = self::EMISSION_FACTORS[$fuelType] ?? 0.0020;
        $efficiencyValue = $efficiency ?? $this->getDefaultEfficiency($stoveType);

        $monthlyFuel = $dailyFuelUse * 30;
        $monthlyEmissions = ($monthlyFuel * $emissionFactor) / $efficiencyValue;

        return [
            'monthly' => round($monthlyEmissions, 4),
            'annual' => round($monthlyEmissions * 12, 4)
        ];
    }

    /**
     * Get default efficiency for stove type
     */
    protected function getDefaultEfficiency($stoveType)
    {
        return self::DEFAULT_EFFICIENCIES[$stoveType] ?? 0.10;
    }

    /**
     * Show success page after baseline submission
     */
    public function showSuccess()
    {
        return view('baseline.success');
    }
}
