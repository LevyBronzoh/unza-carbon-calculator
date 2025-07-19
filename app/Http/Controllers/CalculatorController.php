<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BaselineData;
use App\Models\ProjectData;
use App\Models\Calculation;
use Illuminate\Support\Facades\Cache;

class CalculatorController extends Controller
{
    // Cache keys
    const CACHE_EMISSION_FACTORS = 'emission_factors';
    const CACHE_STOVE_EFFICIENCIES = 'stove_efficiencies';

    protected function getEmissionFactors()
    {
        return Cache::rememberForever(self::CACHE_EMISSION_FACTORS, function () {
            return [
                'wood' => 0.001747,
                'charcoal' => 0.00674,
                'lpg' => 0.002983,
                'electricity' => 0.00085, // Zambia grid average (tCO₂e/kWh)
                'kerosene' => 0.00271,
                'ethanol' => 0.00195,
            ];
        });
    }

    protected function getStoveEfficiencies()
    {
        return Cache::rememberForever(self::CACHE_STOVE_EFFICIENCIES, function () {
            return [
                '3_stone_fire' => 0.10,
                'charcoal_brazier' => 0.10,
                'kerosene_stove' => 0.45,
                'lpg_stove' => 0.55,
                'electric_stove' => 0.75,
                'improved_biomass' => 0.25,
                'improved_charcoal' => 0.25,
                'biogas_stove' => 0.60,
                'induction_cooker' => 0.85,
            ];
        });
    }

    public function index()
    {
        try {
            $user = Auth::user();

            // Get user's baseline and project data
            $baselineData = BaselineData::where('user_id', $user->id)
                ->latest()
                ->first();

            $projectData = ProjectData::where('user_id', $user->id)
                ->latest()
                ->first();

            // Get calculation history
            $calculations = Calculation::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Calculate current emissions if both baseline and project exist
            $currentEmissions = null;
            if ($baselineData && $projectData) {
                $currentEmissions = $this->calculateEmissionReduction($baselineData, $projectData);
            }

            return view('calculator.index', compact(
                'calculations',
                'baselineData',
                'projectData',
                'currentEmissions'
            ));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error loading calculator data: ' . $e->getMessage());
        }
    }

