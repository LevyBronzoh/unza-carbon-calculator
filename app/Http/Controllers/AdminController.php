<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\BaselineData;
use App\Models\ProjectData;
use App\Services\EmissionsCalculator;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\ActivityLog;

class AdminController extends Controller
{
    private const USERS_PER_PAGE = 20;
    private const RECENT_ENTRIES_LIMIT = 10;
    private const RECENT_DAYS = 30;
    private const MONTHS_IN_YEAR = 12;

    public function __construct(
        private EmissionsCalculator $emissionsCalculator
    ) {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): View
    {
        $dashboardData = $this->getDashboardStatistics();

        return view('admin.dashboard', $dashboardData);
    }

    public function users(Request $request): View
    {
        $query = User::with(['baselineData', 'projectData']);
        $this->applyFilters($query, $request);

        return view('admin.users', [
            'users' => $query->latest()->paginate(self::USERS_PER_PAGE),
            'user_types' => User::distinct()->pluck('user_type'),
            'filters' => $request->all()
        ]);
    }

    public function userDetails(int $userId): View
    {
        $user = User::with(['baselineData', 'projectData'])->findOrFail($userId);

        return view('admin.user-details', $this->prepareUserDetails($user));
    }

    public function verifyUserReport(int $userId, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'verification_status' => 'required|in:approved,rejected,pending',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $this->updateVerificationStatus($userId, $validated);

        return redirect()->back()
            ->with('success', "User report {$validated['verification_status']} successfully.");
    }

    public function exportSystemData(string $format = 'csv'): JsonResponse|StreamedResponse
    {
        $exportData = $this->prepareExportData();

        return match ($format) {
            'csv' => $this->exportAsCsv($exportData),
            'excel' => $this->exportAsExcel($exportData),
            'json' => response()->json([
                'export_date' => now()->toDateTimeString(),
                'total_users' => count($exportData),
                'data' => $exportData
            ]),
            default => response()->json(['error' => 'Invalid export format'], 400)
        };
    }

    public function analytics(): View
    {
          // Get recent activities (last 30 days)
    $recentActivities = ActivityLog::where('created_at', '>=', Carbon::now()->subDays(30))
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get();

    // Get other analytics data (your existing code)
    $monthlyRegistrations = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                              ->where('created_at', '>=', Carbon::now()->subYear())
                              ->groupBy('month')
                              ->orderBy('month')
                              ->pluck('count', 'month')
                              ->toArray();

    $stoveTypeDistribution = BaselineData::selectRaw('stove_type, COUNT(*) as count')
                                       ->groupBy('stove_type')
                                       ->pluck('count', 'stove_type')
                                       ->toArray();

    $fuelTypeDistribution = BaselineData::selectRaw('fuel_type, COUNT(*) as count')
                                      ->groupBy('fuel_type')
                                      ->pluck('count', 'fuel_type')
                                      ->toArray();

    $monthlyCredits = ProjectData::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(credits_earned) as total')
                                ->where('created_at', '>=', Carbon::now()->subYear())
                                ->groupBy('month')
                                ->orderBy('month')
                                ->pluck('total', 'month')
                                ->toArray();
        return view('admin.analytics', [
            'monthly_registrations' => $this->getMonthlyRegistrations(),
            'stove_type_distribution' => $this->getStoveTypeDistribution(),
            'fuel_type_distribution' => $this->getFuelTypeDistribution(),
            'monthly_credits' => $this->getMonthlyCreditsTrend(),
            'recentActivities' => $recentActivities
        ]);
    }

