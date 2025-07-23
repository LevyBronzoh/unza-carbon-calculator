<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * EmissionsCalculator Service - UNZA Carbon Calculator
 *
 * This service class implements the Verra VM0042 methodology for calculating
 * carbon emissions from cooking activities and determining emission reductions
 *
 * Developed by Levy Bronzoh, Climate Yanga
 * Version: 2.0
 * Date: July 2025
 */
class EmissionsCalculator
{
    /**
     * @var array
     * Emission factors for different fuel types (tCO₂e/kg or tCO₂e/l)
     * Based on Verra VM0042 methodology and Zambian context
     */
    private const EMISSION_FACTORS = [
        'Wood' => 0.001747,        // tCO₂e/kg - Wood biomass
        'Charcoal' => 0.00674,     // tCO₂e/kg - Charcoal
        'LPG' => 0.002983,         // tCO₂e/kg - Liquefied Petroleum Gas
        'Electricity' => 0.00085,  // tCO₂e/kWh - Zambia grid average
        'Kerosene' => 0.00264,     // tCO₂e/l - Kerosene
        'Ethanol' => 0.00189,      // tCO₂e/l - Ethanol fuel
        'Other' => 0.002000        // default
    ];

    /**
     * @var array
     * Default stove efficiencies (as decimals)
     */
    private const DEFAULT_EFFICIENCIES = [
        '3-Stone Fire' => 0.10,
        'Charcoal Brazier' => 0.15,
        'Kerosene Stove' => 0.45,
        'LPG Stove' => 0.55,
        'Electric Stove' => 0.75,
        'Improved Biomass Stove' => 0.35,
        'Ethanol Stove' => 0.50
    ];

    /**
     * @var int
     * Number of days in a month for calculations
     */
    private $daysPerMonth = 30;

    /**
     * @var int
     * Number of months in a year for annual calculations
     */
    private $monthsPerYear = 12;

    /**
     * Calculate baseline emissions using Verra VM0042 methodology
     * Formula: E_baseline = (F_baseline × EF_fuel) / η_baseline
     *
     * @param float $dailyFuelUse - Daily fuel consumption (kg or liters)
     * @param string $fuelType - Type of fuel being used
     * @param string $stoveType - Type of stove being used
     * @param float|null $efficiency - Custom efficiency (decimal or percentage)
     * @param string $period - Calculation period ('monthly' or 'annual')
     * @return array - Detailed emissions results
     */
    public function calculateBaselineEmissions($dailyFuelUse, $fuelType, $stoveType, $efficiency = null, $period = 'monthly')
    {
        // Get emission factor for fuel type
        $emissionFactor = $this->getEmissionFactor($fuelType);

        // Get stove efficiency (use provided or default)
        $stoveEfficiency = $this->getStoveEfficiency($stoveType, $efficiency);

        // Calculate period multiplier
        $periodMultiplier = $this->getPeriodMultiplier($period);

        // Calculate total fuel consumption for the period
        $periodFuelConsumption = $dailyFuelUse * $periodMultiplier;

        // Calculate emissions using Verra VM0042 formula
        $emissions = ($periodFuelConsumption * $emissionFactor) / $stoveEfficiency;

        // Calculate annual emissions if monthly was requested
        $annualEmissions = $period === 'monthly'
            ? $this->convertToAnnual($emissions)
            : $this->convertToMonthly($emissions);

        return [
            'emissions' => round($emissions, 6),
            'annual_emissions' => round($annualEmissions, 6),
            'emission_factor_used' => $emissionFactor,
            'efficiency_used' => $stoveEfficiency,
            'fuel_consumption' => $periodFuelConsumption,
            'period' => $period
        ];
    }

