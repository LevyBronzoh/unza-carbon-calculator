<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * ProjectData Model for UNZA Carbon Calculator
 *
 * Handles storage and calculation of project cooking emissions data
 * (after intervention with cleaner cooking methods)
 *
 * @author Levy Bronzoh, Climate Yanga
 * @version 1.1
 * @since 2025-07-12
 */
class ProjectData extends Model
{
    use HasFactory;

    protected $table = 'project_data';

    /**
     * The attributes that are mass assignable
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',              // Foreign key linking to User model
        'new_stove_type',       // Type of new/improved stove used
        'new_fuel_type',        // Type of new fuel used after intervention
        'fuel_use_project',     // Amount of fuel used per day after intervention (kg or liters)
        'new_efficiency',       // New stove efficiency as decimal (e.g., 0.25 for 25%)
        'start_date',           // Date when cleaner cooking intervention started
        'monthly_emissions',    // Monthly emissions after intervention (tCO₂e)
        'annual_emissions',     // Annual emissions after intervention (tCO₂e)
        'monthly_reduction',    // Monthly emission reduction (tCO₂e)
        'annual_reduction',     // Annual emission reduction (tCO₂e)
        'percentage_reduction', // Percentage reduction in emissions
        'total_credits',        // Total carbon credits earned
        'emission_factor',      // Emission factor for the new fuel type
        'emissions_after',      // (Legacy) Monthly emissions after intervention
        'credits_earned'        // (Legacy) Carbon credits earned per month
    ];

    /**
     * The attributes that should be cast
     * @var array<string, string>
     */
    protected $casts = [
        'fuel_use_project' => 'decimal:4',
        'new_efficiency' => 'decimal:4',
        'monthly_emissions' => 'decimal:4',
        'annual_emissions' => 'decimal:4',
        'monthly_reduction' => 'decimal:4',
        'annual_reduction' => 'decimal:4',
        'percentage_reduction' => 'decimal:2',
        'total_credits' => 'decimal:4',
        'emission_factor' => 'decimal:6',
        'emissions_after' => 'decimal:4',
        'credits_earned' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_date' => 'datetime',
    ];

    /**
     * Emission factors (tCO₂e per kg or liter)
     * @var array<string, float>
     */
    public static $emissionFactors = [
        'wood' => 0.001747,
        'charcoal' => 0.00674,
        'lpg' => 0.002983,
        'electricity' => 0.00085,
        'kerosene' => 0.002533,
        'ethanol' => 0.001915
    ];

    /**
     * Default stove efficiencies
     * @var array<string, float>
     */
    public static $defaultEfficiencies = [
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

    /**
     * Relationship: Project data belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Project data references baseline data
     */
    public function baseline()
    {
        return $this->belongsTo(BaselineData::class, 'user_id', 'user_id');
    }

    /**
     * Calculate monthly project emissions using Verra VM0042 methodology
     * Formula: E_project = (F_project × EF_fuel) / η_project
     */
    public function calculateMonthlyProjectEmissions(): float
    {
        $emissionFactor = self::$emissionFactors[strtolower($this->new_fuel_type)] ?? 0;
        $monthlyFuelUse = $this->fuel_use_project * 30;
        $efficiency = $this->new_efficiency > 0 ? $this->new_efficiency :
                     (self::$defaultEfficiencies[strtolower($this->new_stove_type)] ?? 0.25);

        return round(($monthlyFuelUse * $emissionFactor) / $efficiency, 4);
    }

    /**
     * Calculate carbon credits earned (emission reduction)
     * Formula: ER = E_baseline - E_project
     */
    public function calculateCarbonCredits(): float
    {
        $baselineData = BaselineData::where('user_id', $this->user_id)->first();

        if (!$baselineData) {
            return 0;
        }

        $emissionReduction = $baselineData->calculateMonthlyEmissions() -
                           $this->calculateMonthlyProjectEmissions();

        return max(0, round($emissionReduction, 4));
    }

    /**
     * Calculate annual carbon credits
     */
    public function calculateAnnualCarbonCredits(): float
    {
        return $this->calculateCarbonCredits() * 12;
    }

    /**
     * Calculate percentage reduction in emissions
     */
    public function calculatePercentageReduction(): float
    {
        $baselineData = BaselineData::where('user_id', $this->user_id)->first();

        if (!$baselineData) {
            return 0;
        }

        $baselineEmissions = $baselineData->calculateMonthlyEmissions();

        if ($baselineEmissions == 0) {
            return 0;
        }

        $percentage = (($baselineEmissions - $this->calculateMonthlyProjectEmissions()) /
                      $baselineEmissions) * 100;

        return max(0, min(100, round($percentage, 2)));
    }

    /**
     * Get formatted project emission factor
     */
    public function getFormattedProjectEmissionFactor(): string
    {
        $factor = self::$emissionFactors[strtolower($this->new_fuel_type)] ?? 0;
        return number_format($factor, 6) . ' tCO₂e/kg';
    }

    /**
     * Get formatted project efficiency percentage
     */
    public function getFormattedProjectEfficiency(): string
    {
        $efficiency = $this->new_efficiency > 0 ? $this->new_efficiency :
                     (self::$defaultEfficiencies[strtolower($this->new_stove_type)] ?? 0.25);
        return ($efficiency * 100) . '%';
    }

    /**
     * Get cumulative carbon credits earned since start date
     */
    public function getCumulativeCarbonCredits(): float
    {
        // Check if start_date exists and is valid
        if (!$this->start_date) {
            return 0.0;
        }

        // Ensure we have a Carbon instance (works with both strings and Carbon objects)
        $startDate = $this->start_date instanceof \Carbon\Carbon
            ? $this->start_date
            : \Carbon\Carbon::parse($this->start_date);

        // Calculate months since start (minimum 1 month)
        $monthsSinceStart = max(1, $startDate->diffInMonths(now()));

        return round($this->calculateCarbonCredits() * $monthsSinceStart, 4);
    }

    /**
     * Check if project is more efficient than baseline
     */
    public function isMoreEfficient(): bool
    {
        $baselineData = BaselineData::where('user_id', $this->user_id)->first();

        if (!$baselineData) {
            return true;
        }

        $baselineEff = $baselineData->efficiency > 0 ? $baselineData->efficiency :
                      (BaselineData::$defaultEfficiencies[strtolower($baselineData->stove_type)] ?? 0.10);

        $projectEff = $this->new_efficiency > 0 ? $this->new_efficiency :
                     (self::$defaultEfficiencies[strtolower($this->new_stove_type)] ?? 0.25);

        return $projectEff > $baselineEff;
    }

    /**
     * Model event: Calculate values before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($project) {
            $project->emissions_after = $project->calculateMonthlyProjectEmissions();
            $project->credits_earned = $project->calculateCarbonCredits();

            // Calculate and store all metrics
            $project->monthly_emissions = $project->calculateMonthlyProjectEmissions();
            $project->annual_emissions = $project->monthly_emissions * 12;

            if ($baseline = BaselineData::where('user_id', $project->user_id)->first()) {
                $project->monthly_reduction = $baseline->monthly_emissions - $project->monthly_emissions;
                $project->annual_reduction = $project->monthly_reduction * 12;
                $project->percentage_reduction = $project->calculatePercentageReduction();
                $project->total_credits = $project->getCumulativeCarbonCredits();
                $project->emission_factor = self::$emissionFactors[strtolower($project->new_fuel_type)] ?? 0;
            }
        });
    }
}