    private function getDashboardStatistics(): array
    {
        $totalBaselineEmissions = BaselineData::sum('emission_total');
        $totalProjectEmissions = ProjectData::sum('emissions_after');

        return [
            'total_users' => User::count(),
            'active_users' => User::has('baselineData')->count(),
            'users_with_projects' => User::has('projectData')->count(),
            'total_baseline_emissions' => $totalBaselineEmissions,
            'total_project_emissions' => $totalProjectEmissions,
            'total_credits_earned' => ProjectData::sum('credits_earned'),
            'avg_reduction_percentage' => $this->calculateReductionPercentage(
                $totalBaselineEmissions,
                $totalProjectEmissions
            ),
            'recent_users' => User::where('created_at', '>=', now()->subDays(self::RECENT_DAYS))
                ->latest()
                ->limit(self::RECENT_ENTRIES_LIMIT)
                ->get(),
            'recent_projects' => ProjectData::with('user')
                ->where('created_at', '>=', now()->subDays(self::RECENT_DAYS))
                ->latest()
                ->limit(self::RECENT_ENTRIES_LIMIT)
                ->get()
        ];
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('user_type', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'active' => $query->has('baselineData'),
                'with_projects' => $query->has('projectData'),
                'new' => $query->where('created_at', '>=', now()->subDays(self::RECENT_DAYS)),
                default => null
            };
        }
    }

   private function prepareUserDetails(User $user): array
{
    $baselineData = $user->baselineData->first();
    $projectData = $user->projectData->first();

    $details = [
        'user' => $user,
        'baseline_data' => $baselineData,
        'project_data' => $projectData,
        'baseline_emissions' => 0,
        'project_emissions' => 0,
        'monthly_credits' => 0,
        'annual_credits' => 0,
        'verification_status' => $projectData->verification_status ?? 'pending'
    ];

    if ($baselineData) {
        $details['baseline_emissions'] = $this->emissionsCalculator
            ->calculateBaselineEmissions(
                $baselineData->daily_fuel_use,
                $baselineData->fuel_type,
                $baselineData->stove_efficiency,
                'monthly' // or 'annual' depending on your needs
            );
    }

    if ($projectData) {
        $details['project_emissions'] = $this->emissionsCalculator
            ->calculateProjectEmissions(
                $projectData->daily_fuel_use,
                $projectData->new_fuel_type,
                $projectData->new_stove_efficiency,
                'monthly' // or 'annual' depending on your needs
            );

        $details['monthly_credits'] = $details['baseline_emissions'] - $details['project_emissions'];
        $details['annual_credits'] = $details['monthly_credits'] * self::MONTHS_IN_YEAR;
    }

    return $details;
}

    private function updateVerificationStatus(int $userId, array $data): void
    {
        if ($projectData = ProjectData::where('user_id', $userId)->latest()->first()) {
            $projectData->update([
                'verification_status' => $data['verification_status'],
                'admin_notes' => $data['admin_notes'] ?? null,
                'verified_by' => Auth::id(),
                'verified_at' => now()
            ]);
        }
    }

    private function prepareExportData(): array
    {
        return User::with(['baselineData', 'projectData'])
            ->has('projectData')
            ->get()
            ->map(function (User $user) {
                $baselineData = $user->baselineData->first();
                $projectData = $user->projectData->first();

                $baselineEmissions = $baselineData
                    ? $this->emissionsCalculator->calculateBaselineEmissions(
                        $baselineData->daily_fuel_use,
                        $baselineData->fuel_type,
                        $baselineData->stove_efficiency
                    )
                    : 0;

                $projectEmissions = $projectData
                    ? $this->emissionsCalculator->calculateProjectEmissions(
                        $projectData->daily_fuel_use,
                        $projectData->new_fuel_type,
                        $projectData->new_stove_efficiency
                    )
                    : 0;

                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'baseline_stove_type' => $baselineData->stove_type ?? 'N/A',
                    'baseline_fuel_type' => $baselineData->fuel_type ?? 'N/A',
                    'baseline_emissions' => $baselineEmissions,
                    'project_stove_type' => $projectData->new_stove_type ?? 'N/A',
                    'project_fuel_type' => $projectData->new_fuel_type ?? 'N/A',
                    'project_emissions' => $projectEmissions,
                    'monthly_credits' => $baselineEmissions - $projectEmissions,
                    'annual_credits' => ($baselineEmissions - $projectEmissions) * self::MONTHS_IN_YEAR,
                    'intervention_start_date' => $projectData->start_date ?? 'N/A',
                    'verification_status' => $projectData->verification_status ?? 'pending',
                    'export_date' => now()->toDateTimeString()
                ];
            })->toArray();
    }

    private function calculateReductionPercentage(float $baseline, float $project): float
    {
        return $baseline > 0 ? round((($baseline - $project) / $baseline) * 100, 2) : 0;
    }

    private function getMonthlyRegistrations(): array
    {
        return User::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    private function getStoveTypeDistribution(): array
    {
        return BaselineData::query()
            ->selectRaw('stove_type, COUNT(*) as count')
            ->groupBy('stove_type')
            ->pluck('count', 'stove_type')
            ->toArray();
    }

    private function getFuelTypeDistribution(): array
    {
        return BaselineData::query()
            ->selectRaw('fuel_type, COUNT(*) as count')
            ->groupBy('fuel_type')
            ->pluck('count', 'fuel_type')
            ->toArray();
    }

    private function getMonthlyCreditsTrend(): array
    {
        return ProjectData::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(credits_earned) as total')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    private function exportAsCsv(array $data): StreamedResponse
{
    return new StreamedResponse(function() use ($data) {
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, array_keys($data[0] ?? []));

        // Write data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="carbon_data_'.now()->format('Ymd_His').'.csv"',
    ]);
}

private function exportAsExcel(array $data): StreamedResponse
{
    return new StreamedResponse(function() use ($data) {
        $output = fopen('php://output', 'w');

        // Write Excel headers (TSV format)
        fputcsv($output, array_keys($data[0] ?? []), "\t");

        // Write data rows
        foreach ($data as $row) {
            fputcsv($output, $row, "\t");
        }

        fclose($output);
    }, 200, [
        'Content-Type' => 'application/vnd.ms-excel',
        'Content-Disposition' => 'attachment; filename="carbon_data_'.now()->format('Ymd_His').'.xls"',
    ]);
}

}