    /**
     * Calculate project emissions using Verra VM0042 methodology
     * Formula: E_project = (F_project × EF_fuel) / η_project
     *
     * @param float $dailyFuelUse - Daily fuel consumption (kg or liters)
     * @param string $fuelType - Type of fuel being used
     * @param string $stoveType - Type of stove being used
     * @param float|null $efficiency - Custom efficiency (decimal or percentage)
     * @param string $period - Calculation period ('monthly' or 'annual')
     * @return array - Detailed emissions results
     */
    public function calculateProjectEmissions($dailyFuelUse, $fuelType, $stoveType, $efficiency = null, $period = 'monthly')
    {
        // Get emission factor for fuel type
        $emissionFactor = $this->getEmissionFactor($fuelType);

        // Get stove efficiency (use provided or default)
        $stoveEfficiency = $this->getStoveEfficiency($stoveType, $efficiency);

        // Calculate period multiplier
        $periodMultiplier = $this->getPeriodMultiplier($period);

        // Calculate total fuel consumption for the period
        $periodFuelConsumption = $dailyFuelUse * $periodMultiplier;

        // Calculate emissions using Verra VM0042 formula
        $emissions = ($periodFuelConsumption * $emissionFactor) / $stoveEfficiency;

        // Calculate annual emissions if monthly was requested
        $annualEmissions = $period === 'monthly'
            ? $this->convertToAnnual($emissions)
            : $this->convertToMonthly($emissions);

        return [
            'emissions' => round($emissions, 6),
            'annual_emissions' => round($annualEmissions, 6),
            'emission_factor_used' => $emissionFactor,
            'efficiency_used' => $stoveEfficiency,
            'fuel_consumption' => $periodFuelConsumption,
            'period' => $period
        ];
    }

    /**
     * Calculate comprehensive emissions data for a user
     *
     * @param array $baselineData - Baseline cooking data
     * @param array $projectData - Project intervention data
     * @param string $period - Calculation period
     * @return array - Comprehensive emissions calculation results
     */
    public function calculateComprehensiveEmissions($baselineData, $projectData, $period = 'monthly')
    {
        // Calculate baseline emissions
        $baselineResults = $this->calculateBaselineEmissions(
            $baselineData['daily_fuel_use'],
            $baselineData['fuel_type'],
            $baselineData['stove_type'],
            $baselineData['efficiency'] ?? null,
            $period
        );

        // Calculate project emissions
        $projectResults = $this->calculateProjectEmissions(
            $projectData['daily_fuel_use'],
            $projectData['fuel_type'],
            $projectData['stove_type'],
            $projectData['efficiency'] ?? null,
            $period
        );

        // Calculate emission reduction
        $emissionReduction = $this->calculateEmissionReduction(
            $baselineResults['emissions'],
            $projectResults['emissions']
        );

        // Calculate percentage reduction
        // First calculate the raw emission reduction value (float)
$rawReduction = max(0, $baselineResults['emissions'] - $projectResults['emissions']);

// Then get the full reduction data (array) if needed
$reductionData = $this->calculateEmissionReduction(
    $baselineResults['emissions'],
    $projectResults['emissions'],
    $startDate ?? null
);

// Calculate percentage
$percentageReduction = $baselineResults['emissions'] > 0
    ? ($rawReduction / $baselineResults['emissions']) * 100
    : 0;

return [
    'baseline' => $baselineResults,
    'project' => $projectResults,
    'emission_reduction' => round($rawReduction, 6),
    'percentage_reduction' => round($percentageReduction, 2),
    'period' => $period,
    'calculation_date' => now()->toDateString(),
];
    }

    /**
     * Calculate emission reductions (carbon credits)
     * Formula: ER = E_baseline - E_project
     *
     * @param float $baselineEmissions - Baseline emissions (tCO₂e)
     * @param float $projectEmissions - Project emissions (tCO₂e)
     * @param string|null $startDate - Optional start date for cumulative credits
     * @return array - Reduction details
     */
    public function calculateEmissionReduction($baselineEmissions, $projectEmissions, $startDate = null)
    {
        $reduction = max(0, $baselineEmissions - $projectEmissions);

        // Calculate percentage reduction
        $percentageReduction = $baselineEmissions > 0
            ? ($reduction / $baselineEmissions) * 100
            : 0;

        // Calculate total credits earned since start date if provided
        $totalCredits = $reduction;
        if ($startDate) {
            $monthsElapsed = now()->diffInMonths($startDate) + 1; // Include current month
            $totalCredits = $reduction * $monthsElapsed;
        }

        return [
            'reduction' => round($reduction, 6),
            'percentage_reduction' => round($percentageReduction, 2),
            'total_credits' => round($totalCredits, 6)
        ];
    }

    /**
     * Convert monthly emissions to annual emissions
     *
     * @param float $monthlyEmissions - Monthly emissions value
     * @return float - Annual emissions value
     */
    public function convertToAnnual($monthlyEmissions)
    {
        return $monthlyEmissions * $this->monthsPerYear;
    }

    /**
     * Convert annual emissions to monthly emissions
     *
     * @param float $annualEmissions - Annual emissions value
     * @return float - Monthly emissions value
     */
    public function convertToMonthly($annualEmissions)
    {
        return $annualEmissions / $this->monthsPerYear;
    }

