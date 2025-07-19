<?php

namespace App\Http\Controllers;

use App\Models\BaselineData;
use App\Models\ProjectData;
use App\Models\WeeklyUpdate;
use Illuminate\Support\Facades\Auth;

class ResultsController extends Controller
{
    /**
     * Display the user's cooking emissions results.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = Auth::user();
        $baseline = $user->baselineData()->latest()->first();
        $project = $user->projectData()->latest()->first();
        $updates = $user->weeklyUpdates()->latest()->take(4)->get();

        if (!$baseline || !$project) {
            return redirect()->route('home')
                ->with('warning', 'Please complete your baseline and project setup first.');
        }

        // Calculate emissions
        $results = $this->calculateResults($baseline, $project, $updates);

        return view('results.show', [
            'baseline' => $baseline,
            'project' => $project,
            'updates' => $updates,
            'results' => $results,
            'chartData' => $this->prepareChartData($baseline, $project, $updates)
        ]);
    }

    /**
     * Calculate all emission results
     */
    private function calculateResults($baseline, $project, $updates)
    {
        // Monthly calculations (30 days as per specs)
        $baselineMonthly = $this->calculateMonthlyEmissions(
            $baseline->daily_fuel_use * 30,
            $baseline->fuel_type,
            $baseline->efficiency
        );

        $projectMonthly = $this->calculateMonthlyEmissions(
            $project->fuel_use_project * 30,
            $project->new_fuel_type,
            $project->new_efficiency
        );

        // Weekly calculations from actual updates
        $weeklyReduction = 0;
        $weeklyActual = [];

        foreach ($updates as $update) {
            $weeklyActual[] = [
                'date' => $update->created_at->format('M d'),
                'emissions' => $update->estimated_emissions
            ];
            $weeklyReduction += $update->estimated_emissions;
        }

        return [
            'baseline_monthly' => $baselineMonthly,
            'project_monthly' => $projectMonthly,
            'monthly_reduction' => $baselineMonthly - $projectMonthly,
            'weekly_actual' => $weeklyActual,
            'total_credits' => $project->credits_earned ?? 0,
            'clean_days' => $updates->count() * 7 // Approximate
        ];
    }

    /**
     * Calculate monthly emissions using Verra VM0042 methodology
     */
    private function calculateMonthlyEmissions($fuelConsumption, $fuelType, $efficiency)
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
     * Prepare data for the emissions chart
     */
    private function prepareChartData($baseline, $project, $updates)
    {
        // Implementation for chart data preparation
        // Would return data in format needed by your charting library
        return [
            'labels' => [],
            'datasets' => []
        ];
    }
}
