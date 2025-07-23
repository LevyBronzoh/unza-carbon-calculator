<?php

namespace App\Http\Controllers;

use App\Models\BaselineData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            // Calculate emissions
            $emissions = $this->calculateEmissions(
                $validated['fuel_type'],
                $validated['daily_fuel_use'],
                $validated['efficiency'] ?? null,
                $validated['stove_type']
            );

            // Create baseline data record
            $baselineData = BaselineData::updateOrCreate(
                ['user_id' => Auth::id()], // Unique constraint
                [
                    'stove_type' => $validated['stove_type'],
                    'fuel_type' => $validated['fuel_type'],
                    'daily_fuel_use' => $validated['daily_fuel_use'],
                    'daily_hours' => $validated['daily_hours'],
                    'efficiency' => $validated['efficiency'] ?? $this->getDefaultEfficiency($validated['stove_type']),
                    'household_size' => $validated['household_size'],
                    'monthly_emissions' => $emissions['monthly'],
                    'annual_emissions' => $emissions['annual'],
                    'emission_factor' => self::EMISSION_FACTORS[$validated['fuel_type']]
                ]
            );

            // Log successful creation
            Log::info('Baseline data saved for user: '.Auth::id(), [
                'data' => $baselineData->toArray()
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Baseline data saved successfully!')
                ->with('emissions', [
                    'monthly' => $emissions['monthly'],
                    'annual' => $emissions['annual']
                ]);

        } catch (\Exception $e) {
            Log::error('Error saving baseline data: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error saving data: '.$e->getMessage());
        }
    }

    /**
     * Calculate emissions (AJAX endpoint)
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'fuel_type' => ['required', Rule::in(self::FUEL_TYPES)],
            'daily_fuel_use' => 'required|numeric|min:0',
            'stove_type' => ['required', Rule::in(self::STOVE_TYPES)],
            'efficiency' => 'nullable|numeric|min:0.01|max:1'
        ]);

        try {
            $emissions = $this->calculateEmissions(
                $validated['fuel_type'],
                $validated['daily_fuel_use'],
                $validated['efficiency'] ?? null,
                $validated['stove_type']
            );

            return response()->json([
                'success' => true,
                'monthly_emissions' => $emissions['monthly'],
                'annual_emissions' => $emissions['annual'],
                'efficiency_used' => $validated['efficiency'] ?? $this->getDefaultEfficiency($validated['stove_type']),
                'emission_factor' => self::EMISSION_FACTORS[$validated['fuel_type']]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calculation error: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate the incoming request data
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'stove_type' => ['required', Rule::in(self::STOVE_TYPES)],
            'fuel_type' => ['required', Rule::in(self::FUEL_TYPES)],
            'daily_hours' => 'required|numeric|min:0.1|max:24',
            'daily_fuel_use' => 'required|numeric|min:0.01',
            'efficiency' => 'nullable|numeric|min:0.01|max:1',
            'household_size' => 'required|integer|min:1|max:20',
        ], [
            'stove_type.in' => 'Please select a valid stove type',
            'fuel_type.in' => 'Please select a valid fuel type',
            'daily_hours.max' => 'Daily cooking hours cannot exceed 24 hours',
            'daily_fuel_use.min' => 'Fuel use must be at least 0.01'
        ]);
    }

    /**
     * Calculate emissions using Verra VM0042 methodology
     */
    protected function calculateEmissions($fuelType, $dailyFuelUse, $efficiency, $stoveType)
    {
        $emissionFactor = self::EMISSION_FACTORS[$fuelType] ?? 0.0020;
        $efficiencyValue = $efficiency ?? $this->getDefaultEfficiency($stoveType);

        if ($efficiencyValue <= 0) {
            throw new \Exception('Invalid efficiency value');
        }

        $monthlyFuel = $dailyFuelUse * 30; // 30 days
        $monthlyEmissions = ($monthlyFuel * $emissionFactor) / $efficiencyValue;

        return [
            'monthly' => round($monthlyEmissions, 6),
            'annual' => round($monthlyEmissions * 12, 6)
        ];
    }

    /**
     * Get default efficiency for stove type
     */
    protected function getDefaultEfficiency($stoveType)
    {
        return self::DEFAULT_EFFICIENCIES[$stoveType] ?? 0.10;
    }
}