    /**
     * Get stove efficiency
     *
     * @param string $stoveType - Type of stove
     * @param float|null $efficiency - Custom efficiency value
     * @return float - Efficiency value (decimal)
     */
    private function getStoveEfficiency($stoveType, $efficiency = null)
    {
        if ($efficiency !== null) {
            // Convert percentage to decimal if necessary
            return $efficiency > 1 ? $efficiency / 100 : $efficiency;
        }

        return self::DEFAULT_EFFICIENCIES[$stoveType] ?? 0.15; // Default to 15% if unknown
    }

    /**
     * Get emission factor for a specific fuel type
     *
     * @param string $fuelType - Type of fuel
     * @return float - Emission factor in tCO₂e/kg or tCO₂e/l
     */
    public function getEmissionFactor($fuelType)
    {
        if (!isset(self::EMISSION_FACTORS[$fuelType])) {
            Log::warning("Unknown fuel type: {$fuelType}. Using default emission factor.");
            return self::EMISSION_FACTORS['Other'];
        }

        return self::EMISSION_FACTORS[$fuelType];
    }

    /**
     * Get period multiplier for calculations
     *
     * @param string $period - Period type ('monthly' or 'annual')
     * @return int - Number of days for the period
     */
    private function getPeriodMultiplier($period)
    {
        switch (strtolower($period)) {
            case 'monthly':
                return $this->daysPerMonth;
            case 'annual':
                return $this->daysPerMonth * $this->monthsPerYear;
            default:
                Log::warning("Unknown period: {$period}. Using monthly as default.");
                return $this->daysPerMonth;
        }
    }

    /**
     * Get all available emission factors
     *
     * @return array - Array of all emission factors
     */
    public function getEmissionFactors()
    {
        return self::EMISSION_FACTORS;
    }

    /**
     * Get default efficiency for a stove type
     *
     * @param string $stoveType - Type of stove
     * @return float - Default efficiency (decimal)
     */
    public function getDefaultEfficiency($stoveType)
    {
        return self::DEFAULT_EFFICIENCIES[$stoveType] ?? 0.15;
    }

    /**
     * Validate calculation inputs
     *
     * @param float $dailyFuelUse - Daily fuel consumption
     * @param string $fuelType - Type of fuel
     * @param string $stoveType - Type of stove
     * @param float|null $efficiency - Custom efficiency
     * @return array - Array of error messages
     */
    public function validateInputs($dailyFuelUse, $fuelType, $stoveType, $efficiency = null)
    {
        $errors = [];

        if ($dailyFuelUse <= 0) {
            $errors[] = 'Daily fuel use must be greater than 0';
        }

        if (!isset(self::EMISSION_FACTORS[$fuelType])) {
            $errors[] = 'Invalid fuel type provided';
        }

        if (!isset(self::DEFAULT_EFFICIENCIES[$stoveType])) {
            $errors[] = 'Invalid stove type provided';
        }

        if ($efficiency !== null && ($efficiency <= 0 || $efficiency > 100)) {
            $errors[] = 'Efficiency must be between 1 and 100 percent';
        }

        return $errors;
    }

    /**
     * Validate comprehensive calculation data
     *
     * @param array $data - Input data to validate
     * @return bool - True if valid
     * @throws \InvalidArgumentException - If invalid
     */
    public function validateCalculationData($data)
    {
        $requiredFields = ['daily_fuel_use', 'fuel_type', 'stove_type'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Required field {$field} is missing");
            }
        }

        if (!is_numeric($data['daily_fuel_use']) || $data['daily_fuel_use'] <= 0) {
            throw new \InvalidArgumentException("Daily fuel use must be a positive number");
        }

        if (isset($data['efficiency']) &&
            (!is_numeric($data['efficiency']) || $data['efficiency'] <= 0 || $data['efficiency'] > 100)) {
            throw new \InvalidArgumentException("Efficiency must be between 1 and 100");
        }

        if (!isset(self::EMISSION_FACTORS[$data['fuel_type']])) {
            throw new \InvalidArgumentException("Unknown fuel type: {$data['fuel_type']}");
        }

        if (!isset(self::DEFAULT_EFFICIENCIES[$data['stove_type']])) {
            throw new \InvalidArgumentException("Unknown stove type: {$data['stove_type']}");
        }

        return true;
    }
}
