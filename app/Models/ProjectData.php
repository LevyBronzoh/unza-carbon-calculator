<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ProjectData Model for UNZA Carbon Calculator
 *
 * This class handles the storage and calculation of project cooking emissions data
 * (after intervention with cleaner cooking methods)
 * Extends Laravel's Model class (Polymorphism) - inherits Eloquent ORM functionality
 *
 * @author Levy Bronzoh, Climate Yanga
 * @version 1.0
 * @since 2025-07-12
 */
class ProjectData extends Model
{
    // Uses Laravel factory trait for database seeding and testing
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Allows these fields to be filled using create() or update() methods
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',              // Foreign key linking to User model
        'new_stove_type',       // Type of new/improved stove used
        'new_fuel_type',        // Type of new fuel used after intervention
        'fuel_use_project',     // Amount of fuel used per day after intervention (kg or liters)
        'new_efficiency',       // New stove efficiency as decimal (e.g., 0.25 for 25%)
        'start_date',           // Date when cleaner cooking intervention started
        'emissions_after',      // Monthly emissions after intervention (tCO₂e)
        'credits_earned',       // Carbon credits earned per month (tCO₂e)
    ];

    /**
     * The attributes that should be cast to native types.
     * Automatically converts database values to appropriate PHP types
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fuel_use_project' => 'float',    // Cast to float for calculations
        'new_efficiency' => 'float',      // Cast to float for calculations
        'emissions_after' => 'float',     // Cast to float for calculations
        'credits_earned' => 'float',      // Cast to float for calculations
        'start_date' => 'date',           // Cast to Carbon date object
        'created_at' => 'datetime',       // Cast to Carbon datetime object
        'updated_at' => 'datetime',
        'monthly_emissions' => 'float',
        'annual_emissions' => 'float',      // Cast to Carbon datetime object
    ];

    /**
     * Define relationship with User model
     * Many project data entries belong to one user (Many-to-One relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // Returns the user who owns this project data record
        return $this->belongsTo(User::class);
    }

    /**
     * Define relationship with BaselineData model
     * Project data references baseline data for comparison
     * Using shorter method name 'baseline' for cleaner code
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function baseline()
    {
        // Returns the baseline data record for this user (for comparison)
        return $this->belongsTo(BaselineData::class, 'user_id', 'user_id');
    }

    /**
     * Calculate monthly project emissions using Verra VM0042 methodology
     * Formula: E_project = (F_project × EF_fuel) / η_project
     *
     * @return float Monthly emissions in tCO₂e after intervention
     */
    public function calculateMonthlyProjectEmissions(): float
    {
        // Get the emission factor for the new fuel type (defaults to 0 if not found)
        $emissionFactor = BaselineData::$emissionFactors[strtolower($this->new_fuel_type)] ?? 0;

        // Calculate monthly fuel consumption (daily use × 30 days)
        $monthlyFuelUse = $this->fuel_use_project * 30;

        // Get efficiency value (use stored efficiency or default if not set)
        $efficiency = $this->new_efficiency > 0 ? $this->new_efficiency :
                     (BaselineData::$defaultEfficiencies[strtolower($this->new_stove_type)] ?? 0.25);

        // Calculate emissions using Verra VM0042 formula
        // E_project = (Monthly_Fuel_Use × Emission_Factor) / Efficiency
        $emissions = ($monthlyFuelUse * $emissionFactor) / $efficiency;

        return round($emissions, 4); // Round to 4 decimal places for precision
    }

    /**
     * Calculate carbon credits earned (emission reduction)
     * Formula: ER = E_baseline - E_project
     *
     * @return float Monthly carbon credits earned in tCO₂e
     */
    public function calculateCarbonCredits(): float
    {
        // Get the user's baseline data for comparison using the baseline relationship
        $baselineData = $this->user->baseline ?? null;

        // Alternative: get latest baseline data if multiple records exist
        if (!$baselineData) {
            $baselineData = $this->user->baselineData()->latest()->first();
        }

        // If no baseline data exists, return 0
        if (!$baselineData) {
            return 0;
        }

        // Calculate baseline emissions
        $baselineEmissions = $baselineData->calculateMonthlyEmissions();

        // Calculate project emissions
        $projectEmissions = $this->calculateMonthlyProjectEmissions();

        // Calculate emission reduction (carbon credits)
        $emissionReduction = $baselineEmissions - $projectEmissions;

        // Ensure we don't have negative credits (project should reduce emissions)
        return max(0, round($emissionReduction, 4));
    }

    /**
     * Calculate annual carbon credits earned
     * Simply multiplies monthly credits by 12
     *
     * @return float Annual carbon credits earned in tCO₂e
     */
    public function calculateAnnualCarbonCredits(): float
    {
        return $this->calculateCarbonCredits() * 12;
    }

    /**
     * Calculate percentage reduction in emissions
     * Shows how much emissions were reduced compared to baseline
     *
     * @return float Percentage reduction (0-100)
     */
    public function calculatePercentageReduction(): float
    {
        // Get the user's baseline data for comparison using the baseline relationship
        $baselineData = $this->user->baseline ?? null;

        // Alternative: get latest baseline data if multiple records exist
        if (!$baselineData) {
            $baselineData = $this->user->baselineData()->latest()->first();
        }

        // If no baseline data exists, return 0
        if (!$baselineData) {
            return 0;
        }

        // Calculate baseline emissions
        $baselineEmissions = $baselineData->calculateMonthlyEmissions();

        // If baseline is 0, avoid division by zero
        if ($baselineEmissions == 0) {
            return 0;
        }

        // Calculate project emissions
        $projectEmissions = $this->calculateMonthlyProjectEmissions();

        // Calculate percentage reduction: ((Baseline - Project) / Baseline) × 100
        $percentageReduction = (($baselineEmissions - $projectEmissions) / $baselineEmissions) * 100;

        // Ensure percentage is between 0 and 100
        return max(0, min(100, round($percentageReduction, 2)));
    }

    /**
     * Get formatted project emission factor for display
     * Returns the emission factor with proper formatting
     *
     * @return string Formatted emission factor
     */
    public function getFormattedProjectEmissionFactor(): string
    {
        $factor = BaselineData::$emissionFactors[strtolower($this->new_fuel_type)] ?? 0;
        return number_format($factor, 6) . ' tCO₂e/kg';
    }

    /**
     * Get formatted project efficiency percentage for display
     * Converts decimal efficiency to percentage format
     *
     * @return string Formatted efficiency percentage
     */
    public function getFormattedProjectEfficiency(): string
    {
        $efficiency = $this->new_efficiency > 0 ? $this->new_efficiency :
                     (BaselineData::$defaultEfficiencies[strtolower($this->new_stove_type)] ?? 0.25);
        return (($efficiency * 100) . '%');
    }

    /**
     * Get cumulative carbon credits earned since start date
     * Calculates total credits from start date to current date
     *
     * @return float Total carbon credits earned since intervention started
     */
    public function getCumulativeCarbonCredits(): float
    {
        // Calculate months since intervention started
        // Fixed: Call diffInMonths from start_date to now (correct direction)
        $monthsSinceStart = $this->start_date->diffInMonths(now());

        // If intervention just started, count at least 1 month
        $monthsSinceStart = max(1, $monthsSinceStart);

        // Calculate total credits earned
        $monthlyCredits = $this->calculateCarbonCredits();
        $totalCredits = $monthlyCredits * $monthsSinceStart;

        return round($totalCredits, 4);
    }

    /**
     * Check if this project intervention is more efficient than baseline
     * Compares project efficiency with baseline efficiency
     *
     * @return bool True if project is more efficient than baseline
     */
    public function isMoreEfficient(): bool
    {
        // Get the user's baseline data for comparison using the baseline relationship
        $baselineData = $this->user->baseline ?? null;

        // Alternative: get latest baseline data if multiple records exist
        if (!$baselineData) {
            $baselineData = $this->user->baselineData()->latest()->first();
        }

        // If no baseline data exists, assume project is better
        if (!$baselineData) {
            return true;
        }

        // Get baseline efficiency
        $baselineEfficiency = $baselineData->efficiency > 0 ? $baselineData->efficiency :
                             (BaselineData::$defaultEfficiencies[strtolower($baselineData->stove_type)] ?? 0.10);

        // Get project efficiency
        $projectEfficiency = $this->new_efficiency > 0 ? $this->new_efficiency :
                            (BaselineData::$defaultEfficiencies[strtolower($this->new_stove_type)] ?? 0.25);

        // Return true if project efficiency is higher than baseline
        return $projectEfficiency > $baselineEfficiency;
    }

    /**
     * Boot method to automatically calculate emissions and credits when model is saved
     * Laravel model event - fires before saving to database
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot(); // Call parent boot method (Polymorphism)

        // Automatically calculate emissions and credits before saving
        static::saving(function ($projectData) {
            // Calculate and store project emissions
            $projectData->emissions_after = $projectData->calculateMonthlyProjectEmissions();

            // Calculate and store carbon credits earned
            $projectData->credits_earned = $projectData->calculateCarbonCredits();
        });
    }
}
