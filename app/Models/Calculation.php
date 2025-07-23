<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'data',
        'monthly_emissions',
        'annual_emissions',
        'weekly_emissions',
        'emission_reduction',
        'credit_earned',
    ];

    protected $casts = [
        'data' => 'array',
        'monthly_emissions' => 'float',
        'annual_emissions' => 'float',
        'weekly_emissions' => 'float',
        'emission_reduction' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeDisplayAttribute()
    {
        $types = [
            'baseline' => 'Baseline Data',
            'project' => 'Clean Cooking Project',
            'quick' => 'Quick Calculation',
            'weekly_update' => 'Weekly Update',
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getTypeBadgeClassAttribute()
    {
        $classes = [
            'baseline' => 'bg-danger',
            'project' => 'bg-success',
            'quick' => 'bg-info',
            'weekly_update' => 'bg-warning',
        ];

        return $classes[$this->type] ?? 'bg-secondary';
    }

    public function getMonthlyEmissionsFormattedAttribute()
    {
        return $this->monthly_emissions ? number_format($this->monthly_emissions, 3) . ' tCO₂e' : 'N/A';
    }

    public function getAnnualEmissionsFormattedAttribute()
    {
        return $this->annual_emissions ? number_format($this->annual_emissions, 3) . ' tCO₂e' : 'N/A';
    }

    public function getWeeklyEmissionsFormattedAttribute()
    {
        return $this->weekly_emissions ? number_format($this->weekly_emissions, 3) . ' tCO₂e' : 'N/A';
    }

    public function getEmissionReductionFormattedAttribute()
    {
        return $this->emission_reduction ? number_format($this->emission_reduction, 3) . ' tCO₂e' : 'N/A';
    }

    public function getFuelTypeAttribute()
    {
        return $this->data['fuel_type'] ?? 'N/A';
    }

    public function getStoveTypeAttribute()
    {
        return $this->data['stove_type'] ?? 'N/A';
    }

    public function getDailyFuelUseAttribute()
    {
        return $this->data['daily_fuel_use'] ?? 0;
    }

    public function getHouseholdSizeAttribute()
    {
        return $this->data['household_size'] ?? 'N/A';
    }
}
