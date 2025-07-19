<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_data_id',
        'fuel_consumption',
        'cooking_hours',
        'stove_usage_percentage',
        'estimated_emissions',
        'notes'
    ];

    protected $casts = [
        'fuel_consumption' => 'float',
        'cooking_hours' => 'float',
        'stove_usage_percentage' => 'float',
        'estimated_emissions' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projectData()
    {
        return $this->belongsTo(ProjectData::class);
    }
}
