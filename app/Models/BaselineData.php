<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaselineData extends Model
{
    use HasFactory;

    protected $table = 'baseline_data';

    protected $fillable = [
        'user_id',
        'stove_type',
        'fuel_type',
        'daily_fuel_use',
        'daily_hours',              // Match migration column name
        'efficiency',               // Match migration column name
        'household_size',
        'monthly_emissions',
        'annual_emissions',
        'emission_total',
        'emission_factor'
    ];

    protected $casts = [
        'daily_fuel_use' => 'float',
        'daily_hours' => 'float',           // Match migration column name
        'efficiency' => 'float',            // Match migration column name
        'household_size' => 'integer',
        'monthly_emissions' => 'float',
        'annual_emissions' => 'float',
        'emission_total' => 'float',
        'emission_factor' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Emission factors based on Verra VM0042 methodology (tCO₂e/kg or kWh)
    public static $emissionFactors = [
        'wood' => 0.001747,
        'charcoal' => 0.00674,
        'lpg' => 0.002983,
        'electricity' => 0.00085,
        'kerosene' => 0.00271,
        'ethanol' => 0.00195,
    ];

    // Default stove efficiencies
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
        '3-stone fire' => 0.10,
        'charcoal brazier' => 0.12,
        'improved biomass stove' => 0.25,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calculateMonthlyEmissions(): float
    {
        $emissionFactor = self::$emissionFactors[strtolower($this->fuel_type)] ?? 0;
        $monthlyFuelUse = $this->daily_fuel_use * 30;
        $efficiency = $this->getEffectiveEfficiency();

        if ($efficiency <= 0) return 0;

        return round(($monthlyFuelUse * $emissionFactor) / $efficiency, 6);
    }

    public function calculateAnnualEmissions(): float
    {
        return $this->calculateMonthlyEmissions() * 12;
    }

    public function getEffectiveEfficiency(): float
    {
        if ($this->efficiency > 0) {
            return $this->efficiency;
        }

        $stoveType = strtolower($this->stove_type);
        if (isset(self::$defaultEfficiencies[$stoveType])) {
            return self::$defaultEfficiencies[$stoveType];
        }

        $stoveTypeSpaces = str_replace('_', ' ', $stoveType);
        return self::$defaultEfficiencies[$stoveTypeSpaces] ?? 0.10;
    }

    // Accessor for backward compatibility with daily_cooking_hours
    public function getDailyCookingHoursAttribute()
    {
        return $this->daily_hours;
    }

    public function setDailyCookingHoursAttribute($value)
    {
        $this->attributes['daily_hours'] = $value;
    }

    // Accessor for backward compatibility with stove_efficiency
    public function getStoveEfficiencyAttribute()
    {
        return $this->efficiency;
    }

    public function setStoveEfficiencyAttribute($value)
    {
        $this->attributes['efficiency'] = $value;
    }

    public function getFormattedEmissionFactor(): string
    {
        $factor = self::$emissionFactors[strtolower($this->fuel_type)] ?? 0;
        return number_format($factor, 6) . ' tCO₂e/kg';
    }

    public function getFormattedEfficiency(): string
    {
        return number_format($this->getEffectiveEfficiency() * 100, 1) . '%';
    }

    public function getFormattedDailyFuelUse(): string
    {
        $unit = $this->fuel_type === 'electricity' ? 'kWh' : 'kg';
        return number_format($this->daily_fuel_use, 2) . " {$unit}/day";
    }

    public function getFormattedMonthlyEmissions(): string
    {
        $emissions = $this->monthly_emissions ?? $this->calculateMonthlyEmissions();
        return number_format($emissions, 4) . ' tCO₂e/month';
    }

    public function getFormattedAnnualEmissions(): string
    {
        $emissions = $this->annual_emissions ?? $this->calculateAnnualEmissions();
        return number_format($emissions, 2) . ' tCO₂e/year';
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function isComplete(): bool
    {
        return !empty($this->stove_type) &&
               !empty($this->fuel_type) &&
               $this->daily_fuel_use > 0 &&
               $this->daily_hours > 0 &&
               $this->household_size > 0;
    }

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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($baselineData) {
            $monthlyEmissions = $baselineData->calculateMonthlyEmissions();
            $baselineData->monthly_emissions = $monthlyEmissions;
            $baselineData->annual_emissions = $monthlyEmissions * 12;
            $baselineData->emission_total = $monthlyEmissions; // backward compatibility
        });
    }
}
