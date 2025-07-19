<?php

namespace App\Http\Controllers;

use App\Models\ProjectData;
use App\Models\BaselineData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmissionsCalculator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProjectDataController extends Controller
{

    protected $emissionsCalculator;

    public function __construct(EmissionsCalculator $emissionsCalculator)
    {
        $this->middleware('auth');
        $this->emissionsCalculator = $emissionsCalculator;
    }

    /**
     * Show the form for creating new project data
     */
    public function create()
    {
        $userId = Auth::id();
        $baseline = BaselineData::where('user_id', $userId)->first();

        if (!$baseline) {
            return redirect()->route('baseline.create')
                ->with('error', 'Please complete baseline data first before adding project intervention.');
        }

        return view('project.create', [
            'baseline' => $baseline,
            'stoveTypes' => [
                '3-Stone Fire',
                'Charcoal Brazier',
                'Kerosene Stove',
                'LPG Stove',
                'Electric Stove',
                'Improved Biomass Stove'
            ],
            'fuelTypes' => [
                'Wood',
                'Charcoal',
                'LPG',
                'Electricity',
                'Ethanol',
                'Other'
            ]
        ]);
    }

    /**
     * Store newly created project data
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_stove_type' => 'required|string|max:255',
            'new_fuel_type' => 'required|string|max:255',
            'fuel_use_project' => 'required|numeric|min:0.01',
            'new_efficiency' => 'nullable|numeric|min:0.01|max:1',
            'start_date' => 'required|date|before_or_equal:today',
            'observed_reduction' => 'nullable|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userId = Auth::id();
            $baseline = BaselineData::where('user_id', $userId)->firstOrFail();

            $validated = $validator->validated();
            $efficiency = $validated['new_efficiency'] ?? $this->getDefaultEfficiency($validated['new_stove_type']);

            // Calculate comprehensive emissions data
            $emissions = $this->emissionsCalculator->calculateComprehensiveEmissions(
                [
                    'daily_fuel_use' => $baseline->daily_fuel_use,
                    'fuel_type' => $baseline->fuel_type,
                    'efficiency' => $baseline->efficiency
                ],
                [
                    'fuel_use_project' => $validated['fuel_use_project'],
                    'new_fuel_type' => $validated['new_fuel_type'],
                    'new_efficiency' => $efficiency
                ]
            );

            // Create project data
            ProjectData::create([
                'user_id' => $userId,
                'new_stove_type' => $validated['new_stove_type'],
                'new_fuel_type' => $validated['new_fuel_type'],
                'fuel_use_project' => $validated['fuel_use_project'],
                'new_efficiency' => $efficiency,
                'start_date' => $validated['start_date'],
                'observed_reduction' => $validated['observed_reduction'],
                'emissions_after' => $emissions['project_emissions'],
                'credits_earned' => $emissions['emission_reduction']
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Project intervention data saved successfully! You have earned ' .
                      round($emissions['emission_reduction'], 3) . ' tonnes CO₂e in carbon credits.');

        } catch (\Exception $e) {
            Log::error('Error storing project data: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'An error occurred while saving your data. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the project data details
     */
    public function show($id)
    {
        $project = ProjectData::where('user_id', Auth::id())->findOrFail($id);
        $baseline = BaselineData::where('user_id', Auth::id())->firstOrFail();

        return view('project.show', [
            'project' => $project,
            'baseline' => $baseline
        ]);
    }

    /**
     * Show the form for editing project data
     */
    public function edit($id)
    {
        $project = ProjectData::where('user_id', Auth::id())->findOrFail($id);
        $baseline = BaselineData::where('user_id', Auth::id())->firstOrFail();

        return view('project.edit', [
            'project' => $project,
            'baseline' => $baseline,
            'stoveTypes' => [
                '3-Stone Fire',
                'Charcoal Brazier',
                'Kerosene Stove',
                'LPG Stove',
                'Electric Stove',
                'Improved Biomass Stove'
            ],
            'fuelTypes' => [
                'Wood',
                'Charcoal',
                'LPG',
                'Electricity',
                'Ethanol',
                'Other'
            ]
        ]);
    }

    /**
     * Update the project data
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_stove_type' => 'required|string|max:255',
            'new_fuel_type' => 'required|string|max:255',
            'fuel_use_project' => 'required|numeric|min:0.01',
            'new_efficiency' => 'nullable|numeric|min:0.01|max:1',
            'start_date' => 'required|date|before_or_equal:today',
            'observed_reduction' => 'nullable|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $project = ProjectData::where('user_id', Auth::id())->findOrFail($id);
            $baseline = BaselineData::where('user_id', Auth::id())->firstOrFail();

            $validated = $validator->validated();
            $efficiency = $validated['new_efficiency'] ?? $this->getDefaultEfficiency($validated['new_stove_type']);

            // Calculate comprehensive emissions data
            $emissions = $this->emissionsCalculator->calculateComprehensiveEmissions(
                [
                    'daily_fuel_use' => $baseline->daily_fuel_use,
                    'fuel_type' => $baseline->fuel_type,
                    'efficiency' => $baseline->efficiency
                ],
                [
                    'fuel_use_project' => $validated['fuel_use_project'],
                    'new_fuel_type' => $validated['new_fuel_type'],
                    'new_efficiency' => $efficiency
                ]
            );

            // Update project data
            $project->update([
                'new_stove_type' => $validated['new_stove_type'],
                'new_fuel_type' => $validated['new_fuel_type'],
                'fuel_use_project' => $validated['fuel_use_project'],
                'new_efficiency' => $efficiency,
                'start_date' => $validated['start_date'],
                'observed_reduction' => $validated['observed_reduction'],
                'emissions_after' => $emissions['project_emissions'],
                'credits_earned' => $emissions['emission_reduction']
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Project data updated successfully! Updated carbon credits: ' .
                      round($emissions['emission_reduction'], 3) . ' tonnes CO₂e.');

        } catch (\Exception $e) {
            Log::error('Error updating project data: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'An error occurred while updating your data. Please try again.')
                ->withInput();
        }
    }

    /**
     * Get default efficiency for stove type
     */
    private function getDefaultEfficiency($stoveType)
    {
        $efficiencies = [
            '3-Stone Fire' => 0.10,
            'Charcoal Brazier' => 0.10,
            'Kerosene Stove' => 0.45,
            'LPG Stove' => 0.55,
            'Electric Stove' => 0.75,
            'Improved Biomass Stove' => 0.25,
        ];

        return $efficiencies[$stoveType] ?? 0.15;
    }
}
