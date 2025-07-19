<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaselineData;
use App\Models\ProjectData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DashboardController - Main dashboard for UNZA Carbon Calculator
 *
 * This controller manages the main dashboard where users can view their emissions summary,
 * carbon credits earned, and overall progress. It provides both user dashboard and
 * admin dashboard functionality with comprehensive analytics.
 *
 * @package App\Http\Controllers
 * @author Developed by Levy Bronzoh, Climate Yanga
 * @version 1.0
 * @since 2025-07-12
 */
class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     * Apply auth middleware to protect all dashboard routes
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the main user dashboard
     *
     * This method shows the user's emissions summary, carbon credits earned,
     * progress tracking, and quick access to baseline and project data forms.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Retrieve user's baseline and project data
        $baselineData = BaselineData::where('user_id', $userId)->first();
        $projectData = ProjectData::where('user_id', $userId)->first();

        // Initialize dashboard metrics
        $dashboardMetrics = [
            'user_id' => $userId,
            'user_name' => $user->name,
            'user_type' => $user->user_type,
            'has_baseline' => false,
            'has_project' => false,
            'monthly_baseline_emissions' => 0,
            'monthly_project_emissions' => 0,
            'monthly_credits_earned' => 0,
            'annual_baseline_emissions' => 0,
            'annual_project_emissions' => 0,
            'annual_credits_earned' => 0,
            'percentage_reduction' => 0,
            'cumulative_credits' => 0,
            'project_duration_months' => 0,
            'next_steps' => []
        ];

        // Process baseline data if exists
        if ($baselineData) {
            $dashboardMetrics['has_baseline'] = true;
            $dashboardMetrics['monthly_baseline_emissions'] = $baselineData->emission_total;
            $dashboardMetrics['annual_baseline_emissions'] = $baselineData->emission_total * 12;
        }

        // Process project data if exists
        if ($projectData) {
            $dashboardMetrics['has_project'] = true;
            $dashboardMetrics['monthly_project_emissions'] = $projectData->emissions_after;
            $dashboardMetrics['monthly_credits_earned'] = $projectData->credits_earned;
            $dashboardMetrics['annual_project_emissions'] = $projectData->emissions_after * 12;
            $dashboardMetrics['annual_credits_earned'] = $projectData->credits_earned * 12;

            // Calculate percentage reduction
            if ($baselineData && $baselineData->emission_total > 0) {
                $dashboardMetrics['percentage_reduction'] =
                    round(($projectData->credits_earned / $baselineData->emission_total) * 100, 1);
            }

            // Calculate cumulative credits
            $startDate = $projectData->start_date instanceof \DateTimeInterface
                ? Carbon::instance($projectData->start_date)
                : Carbon::parse((string)$projectData->start_date);

            $monthsElapsed = $startDate->diffInMonths(Carbon::now());
            $dashboardMetrics['project_duration_months'] = $monthsElapsed;
            $dashboardMetrics['cumulative_credits'] = $projectData->credits_earned * $monthsElapsed;
        }

        // Generate next steps and recent activity
        $dashboardMetrics['next_steps'] = $this->generateNextSteps($baselineData, $projectData);
        $recentActivity = $this->getRecentActivity($userId);

        // Create recent activities array for the blade template
        $recentActivities = collect($recentActivity)->map(function ($activity) {
            return (object) [
                'type' => $activity['type'],
                'description' => $activity['description'],
                'created_at' => $activity['date']
            ];
        });

        // Calculate additional metrics
        $totalEmissions = $this->calculateTotalEmissions($user);
        $recentCalculations = $this->getRecentCalculations($user);

        // Return the view with all data
        return view('dashboard', compact(
            'dashboardMetrics',
            'baselineData',
            'projectData',
            'recentActivity',
            'recentActivities',
            'totalEmissions',
            'recentCalculations'
        ));
    }

    /**
     * Display admin dashboard with system-wide analytics
     * Only accessible to staff members (since there's no admin user_type in specs)
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function adminDashboard()
    {
        // Check if user has staff privileges (using staff as admin equivalent)
        $user = Auth::user();
        if ($user->user_type !== 'staff') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Staff privileges required.');
        }

        // Get system-wide statistics
        $systemStats = $this->getSystemStatistics();

        // Get recent user registrations (last 30 days)
        $recentUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();

        // Get top carbon credit earners
        $topCreditEarners = $this->getTopCreditEarners();

        // Get monthly registration trends (last 12 months)
        $registrationTrends = $this->getRegistrationTrends();

        // Get monthly carbon credit trends (last 12 months)
        $creditTrends = $this->getCreditTrends();

        // Return admin dashboard view
        return view('dashboard.admin', compact(
            'systemStats',
            'recentUsers',
            'topCreditEarners',
            'registrationTrends',
            'creditTrends'
        ));
    }

    /**
     * Generate personalized next steps recommendations for users
     *
     * @param BaselineData|null $baselineData User's baseline data
     * @param ProjectData|null $projectData User's project data
     * @return array Array of recommended next steps
     */
    private function generateNextSteps($baselineData, $projectData)
    {
        $nextSteps = [];

        // If no baseline data, recommend starting with baseline
        if (!$baselineData) {
            $nextSteps[] = [
                'title' => 'Enter Baseline Data',
                'description' => 'Start by recording your current cooking setup to establish your baseline emissions.',
                'action' => 'Go to Baseline Form',
                'url' => route('baseline.create'), // Updated to use create route
                'priority' => 'high'
            ];
            return $nextSteps;
        }

        // If baseline exists but no project data, recommend project intervention
        if (!$projectData) {
            $nextSteps[] = [
                'title' => 'Record Clean Cooking Intervention',
                'description' => 'Enter details about your cleaner cooking method to start earning carbon credits.',
                'action' => 'Go to Project Form',
                'url' => route('project.create'), // Updated to use create route
                'priority' => 'high'
            ];
        }

        // If both exist, provide improvement recommendations
        if ($baselineData && $projectData) {
            // Check if emissions reduction is low (less than 30%)
            if ($baselineData->emission_total > 0) {
                $reductionPercentage = ($projectData->credits_earned / $baselineData->emission_total) * 100;

                if ($reductionPercentage < 30) {
                    $nextSteps[] = [
                        'title' => 'Improve Stove Efficiency',
                        'description' => 'Consider upgrading to a more efficient stove to increase your carbon credits.',
                        'action' => 'View Stove Options',
                        'url' => '#stove-options',
                        'priority' => 'medium'
                    ];
                }
            }

            // Recommend weekly updates
            $nextSteps[] = [
                'title' => 'Weekly Progress Update',
                'description' => 'Update your cooking data weekly for more accurate credit calculations.',
                'action' => 'Update Now',
                'url' => route('project.edit', $projectData->id),
                'priority' => 'low'
            ];
        }

        return $nextSteps;
    }

    /**
     * Get recent activity for a specific user
     *
     * @param int $userId The user ID to get activity for
     * @return array Array of recent activity items
     */
    private function getRecentActivity($userId)
    {
        $activities = [];

        // Get baseline data creation/update
        $baselineData = BaselineData::where('user_id', $userId)->first();
        if ($baselineData) {
            $activities[] = [
                'type' => 'baseline',
                'title' => 'Baseline Data Recorded',
                'date' => $baselineData->updated_at ?? $baselineData->created_at,
                'description' => 'Monthly emissions: ' . round($baselineData->emission_total, 3) . ' tCO₂e'
            ];
        }

        // Get project data creation/update
        $projectData = ProjectData::where('user_id', $userId)->first();
        if ($projectData) {
            $activities[] = [
                'type' => 'project',
                'title' => 'Project Intervention Recorded',
                'date' => $projectData->updated_at ?? $projectData->created_at,
                'description' => 'Monthly credits: ' . round($projectData->credits_earned, 3) . ' tCO₂e'
            ];
        }

        // Sort activities by date (newest first)
        usort($activities, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 5); // Return last 5 activities
    }

    /**
     * Calculate total emissions for a user
     *
     * @param User $user
     * @return float
     */
    private function calculateTotalEmissions($user)
    {
        $baselineData = BaselineData::where('user_id', $user->id)->first();
        $projectData = ProjectData::where('user_id', $user->id)->first();

        $totalEmissions = 0;

        if ($baselineData) {
            $totalEmissions += $baselineData->emission_total;
        }

        if ($projectData) {
            $totalEmissions += $projectData->emissions_after;
        }

        return $totalEmissions;
    }

    /**
     * Get recent calculations for a user
     *
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    private function getRecentCalculations($user)
    {
        $calculations = collect();

        // Get recent baseline calculations
        $baselineData = BaselineData::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($baselineData as $baseline) {
            $calculations->push([
                'type' => 'baseline',
                'date' => $baseline->updated_at,
                'description' => 'Baseline calculation: ' . round($baseline->emission_total, 3) . ' tCO₂e'
            ]);
        }

        // Get recent project calculations
        $projectData = ProjectData::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($projectData as $project) {
            $calculations->push([
                'type' => 'project',
                'date' => $project->updated_at,
                'description' => 'Project calculation: ' . round($project->credits_earned, 3) . ' tCO₂e credits'
            ]);
        }

        return $calculations->sortByDesc('date')->take(5);
    }

    /**
     * Get system-wide statistics for admin dashboard
     *
     * @return array Array of system statistics
     */
    private function getSystemStatistics()
    {
        // Get total users count
        $totalUsers = User::count();

        // Get users with baseline data
        $usersWithBaseline = BaselineData::distinct('user_id')->count();

        // Get users with project data
        $usersWithProject = ProjectData::distinct('user_id')->count();

        // Calculate total emissions and credits
        $totalBaselineEmissions = BaselineData::sum('emission_total');
        $totalProjectEmissions = ProjectData::sum('emissions_after');
        $totalCreditsEarned = ProjectData::sum('credits_earned');

        // Calculate average emissions per user
        $avgBaselineEmissions = $usersWithBaseline > 0 ? $totalBaselineEmissions / $usersWithBaseline : 0;
        $avgProjectEmissions = $usersWithProject > 0 ? $totalProjectEmissions / $usersWithProject : 0;

        // Calculate system-wide emission reduction percentage
        $systemReductionPercentage = $totalBaselineEmissions > 0
            ? (($totalBaselineEmissions - $totalProjectEmissions) / $totalBaselineEmissions) * 100
            : 0;

        return [
            'total_users' => $totalUsers,
            'users_with_baseline' => $usersWithBaseline,
            'users_with_project' => $usersWithProject,
            'completion_rate' => $totalUsers > 0 ? round(($usersWithProject / $totalUsers) * 100, 1) : 0,
            'total_baseline_emissions' => round($totalBaselineEmissions, 2),
            'total_project_emissions' => round($totalProjectEmissions, 2),
            'total_credits_earned' => round($totalCreditsEarned, 2),
            'avg_baseline_emissions' => round($avgBaselineEmissions, 3),
            'avg_project_emissions' => round($avgProjectEmissions, 3),
            'system_reduction_percentage' => round($systemReductionPercentage, 1),
            'annual_credits_potential' => round($totalCreditsEarned * 12, 2)
        ];
    }

    /**
     * Get top carbon credit earners for admin dashboard
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopCreditEarners()
    {
        return DB::table('project_data')
                 ->join('users', 'project_data.user_id', '=', 'users.id')
                 ->select('users.name', 'users.email', 'users.user_type', 'project_data.credits_earned', 'project_data.start_date')
                 ->orderBy('project_data.credits_earned', 'desc')
                 ->limit(10)
                 ->get();
    }

    /**
     * Get monthly user registration trends for admin dashboard
     *
     * @return array Array of monthly registration counts
     */
    private function getRegistrationTrends()
    {
        $trends = [];

        // Get registrations for each of the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count();

            $trends[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        return $trends;
    }

    /**
     * Get monthly carbon credit trends for admin dashboard
     *
     * @return array Array of monthly carbon credit totals
     */
    private function getCreditTrends()
    {
        $trends = [];

        // Get carbon credits for each of the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $credits = ProjectData::whereYear('created_at', $month->year)
                                 ->whereMonth('created_at', $month->month)
                                 ->sum('credits_earned');

            $trends[] = [
                'month' => $month->format('M Y'),
                'credits' => round($credits, 2)
            ];
        }

        return $trends;
    }

    /**
     * Export user data for admin purposes
     *
     * @return \Illuminate\Http\Response CSV download response
     */
    public function exportUserData()
    {
        // Check staff privileges (using staff as admin equivalent)
        if (Auth::user()->user_type !== 'staff') {
            return response('Access denied.', 403);
        }

        // Get all user data with emissions and credits
        $userData = DB::table('users')
                     ->leftJoin('baseline_data', 'users.id', '=', 'baseline_data.user_id')
                     ->leftJoin('project_data', 'users.id', '=', 'project_data.user_id')
                     ->select(
                         'users.name',
                         'users.email',
                         'users.user_type',
                         'users.phone',
                         'baseline_data.emission_total as baseline_emissions',
                         'project_data.emissions_after as project_emissions',
                         'project_data.credits_earned',
                         'project_data.start_date',
                         'users.created_at'
                     )
                     ->get();

        // Generate CSV content
        $csvContent = "Name,Email,User Type,Phone,Baseline Emissions (tCO2e),Project Emissions (tCO2e),Credits Earned (tCO2e),Project Start Date,Registration Date\n";

        foreach ($userData as $user) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $user->name,
                $user->email,
                $user->user_type,
                $user->phone ?? 'N/A',
                $user->baseline_emissions ?? 'N/A',
                $user->project_emissions ?? 'N/A',
                $user->credits_earned ?? 'N/A',
                $user->start_date ?? 'N/A',
                $user->created_at
            );
        }

        // Return CSV download response
        return response($csvContent)
               ->header('Content-Type', 'text/csv')
               ->header('Content-Disposition', 'attachment; filename="unza_carbon_data_' . date('Y-m-d') . '.csv"');
    }
}
