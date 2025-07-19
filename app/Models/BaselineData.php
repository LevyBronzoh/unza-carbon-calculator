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
        'daily_hours',
        'stove_efficiency',  // Changed from 'efficiency' to match migration
        'household_size',
        'emission_total',
    ];

    protected $casts = [
        'daily_fuel_use' => 'float',
        'daily_hours' => 'float',
        'stove_efficiency' => 'float',  // Updated to match field name
        'household_size' => 'integer',
        'emission_total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static $emissionFactors = [
        'wood' => 0.001747,
        'charcoal' => 0.00674,
        'lpg' => 0.002983,
        'electricity' => 0.00085,
        'kerosene' => 0.00264,
        'ethanol' => 0.00151,
    ];

    public static $defaultEfficiencies = [
        '3-stone fire' => 0.10,
        'charcoal brazier' => 0.12,
        'kerosene stove' => 0.45,
        'lpg stove' => 0.55,
        'electric stove' => 0.70,
        'improved biomass stove' => 0.25,
    ];

    public function calculateMonthlyEmissions(): float
    {
        $emissionFactor = self::$emissionFactors[strtolower($this->fuel_type)] ?? 0;
        $monthlyFuelUse = $this->daily_fuel_use * 30;

        // Use stored efficiency or default if not set
        $efficiency = $this->stove_efficiency > 0 ? $this->stove_efficiency :
                     (self::$defaultEfficiencies[strtolower($this->stove_type)] ?? 0.10);

        $emissions = ($monthlyFuelUse * $emissionFactor) / $efficiency;
        return round($emissions, 4);
    }

    public function calculateAnnualEmissions(): float
    {
        return $this->calculateMonthlyEmissions() * 12;
    }

    public function getFormattedEmissionFactor(): string
    {
        $factor = self::$emissionFactors[strtolower($this->fuel_type)] ?? 0;
        return number_format($factor, 6) . ' tCO₂e/kg';
    }

    public function getFormattedEfficiency(): string
    {
        $efficiency = $this->stove_efficiency > 0 ? $this->stove_efficiency :
                     (self::$defaultEfficiencies[strtolower($this->stove_type)] ?? 0.10);
        return ($efficiency * 100) . '%';
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($baselineData) {
            $baselineData->emission_total = $baselineData->calculateMonthlyEmissions();
        });
    }
}
