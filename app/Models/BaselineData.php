<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaselineData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stove_type',
        'fuel_type',
        'daily_fuel_use',
        'daily_cooking_hours', // Standardized field name
        'stove_efficiency',
        'household_size',
        'monthly_emissions', // Added from controller usage
        'annual_emissions',  // Added from controller usage
        'emission_total',    // Keep for backward compatibility
    ];

    protected $casts = [
        'daily_fuel_use' => 'float',
        'daily_cooking_hours' => 'float',
        'stove_efficiency' => 'float',
        'household_size' => 'integer',
        'monthly_emissions' => 'float',
        'annual_emissions' => 'float',
        'emission_total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Emission factors based on Verra VM0042 methodology (tCO₂e/kg)
    public static $emissionFactors = [
        'wood' => 0.001747,
        'charcoal' => 0.00674,
        'lpg' => 0.002983,
        'electricity' => 0.00085, // Zambia grid average (tCO₂e/kWh)
        'kerosene' => 0.00271,    // Updated to match controller
        'ethanol' => 0.00195,     // Updated to match controller
    ];

    // Default stove efficiencies (as decimal, e.g., 0.10 = 10%)
    public static $defaultEfficiencies = [
        '3_stone_fire' => 0.10,
        'charcoal_brazier' => 0.10,
        'kerosene_stove' => 0.45,
        'lpg_stove' => 0.55,
        'electric_stove' => 0.75,
        'improved_biomass' => 0.25,
        'improved_charcoal' => 0.25,
        'biogas_stove' => 0.60,
        'induction_cooker' => 0.85,
        // Backward compatibility aliases
        '3-stone fire' => 0.10,
        'charcoal brazier' => 0.12,
        'improved biomass stove' => 0.25,
    ];

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate monthly emissions using Verra VM0042 methodology
     * Formula: E_baseline = (F_baseline × EF_fuel) / η_baseline
     */
    public function calculateMonthlyEmissions(): float
    {
        $emissionFactor = self::$emissionFactors[strtolower($this->fuel_type)] ?? 0;
        $monthlyFuelUse = $this->daily_fuel_use * 30;

        // Use stored efficiency or default if not set
        $efficiency = $this->getEffectiveEfficiency();

        if ($efficiency <= 0) {
            return 0;
        }

        $emissions = ($monthlyFuelUse * $emissionFactor) / $efficiency;
        return round($emissions, 6); // Increased precision to match controller
    }

    /**
     * Calculate annual emissions
     */
    public function calculateAnnualEmissions(): float
    {
        return $this->calculateMonthlyEmissions() * 12;
    }

    /**
     * Get the effective efficiency value
     */
    public function getEffectiveEfficiency(): float
    {
        if ($this->stove_efficiency > 0) {
            return $this->stove_efficiency;
        }

        // Try exact match first
        $stoveType = strtolower($this->stove_type);
        if (isset(self::$defaultEfficiencies[$stoveType])) {
            return self::$defaultEfficiencies[$stoveType];
        }

        // Try with underscores replaced by spaces
        $stoveTypeSpaces = str_replace('_', ' ', $stoveType);
        if (isset(self::$defaultEfficiencies[$stoveTypeSpaces])) {
            return self::$defaultEfficiencies[$stoveTypeSpaces];
        }

        // Default fallback
        return 0.10;
    }

    /**
     * Get formatted emission factor for display
     */
    public function getFormattedEmissionFactor(): string
    {
        $factor = self::$emissionFactors[strtolower($this->fuel_type)] ?? 0;
        return number_format($factor, 6) . ' tCO₂e/kg';
    }

    /**
     * Get formatted efficiency for display
     */
    public function getFormattedEfficiency(): string
    {
        $efficiency = $this->getEffectiveEfficiency();
        return number_format($efficiency * 100, 1) . '%';
    }

    /**
     * Get formatted daily fuel use for display
     */
    public function getFormattedDailyFuelUse(): string
    {
        $unit = $this->fuel_type === 'electricity' ? 'kWh' : 'kg';
        return number_format($this->daily_fuel_use, 2) . ' ' . $unit . '/day';
    }

    /**
     * Get formatted monthly emissions for display
     */
    public function getFormattedMonthlyEmissions(): string
    {
        $emissions = $this->monthly_emissions ?? $this->calculateMonthlyEmissions();
        return number_format($emissions, 4) . ' tCO₂e/month';
    }

    /**
     * Get formatted annual emissions for display
     */
    public function getFormattedAnnualEmissions(): string
    {
        $emissions = $this->annual_emissions ?? $this->calculateAnnualEmissions();
        return number_format($emissions, 2) . ' tCO₂e/year';
    }

    /**
     * Scope for filtering by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for getting latest baseline data
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if this baseline data is complete
     */
    public function isComplete(): bool
    {
        return !empty($this->stove_type) &&
               !empty($this->fuel_type) &&
               $this->daily_fuel_use > 0 &&
               $this->daily_cooking_hours > 0 &&
               $this->household_size > 0;
    }

    /**
     * Get stove type display name
     */
    public function getStoveTypeDisplayName(): string
    {
        $displayNames = [
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

        return $displayNames[$this->stove_type] ?? ucwords(str_replace('_', ' ', $this->stove_type));
    }

    /**
     * Get fuel type display name
     */
    public function getFuelTypeDisplayName(): string
    {
        $displayNames = [
            'wood' => 'Wood',
            'charcoal' => 'Charcoal',
            'lpg' => 'LPG',
            'electricity' => 'Electricity',
            'kerosene' => 'Kerosene',
            'ethanol' => 'Ethanol',
        ];

        return $displayNames[$this->fuel_type] ?? ucfirst($this->fuel_type);
    }

    /**
     * Model boot method to auto-calculate emissions
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($baselineData) {
            // Auto-calculate emissions when saving
            $monthlyEmissions = $baselineData->calculateMonthlyEmissions();
            $baselineData->monthly_emissions = $monthlyEmissions;
            $baselineData->annual_emissions = $monthlyEmissions * 12;
            $baselineData->emission_total = $monthlyEmissions; // For backward compatibility
        });
    }

    /**
     * Accessor for backward compatibility with 'daily_hours' field
     */
    public function getDailyHoursAttribute()
    {
        return $this->daily_cooking_hours;
    }

    /**
     * Mutator for backward compatibility with 'daily_hours' field
     */
    public function setDailyHoursAttribute($value)
    {
        $this->attributes['daily_cooking_hours'] = $value;
    }

    /**
     * Accessor for backward compatibility with 'efficiency' field
     */
    public function getEfficiencyAttribute()
    {
        return $this->stove_efficiency;
    }

    /**
     * Mutator for backward compatibility with 'efficiency' field
     */
    public function setEfficiencyAttribute($value)
    {
        $this->attributes['stove_efficiency'] = $value;
    }
}
