<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Imports the Request class to handle HTTP requests.
use Illuminate\Support\Facades\Auth; // Imports the Auth facade for user authentication.
use Illuminate\Support\Facades\Log; // IMPORTS THE LOG FACADE TO RESOLVE INTELEPHENSE WARNING.
use App\Models\ProjectData; // Imports the ProjectData model to interact with project emissions data.
use App\Models\BaselineData; // Imports the BaselineData model to fetch baseline for calculations.
use App\Services\EmissionsCalculator; // Imports the EmissionsCalculator service for emission calculations.


/**
 * Developed by Levy Bronzoh, Climate Yanga.
 *
 * WeeklyInputController
 * Handles the display and submission of the weekly/monthly fuel usage update form,
 * and calculates cumulative carbon credits earned.
 */
class WeeklyInputController extends Controller
{
    protected $emissionsCalculator; // Declares a protected property to hold an instance of EmissionsCalculator.

    /**
     * Constructor for WeeklyInputController.
     * @param EmissionsCalculator $emissionsCalculator An instance of the EmissionsCalculator service.
     * This method uses dependency injection to automatically resolve and inject the
     * EmissionsCalculator service when the WeeklyInputController is instantiated.
     */
    public function __construct(EmissionsCalculator $emissionsCalculator)
    {
        // Assigns the injected EmissionsCalculator instance to the controller's property.
        $this->emissionsCalculator = $emissionsCalculator;
    }

    /**
     * Display the form for weekly/monthly cooking behavior updates.
     *
     * @return \Illuminate\View\View Returns the view containing the weekly input form.
     */
    public function create()
    {
        // Checks if a user is currently authenticated.
        if (!Auth::check()) {
            // If no user is authenticated, redirects them to the login page.
            return redirect()->route('login');
        }

        // Returns the 'weekly_update.create' view, which will contain the form for weekly data input.
        return view('weekly_update.create');
    }

    /**
     * Store the weekly/monthly cooking behavior update.
     * This method validates the incoming request, calculates updated project emissions
     * and carbon credits, and saves the data to the database.
     *
     * @param Request $request The incoming HTTP request containing weekly update data.
     * @return \Illuminate\Http\RedirectResponse Redirects to the dashboard or back with errors.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data.
        // Ensures required fields are present and in the correct format.
        $validatedData = $request->validate([
            'new_fuel_type' => 'required|string|max:255', // New fuel type is required.
            'fuel_use_project' => 'required|numeric|min:0', // Weekly/monthly fuel use is required, numeric, and non-negative.
            'new_efficiency' => 'nullable|numeric|min:0|max:1', // New stove efficiency is optional, numeric, between 0 and 1.
            'start_date' => 'required|date', // Start date of the project (or current update period) is required.
        ]);

        // Check if a user is authenticated.
        if (!Auth::check()) {
            // If not authenticated, log an error and redirect to login.
            Log::error('Attempted to store weekly update data without authentication.');
            return redirect()->route('login')->with('error', 'You must be logged in to submit weekly updates.');
        }

        // Get the authenticated user's ID.
        $userId = Auth::id();

        try {
            // Fetch the latest baseline data for the user to calculate emission reduction.
            $baselineData = BaselineData::where('user_id', $userId)->latest()->first();

            // If no baseline data exists, the user cannot track reductions.
            if (!$baselineData) {
                return back()->withInput()->with('error', 'Please submit your baseline data first.');
            }

            // Calculate project emissions based on the new weekly fuel use.
            $projectEmissions = $this->emissionsCalculator->calculateProjectEmissions(
                $validatedData['new_fuel_type'],
                $validatedData['fuel_use_project'],
                $validatedData['new_efficiency'] ?? null // Pass null if efficiency is not provided.
            );

            // Calculate emission reduction (carbon credits).
            // This assumes baselineEmissions is already calculated and stored in baselineData.
            $emissionReduction = $baselineData->emission_total - $projectEmissions;

            // Find the latest project data entry for the user to update it, or create a new one.
            // This approach updates the *most recent* project data entry, effectively
            // tracking the latest state of the project and accumulating credits.
            $projectData = ProjectData::where('user_id', $userId)->latest()->first();

            if ($projectData) {
                // If existing project data, update it.
                $projectData->new_fuel_type = $validatedData['new_fuel_type']; // Update fuel type.
                $projectData->fuel_use_project = $validatedData['fuel_use_project']; // Update fuel use.
                $projectData->new_efficiency = $validatedData['new_efficiency']; // Update efficiency.
                $projectData->start_date = $validatedData['start_date']; // Update start date (or current update date).
                $projectData->emissions_after = $projectEmissions; // Update calculated project emissions.
                // Add the new emission reduction to existing credits earned for cumulative tracking.
                $projectData->credits_earned += $emissionReduction; // Accumulate credits.
                $projectData->updated_at = now(); // Update the timestamp.
                $projectData->save(); // Save the updated record.
            } else {
                // If no existing project data (first project data submission via weekly form), create a new one.
                $projectData = new ProjectData();
                $projectData->user_id = $userId; // Assign the authenticated user's ID.
                $projectData->new_stove_type = 'N/A'; // Default, as this form doesn't explicitly ask for new stove type.
                                                      // This could be improved by fetching from baseline or previous project data.
                $projectData->new_fuel_type = $validatedData['new_fuel_type']; // Assign new fuel type.
                $projectData->fuel_use_project = $validatedData['fuel_use_project']; // Assign new fuel use.
                $projectData->new_efficiency = $validatedData['new_efficiency']; // Assign new efficiency.
                $projectData->start_date = $validatedData['start_date']; // Assign start date.
                $projectData->emissions_after = $projectEmissions; // Assign calculated project emissions.
                $projectData->credits_earned = $emissionReduction; // Assign initial credits earned.
                $projectData->save(); // Save the new record.
            }

            // Redirect to the dashboard with a success message.
            return redirect()->route('dashboard')->with('success', 'Weekly update submitted and credits tracked!');

        } catch (\Exception $e) {
            // Catch any exceptions during the process and log them.
            Log::error('Error storing weekly update data: ' . $e->getMessage(), ['user_id' => $userId, 'data' => $validatedData]);
            // Redirect back with an error message.
            return back()->withInput()->with('error', 'There was an error submitting your weekly update. Please try again.');
        }
    }
}