   public function create(Request $request, $type = null)
    {
        // Get type from route parameter, query string, or default to baseline
        $type = $type ?? $request->get('type', 'baseline');

        // Validate type parameter
        if (!in_array($type, ['baseline', 'project'])) {
            $type = 'baseline';
        }

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

        return view('calculator.create', compact('stoveTypes', 'fuelTypes', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            $user = Auth::user();

            // Handle AJAX request
            if ($request->wantsJson()) {
                $monthlyEmissions = $this->calculateBaselineEmissions($validated);
                return response()->json([
                    'monthly' => $monthlyEmissions,
                    'annual' => $monthlyEmissions * 12,
                    'message' => 'Calculation successful'
                ]);
            }

            // Handle regular form submission
            switch ($validated['calculation_type']) {
                case 'baseline':
                    return $this->storeBaseline($request, $user);
                case 'project':
                    return $this->storeProject($request, $user);
                default:
                    return $this->storeQuickCalculation($request, $user);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Calculation failed: ' . $e->getMessage());
        }
    }

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

    private function storeBaseline(Request $request, $user)
    {
        $monthlyEmissions = $this->calculateBaselineEmissions($request->all());
        $stoveEfficiency = $request->stove_efficiency ?? $this->getStoveEfficiencies()[$request->stove_type];

        // Store baseline data
        BaselineData::updateOrCreate(
            ['user_id' => $user->id],
            [
                'stove_type' => $request->stove_type,
                'fuel_type' => $request->fuel_type,
                'daily_fuel_use' => $request->daily_fuel_use,
                'daily_cooking_hours' => $request->daily_cooking_hours,
                'household_size' => $request->household_size,
                'stove_efficiency' => $stoveEfficiency,
                'monthly_emissions' => $monthlyEmissions,
                'annual_emissions' => $monthlyEmissions * 12,
            ]
        );

        // Store calculation record
        Calculation::create([
            'user_id' => $user->id,
            'type' => 'baseline',
            'data' => json_encode($request->except('_token')),
            'monthly_emissions' => $monthlyEmissions,
            'annual_emissions' => $monthlyEmissions * 12,
        ]);

        return redirect()->route('calculator.index')
            ->with('success', 'Baseline data saved successfully!')
            ->with('emissions', [
                'monthly' => $monthlyEmissions,
                'annual' => $monthlyEmissions * 12,
                'type' => 'baseline'
            ]);
    }

    private function storeProject(Request $request, $user)
    {
        // Check if baseline exists
        $baselineData = BaselineData::where('user_id', $user->id)->first();
        if (!$baselineData) {
            return redirect()->route('calculator.create')
                ->with('error', 'Please enter your baseline data first before adding project data.');
        }

        // Calculate project emissions
        $monthlyEmissions = $this->calculateProjectEmissions($request->all());
        $stoveEfficiency = $request->stove_efficiency ?? $this->getStoveEfficiencies()[$request->stove_type];

        // Store project data
        $projectData = ProjectData::updateOrCreate(
            ['user_id' => $user->id],
            [
                'new_stove_type' => $request->stove_type,
                'new_fuel_type' => $request->fuel_type,
                'daily_fuel_use' => $request->daily_fuel_use,
                'daily_cooking_hours' => $request->daily_cooking_hours,
                'stove_efficiency' => $stoveEfficiency,
                'start_date' => $request->start_date,
                'monthly_emissions' => $monthlyEmissions,
                'annual_emissions' => $monthlyEmissions * 12,
            ]
        );

        // Calculate emission reduction
        $emissionReduction = $this->calculateEmissionReduction($baselineData, $projectData);

        // Store calculation record
        Calculation::create([
            'user_id' => $user->id,
            'type' => 'project',
            'data' => json_encode($request->except('_token')),
            'monthly_emissions' => $monthlyEmissions,
            'annual_emissions' => $monthlyEmissions * 12,
            'emission_reduction' => $emissionReduction['monthly_reduction'],
            'credit_earned' => $this->calculateCredits($emissionReduction),
        ]);

        return redirect()->route('calculator.index')
            ->with('success', 'Project data saved successfully!')
            ->with('emissions', [
                'monthly' => $monthlyEmissions,
                'annual' => $monthlyEmissions * 12,
                'reduction' => $emissionReduction,
                'type' => 'project'
            ]);
    }

    private function storeQuickCalculation(Request $request, $user)
    {
        $monthlyEmissions = $this->calculateQuickEmissions($request->all());

        // Store calculation record
        Calculation::create([
            'user_id' => $user->id,
            'type' => 'quick',
            'data' => json_encode($request->except('_token')),
            'monthly_emissions' => $monthlyEmissions,
            'annual_emissions' => $monthlyEmissions * 12,
        ]);

        return redirect()->route('calculator.index')
            ->with('success', 'Quick calculation completed!')
            ->with('emissions', [
                'monthly' => $monthlyEmissions,
                'annual' => $monthlyEmissions * 12,
                'type' => 'quick'
            ]);
    }

    /**
     * Calculate baseline emissions using Verra VM0042 methodology
     * Formula: E_baseline = (F_baseline × EF_fuel) / η_baseline
     */
    private function calculateBaselineEmissions(array $data)
    {
        $dailyFuelUse = $data['daily_fuel_use']; // kg or kWh per day
        $fuelType = $data['fuel_type'];
        $stoveEfficiency = $data['stove_efficiency'] ?? $this->getStoveEfficiencies()[$data['stove_type']];

        // Monthly fuel consumption (30 days)
        $monthlyFuelConsumption = $dailyFuelUse * 30;

        // Get emission factor
        $emissionFactor = $this->getEmissionFactors()[$fuelType] ?? 0;

        // Calculate monthly emissions (tCO₂e)
        $monthlyEmissions = ($monthlyFuelConsumption * $emissionFactor) / $stoveEfficiency;

        return round($monthlyEmissions, 6);
    }

    /**
     * Calculate project emissions using Verra VM0042 methodology
     * Formula: E_project = (F_project × EF_fuel) / η_project
     */
    private function calculateProjectEmissions(array $data)
    {
        $dailyFuelUse = $data['daily_fuel_use']; // kg or kWh per day
        $fuelType = $data['fuel_type'];
        $stoveEfficiency = $data['stove_efficiency'] ?? $this->getStoveEfficiencies()[$data['stove_type']];

        // Monthly fuel consumption (30 days)
        $monthlyFuelConsumption = $dailyFuelUse * 30;

        // Get emission factor
        $emissionFactor = $this->getEmissionFactors()[$fuelType] ?? 0;

        // Calculate monthly emissions (tCO₂e)
        $monthlyEmissions = ($monthlyFuelConsumption * $emissionFactor) / $stoveEfficiency;

        return round($monthlyEmissions, 6);
    }

    /**
     * Calculate quick emissions (simplified calculation)
     */
    private function calculateQuickEmissions(array $data)
    {
        return $this->calculateBaselineEmissions($data);
    }

    /**
     * Calculate emission reduction (Carbon Credits)
     * Formula: ER = E_baseline - E_project
     */
    private function calculateEmissionReduction($baselineData, $projectData)
    {
        $baselineMonthly = is_array($baselineData) ? $baselineData['monthly_emissions'] : $baselineData->monthly_emissions;
        $projectMonthly = is_array($projectData) ? $projectData['monthly_emissions'] : $projectData->monthly_emissions;

        $monthlyReduction = $baselineMonthly - $projectMonthly;
        $annualReduction = $monthlyReduction * 12;

        // Calculate percentage reduction
        $percentageReduction = $baselineMonthly > 0 ? ($monthlyReduction / $baselineMonthly) * 100 : 0;

        return [
            'monthly_reduction' => round($monthlyReduction, 6),
            'annual_reduction' => round($annualReduction, 6),
            'percentage_reduction' => round($percentageReduction, 2),
            'baseline_monthly' => $baselineMonthly,
            'project_monthly' => $projectMonthly,
        ];
    }

    private function calculateCredits(array $emissionReduction)
    {
        // 1 tonne CO₂e = 1 carbon credit
        return $emissionReduction['monthly_reduction'];
    }

    public function show($id)
    {
        $calculation = Calculation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('calculator.show', compact('calculation'));
    }

    public function update(Request $request, $id)
    {
        $calculation = Calculation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Update calculation logic here
        $calculation->update($request->validated());

        return redirect()->route('calculator.index')
            ->with('success', 'Calculation updated successfully!');
    }

    public function destroy($id)
    {
        $calculation = Calculation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $calculation->delete();

        return redirect()->route('calculator.index')
            ->with('success', 'Calculation deleted successfully!');
    }

    /**
     * API endpoint to get emission factors and stove efficiencies
     */
    public function getEmissionData()
    {
        return response()->json([
            'emission_factors' => $this->getEmissionFactors(),
            'stove_efficiencies' => $this->getStoveEfficiencies(),
        ]);
    }

    /**
     * Weekly update endpoint for users to track progress
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

            // Calculate actual emissions for the week
            $weeklyEmissions = $this->calculateWeeklyEmissions($validated, $projectData);

            // Store weekly tracking data
            Calculation::create([
                'user_id' => $user->id,
                'type' => 'weekly_update',
                'data' => json_encode($validated),
                'weekly_emissions' => $weeklyEmissions,
                'monthly_emissions' => $weeklyEmissions * 4.33, // Average weeks per month
                'annual_emissions' => $weeklyEmissions * 52,
            ]);

            // Update cumulative credits
            $baselineData = BaselineData::where('user_id', $user->id)->first();
            if ($baselineData) {
                $emissionReduction = $this->calculateEmissionReduction(
                    $baselineData,
                    ['monthly_emissions' => $weeklyEmissions * 4.33]
                );

                $projectData->increment('total_credits', $emissionReduction['monthly_reduction'] / 4.33);
            }

            return redirect()->route('calculator.index')
                ->with('success', 'Weekly update recorded successfully!')
                ->with('emissions', [
                    'weekly' => $weeklyEmissions,
                    'monthly' => $weeklyEmissions * 4.33,
                    'annual' => $weeklyEmissions * 52,
                    'type' => 'weekly_update'
                ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Weekly update failed: ' . $e->getMessage());
        }
    }

    private function calculateWeeklyEmissions(array $data, $projectData)
    {
        $dailyFuelUse = $data['actual_fuel_use'] / 7; // Convert weekly to daily
        $fuelType = $projectData->new_fuel_type;
        $stoveEfficiency = $projectData->stove_efficiency;

        // Weekly fuel consumption
        $weeklyFuelConsumption = $data['actual_fuel_use'];

        // Get emission factor
        $emissionFactor = $this->getEmissionFactors()[$fuelType] ?? 0;

        // Calculate weekly emissions (tCO₂e)
        $weeklyEmissions = ($weeklyFuelConsumption * $emissionFactor) / $stoveEfficiency;

        return round($weeklyEmissions, 6);
    }
}
