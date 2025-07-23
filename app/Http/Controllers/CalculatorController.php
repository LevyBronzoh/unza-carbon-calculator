<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\BaselineData;
use App\Models\ProjectData;
use App\Models\Calculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Tip;

class CalculatorController extends Controller
{
    // Cache keys
    const CACHE_EMISSION_FACTORS = 'emission_factors';
    const CACHE_STOVE_EFFICIENCIES = 'stove_efficiencies';

    /**
     * Get emission factors with caching
     */
    protected function getEmissionFactors()
    {
        return Cache::rememberForever(self::CACHE_EMISSION_FACTORS, function () {
            return [
                'wood' => 0.001747,
                'charcoal' => 0.00674,
                'lpg' => 0.002983,
                'electricity' => 0.00085, // Zambia grid average
                'kerosene' => 0.002533,
                'ethanol' => 0.001915
            ];
        });
    }

    /**
     * Get stove efficiencies with caching
     */
    protected function getStoveEfficiencies()
    {
        return Cache::rememberForever(self::CACHE_STOVE_EFFICIENCIES, function () {
            return [
                '3_stone_fire' => 0.10,
                'charcoal_brazier' => 0.15,
                'kerosene_stove' => 0.45,
                'lpg_stove' => 0.55,
                'electric_stove' => 0.75,
                'improved_biomass' => 0.25,
                'improved_charcoal' => 0.25,
                'biogas_stove' => 0.60,
                'induction_cooker' => 0.85
            ];
        });
    }

    /**
     * Show calculator dashboard
     */
    public function index()
    {
        try {
            $user = Auth::user();

                        $baselineData = BaselineData::where('user_id', $user->id)->first();
                        $projectData = ProjectData::where('user_id', $user->id)->first();
                        $calculations = Calculation::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);


            $currentEmissions = null;
            if ($baselineData && $projectData) {
                $currentEmissions = $this->calculateEmissionReduction($baselineData, $projectData);
            }

            return view('calculator.index', compact(
                'baselineData',
                'projectData',
                'currentEmissions',
                'calculations'
            ));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error loading calculator data: ' . $e->getMessage());
        }
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'baseline');

        $stoveTypes = [
            '3_stone_fire' => '3-Stone Fire',
            'charcoal_brazier' => 'Charcoal Brazier',
            'kerosene_stove' => 'Kerosene Stove',
            'lpg_stove' => 'LPG Stove',
            'electric_stove' => 'Electric Stove',
            'improved_biomass' => 'Improved Biomass Stove',
            'improved_charcoal' => 'Improved Charcoal Stove',
            'biogas_stove' => 'Biogas Stove',
            'induction_cooker' => 'Induction Cooker',
        ];

        $fuelTypes = [
            'wood' => 'Wood',
            'charcoal' => 'Charcoal',
            'lpg' => 'LPG',
            'electricity' => 'Electricity',
            'kerosene' => 'Kerosene',
            'ethanol' => 'Ethanol',
        ];

        return view('calculator.create', compact('type', 'stoveTypes', 'fuelTypes'));
    }

    /**
     * Store calculation data
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            if ($request->wantsJson()) {
                $monthlyEmissions = $this->calculateBaselineEmissions($validated);
                return response()->json([
                    'monthly' => $monthlyEmissions,
                    'annual' => $monthlyEmissions * 12,
                    'message' => 'Calculation successful'
                ]);
            }

            switch ($validated['calculation_type']) {
                case 'baseline':
                    return $this->storeBaseline($request);
                case 'project':
                    return $this->storeProject($request);
                default:
                    return $this->storeQuickCalculation($request);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Calculation failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate request data
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'calculation_type' => 'required|in:baseline,project,quick',
            'stove_type' => 'required|string',
            'fuel_type' => 'required|string',
            'daily_fuel_use' => 'required|numeric|min:0',
            'daily_cooking_hours' => 'required|numeric|min:0|max:24',
            'household_size' => 'required|integer|min:1',
            'stove_efficiency' => 'nullable|numeric|min:0.01|max:1',
            'start_date' => 'required_if:calculation_type,project|date',
        ], [
            'stove_type.required' => 'Please select your stove type',
            'fuel_type.required' => 'Please select your fuel type',
            'daily_fuel_use.min' => 'Fuel use cannot be negative',
        ]);
    }

    /**
     * Store baseline data
     */
    /**
 * Store baseline data - Fixed version
 */
