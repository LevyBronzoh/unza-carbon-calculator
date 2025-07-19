<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeeklyUpdate;
use App\Models\ProjectData;
use App\Models\BaselineData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyUpdateController extends Controller
{
    /**
     * Show the form for creating a new weekly update.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $user = Auth::user();
        $latestProject = ProjectData::where('user_id', $user->id)->latest()->first();

        if (!$latestProject) {
            return redirect()->route('project.create')
                ->with('warning', 'Please set up your clean cooking project first.');
        }

        return view('weekly-update.create', [
            'project' => $latestProject,
            'lastUpdate' => WeeklyUpdate::where('user_id', $user->id)->latest()->first()
        ]);
    }

    /**
     * Store a newly created weekly update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_consumption' => 'required|numeric|min:0',
            'cooking_hours' => 'required|numeric|min:0',
            'stove_usage_percentage' => 'required|numeric|between:0,100',
            'notes' => 'nullable|string|max:500'
        ]);

        $project = ProjectData::where('user_id', Auth::id())->latest()->first();

        $update = new WeeklyUpdate($validated);
        $update->user_id = Auth::id();
        $update->project_data_id = $project->id;

        // Calculate estimated emissions for this period
        $update->estimated_emissions = $this->calculateWeeklyEmissions(
            $validated['fuel_consumption'],
            $project->new_fuel_type,
            $project->new_efficiency
        );

        $update->save();

        // Update the user's total credits
        $this->updateUserCredits(Auth::user());

        return redirect()->route('results.show')
            ->with('success', 'Weekly update submitted successfully!');
    }

    /**
     * Calculate weekly emissions based on Verra VM0042 methodology
     */
    private function calculateWeeklyEmissions($fuelConsumption, $fuelType, $efficiency)
    {
        $emissionFactors = [
            'wood' => 0.001747,
            'charcoal' => 0.00674,
            'lpg' => 0.002983,
            'electricity' => 0.00085
        ];

        $factor = $emissionFactors[strtolower($fuelType)] ?? 0.001747;
        return ($fuelConsumption * $factor) / ($efficiency / 100);
    }

    /**
     * Update user's total credits
     */
    private function updateUserCredits($user)
    {
        $baseline = BaselineData::where('user_id', $user->id)->latest()->first();
        $project = ProjectData::where('user_id', $user->id)->latest()->first();
        $updates = WeeklyUpdate::where('user_id', $user->id)->get();

        $totalReduction = $updates->sum('estimated_emissions');
        $baselineEmissions = $this->calculateWeeklyEmissions(
            $baseline->daily_fuel_use * 7, // Weekly baseline
            $baseline->fuel_type,
            $baseline->efficiency
        );

        $project->credits_earned = max(0, $baselineEmissions - $totalReduction);
        $project->save();
    }
};