private function storeBaseline(Request $request)
{
    $stoveType = $request->input('stove_type');
    $fuelType = $request->input('fuel_type');

    // Get efficiency - fix the fallback logic
    $stoveEfficiency = $request->input('stove_efficiency');
    if (!$stoveEfficiency) {
        $efficiencies = $this->getStoveEfficiencies();
        $stoveEfficiency = $efficiencies[$stoveType] ?? 0.10; // Default fallback
    }

    // Get emission factor
    $emissionFactors = $this->getEmissionFactors();
    $emissionFactor = $emissionFactors[$fuelType] ?? 0;

    // Calculate emissions
    $monthlyEmissions = $this->calculateBaselineEmissions($request->all());

    try {
        $baselineData = BaselineData::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'stove_type' => $stoveType,
                'fuel_type' => $fuelType,
                'daily_fuel_use' => (float) $request->input('daily_fuel_use'),
                'daily_cooking_hours' => (float) $request->input('daily_cooking_hours'),
                'household_size' => (int) $request->input('household_size'),
                'stove_efficiency' => (float) $stoveEfficiency,
                'monthly_emissions' => $monthlyEmissions,
                'annual_emissions' => $monthlyEmissions * 12,
                'emission_factor' => $emissionFactor
            ]
        );

        // Create calculation record
        Calculation::create([
            'user_id' => Auth::id(),
            'type' => 'baseline',
            'data' => json_encode($request->except('_token')),
            'monthly_emissions' => $monthlyEmissions,
            'annual_emissions' => $monthlyEmissions * 12,
        ]);

        return redirect()->route('calculator.index')
            ->with('success', 'Baseline data saved! Monthly: ' .
                  number_format($monthlyEmissions, 4) . ' tCO₂e');

    } catch (\Exception $e) {
        Log::error('Failed to save baseline data: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to save baseline data: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Store project data
     */
    private function storeProject(Request $request)
    {
        $baselineData = BaselineData::where('user_id', Auth::id())->first();
        if (!$baselineData) {
            return redirect()->route('calculator.create')
                ->with('error', 'Please set baseline data first');
        }

        $monthlyEmissions = $this->calculateProjectEmissions($request->all());
        $stoveEfficiency = $request->stove_efficiency ?? $this->getStoveEfficiencies()[$request->stove_type];

        // Calculate emission reduction
        $monthlyReduction = $baselineData->monthly_emissions - $monthlyEmissions;
        $percentageReduction = ($monthlyReduction / $baselineData->monthly_emissions) * 100;

        // Calculate total credits
        $monthsElapsed = Carbon::parse($request->input('start_date'))->diffInMonths(now());
        $totalCredits = max(0, $monthlyReduction * $monthsElapsed);

        ProjectData::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'new_stove_type' => $request->input('stove_type'),
            'new_fuel_type' => $request->input('fuel_type'),
            'fuel_use_project' => $request->input('daily_fuel_use'),
            'new_efficiency' => $stoveEfficiency,
            'start_date' => $request->input('start_date'),
            'monthly_emissions' => $monthlyEmissions,
            'annual_emissions' => $monthlyEmissions * 12,
            'monthly_reduction' => $monthlyReduction,
            'percentage_reduction' => $percentageReduction,
            'total_credits' => $totalCredits,
            'emission_factor' => $this->getEmissionFactors()[$request->input('fuel_type')] ?? 0
            ]
        );

        Calculation::create([
            'user_id' => Auth::id(),
            'type' => 'project',
            'data' => json_encode($request->except('_token')),
            'monthly_emissions' => $monthlyEmissions,
            'annual_emissions' => $monthlyEmissions * 12,
            'emission_reduction' => $monthlyReduction,
            'credit_earned' => $monthlyReduction,
        ]);

        return redirect()->route('calculator.index')
            ->with('success', 'Project saved! Reduction: ' .
                  number_format($monthlyReduction, 4) . ' tCO₂e/month (' .
                  number_format($percentageReduction, 1) . '%)');
    }

    /**
     * Store quick calculation
     */
    private function storeQuickCalculation(Request $request)
    {
        $monthlyEmissions = $this->calculateQuickEmissions($request->all());

        Calculation::create([
            'user_id' => Auth::id(),
            'type' => 'quick',
            'data' => json_encode($request->except('_token')),
            'monthly_emissions' => $monthlyEmissions,
            'annual_emissions' => $monthlyEmissions * 12,
        ]);

        return redirect()->route('calculator.index')
            ->with('success', 'Quick calculation: ' .
                  number_format($monthlyEmissions, 4) . ' tCO₂e/month');
    }

    /**
     * Calculate baseline emissions (Verra VM0042)
     */
    private function calculateBaselineEmissions(array $data)
        {
            $monthlyFuel = $data['daily_fuel_use'] * 30;

            $fuelType = $data['fuel_type'] ?? 0;
            $emissionFactor = $this->getEmissionFactors()[$fuelType];

            $stoveType = $data['stove_type'] ?? null;
            $efficiency = $data['stove_efficiency'] ?? $this->getStoveEfficiencies()[$stoveType];

            return round(($monthlyFuel * $emissionFactor) / $efficiency, 6);
        }


    /**
     * Calculate project emissions (Verra VM0042)
     */
    private function calculateProjectEmissions(array $data)
    {
        return $this->calculateBaselineEmissions($data);
    }

    /**
     * Quick calculation
     */
    private function calculateQuickEmissions(array $data)
    {
        return $this->calculateBaselineEmissions($data);
    }

    /**
     * Calculate emission reduction
     */
    private function calculateEmissionReduction($baseline, $project)
    {
        $monthlyReduction = $baseline->monthly_emissions - $project->monthly_emissions;
        $percentage = ($monthlyReduction / $baseline->monthly_emissions) * 100;

        return [
            'monthly_reduction' => round($monthlyReduction, 6),
            'annual_reduction' => round($monthlyReduction * 12, 6),
            'percentage_reduction' => round($percentage, 2)
        ];
    }

    /**
     * Weekly update handler
     */
    public function weeklyUpdate(Request $request)
    {
        $validated = $request->validate([
            'actual_fuel_use' => 'required|numeric|min:0',
            'cooking_hours' => 'required|numeric|min:0|max:24',
            'week_start_date' => 'required|date|before_or_equal:today',
        ]);

        try {
            $user = Auth::user();
            $projectData = ProjectData::where('user_id', $user->id)->firstOrFail();
            $weeklyEmissions = $this->calculateWeeklyEmissions($validated, $projectData);

            Calculation::create([
                'user_id' => $user->id,
                'type' => 'weekly_update',
                'data' => json_encode($validated),
                'weekly_emissions' => $weeklyEmissions,
                'monthly_emissions' => $weeklyEmissions * 4.33,
                'annual_emissions' => $weeklyEmissions * 52,
            ]);

            // Update credits if baseline exists
        if ($baseline = BaselineData::where('user_id', Auth::id())->first())
    {
                $reduction = $this->calculateEmissionReduction(
                    $baseline,
                    ['monthly_emissions' => $weeklyEmissions * 4.33]
                );
                $projectData->increment('total_credits', $reduction['monthly_reduction'] / 4.33);
            }

            return redirect()->route('calculator.index')
                ->with('success', 'Weekly update recorded!')
                ->with('emissions', [
                    'weekly' => $weeklyEmissions,
                    'monthly' => $weeklyEmissions * 4.33
                ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Weekly update failed: ' . $e->getMessage());
        }
    }

    /**
     * Calculate weekly emissions
     */
    private function calculateWeeklyEmissions(array $data, $projectData)
    {
        $weeklyFuel = $data['actual_fuel_use'];
        $emissionFactor = $this->getEmissionFactors()[$projectData->new_fuel_type] ?? 0;
        $efficiency = $projectData->new_efficiency;

        return round(($weeklyFuel * $emissionFactor) / $efficiency, 6);
    }

    /**
     * API endpoint for emission data
     */
    public function getEmissionData()
    {
        return response()->json([
            'emission_factors' => $this->getEmissionFactors(),
            'stove_efficiencies' => $this->getStoveEfficiencies()
        ]);
    }
}
