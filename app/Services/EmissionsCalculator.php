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
 * Version: 1.0
 * Date: July 2025
 */
class EmissionsCalculator
{
    /**
     * @var array
     * Emission factors for different fuel types (tCO₂e/kg or tCO₂e/l)
     * Based on Verra VM0042 methodology and Zambian context
     */
    private $emissionFactors = [
        'Wood' => 0.001747,        // tCO₂e/kg - Wood biomass
        'Charcoal' => 0.00674,     // tCO₂e/kg - Charcoal
        'LPG' => 0.002983,         // tCO₂e/kg - Liquefied Petroleum Gas
        'Electricity' => 0.00085,  // tCO₂e/kWh - Zambia grid average
        'Kerosene' => 0.00264,     // tCO₂e/l - Kerosene
        'Ethanol' => 0.00189,      // tCO₂e/l - Ethanol fuel
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
     *
     * Formula: E_baseline = (F_baseline × EF_fuel) / η_baseline
     *
     * @param float $dailyFuelUse - Daily fuel consumption (kg or liters)
     * @param string $fuelType - Type of fuel being used
     * @param float $stoveEfficiency - Efficiency of the stove (decimal: 0.1 = 10%)
     * @param string $period - Calculation period ('monthly' or 'annual')
     * @return float - Baseline emissions in tCO₂e
     *
     * This method calculates the baseline emissions before any intervention
     */
    public function calculateBaselineEmissions($dailyFuelUse, $fuelType, $stoveEfficiency, $period = 'monthly')
    {
        // Get emission factor for the specified fuel type
        $emissionFactor = $this->getEmissionFactor($fuelType);

        // Calculate period multiplier (30 days for monthly, 365 for annual)
        $periodMultiplier = $this->getPeriodMultiplier($period);

        // Calculate total fuel consumption for the period
        $periodFuelConsumption = $dailyFuelUse * $periodMultiplier;

        // Apply Verra VM0042 formula for baseline emissions
        // E_baseline = (F_baseline × EF_fuel) / η_baseline
        $baselineEmissions = ($periodFuelConsumption * $emissionFactor) / $stoveEfficiency;

        return $baselineEmissions;
    }

    /**
     * Calculate project emissions after intervention
     *
     * Formula: E_project = (F_project × EF_fuel) / η_project
     *
     * @param float $dailyFuelUse - Daily fuel consumption after intervention (kg or liters)
     * @param string $fuelType - Type of fuel being used in project
     * @param float $stoveEfficiency - Efficiency of the new stove (decimal)
     * @param string $period - Calculation period ('monthly' or 'annual')
     * @return float - Project emissions in tCO₂e
     *
     * This method calculates emissions after implementing cleaner cooking methods
     */
    public function calculateProjectEmissions($dailyFuelUse, $fuelType, $stoveEfficiency, $period = 'monthly')
    {
        // Get emission factor for the specified fuel type
        $emissionFactor = $this->getEmissionFactor($fuelType);

        // Calculate period multiplier
        $periodMultiplier = $this->getPeriodMultiplier($period);

        // Calculate total fuel consumption for the period
        $periodFuelConsumption = $dailyFuelUse * $periodMultiplier;

        // Apply Verra VM0042 formula for project emissions
        // E_project = (F_project × EF_fuel) / η_project
        $projectEmissions = ($periodFuelConsumption * $emissionFactor) / $stoveEfficiency;

        return $projectEmissions;
    }

    /**
     * Calculate emission reduction (carbon credits earned)
     *
     * Formula: ER = E_baseline - E_project
     *
     * @param float $baselineEmissions - Baseline emissions (tCO₂e)
     * @param float $projectEmissions - Project emissions (tCO₂e)
     * @return float - Emission reduction in tCO₂e
     *
     * This method calculates the carbon credits earned by switching to cleaner cooking
     */
    public function calculateEmissionReduction($baselineEmissions, $projectEmissions)
    {
        // Calculate emission reduction using Verra VM0042 methodology
        // ER = E_baseline - E_project
        $emissionReduction = $baselineEmissions - $projectEmissions;

        // Ensure emission reduction is not negative
        // If project emissions are higher than baseline, return 0
        return max(0, $emissionReduction);
    }

    /**
     * Calculate comprehensive emissions data for a user
     *
     * @param array $baselineData - Baseline cooking data
     * @param array $projectData - Project intervention data
     * @param string $period - Calculation period
     * @return array - Comprehensive emissions calculation results
     *
     * This method provides a complete analysis of emissions before and after intervention
     */
    public function calculateComprehensiveEmissions($baselineData, $projectData, $period = 'monthly')
    {
        // Calculate baseline emissions
        $baselineEmissions = $this->calculateBaselineEmissions(
            $baselineData['daily_fuel_use'],
            $baselineData['fuel_type'],
            $baselineData['efficiency'],
            $period
        );

        // Calculate project emissions
        $projectEmissions = $this->calculateProjectEmissions(
            $projectData['fuel_use_project'],
            $projectData['new_fuel_type'],
            $projectData['new_efficiency'],
            $period
        );

        // Calculate emission reduction
        $emissionReduction = $this->calculateEmissionReduction($baselineEmissions, $projectEmissions);

        // Calculate percentage reduction
        $percentageReduction = $baselineEmissions > 0 ?
            ($emissionReduction / $baselineEmissions) * 100 : 0;

        // Return comprehensive results array
        return [
            'baseline_emissions' => round($baselineEmissions, 4),
            'project_emissions' => round($projectEmissions, 4),
            'emission_reduction' => round($emissionReduction, 4),
            'percentage_reduction' => round($percentageReduction, 2),
            'period' => $period,
            'calculation_date' => now()->toDateString(),
        ];
    }

    /**
     * Convert monthly emissions to annual emissions
     *
     * @param float $monthlyEmissions - Monthly emissions value
     * @return float - Annual emissions value
     *
     * This method converts monthly calculations to annual values
     */
    public function convertToAnnual($monthlyEmissions)
    {
        // Multiply monthly emissions by 12 months
        return $monthlyEmissions * $this->monthsPerYear;
    }

    /**
     * Convert annual emissions to monthly emissions
     *
     * @param float $annualEmissions - Annual emissions value
     * @return float - Monthly emissions value
     *
     * This method converts annual calculations to monthly values
     */
    public function convertToMonthly($annualEmissions)
    {
        // Divide annual emissions by 12 months
        return $annualEmissions / $this->monthsPerYear;
    }

    /**
     * Get emission factor for a specific fuel type
     *
     * @param string $fuelType - Type of fuel
     * @return float - Emission factor in tCO₂e/kg or tCO₂e/l
     *
     * This private method retrieves the emission factor for a given fuel type
     */
    private function getEmissionFactor($fuelType)
    {
        // Check if fuel type exists in emission factors array
        if (!isset($this->emissionFactors[$fuelType])) {
            // Log warning for unknown fuel type
            Log::warning("Unknown fuel type: {$fuelType}. Using default emission factor.");

            // Return default emission factor for wood
            return $this->emissionFactors['Wood'];
        }

        // Return specific emission factor
        return $this->emissionFactors[$fuelType];
    }

    /**
     * Get period multiplier for calculations
     *
     * @param string $period - Period type ('monthly' or 'annual')
     * @return int - Number of days for the period
     *
     * This private method returns the appropriate multiplier for time period calculations
     */
    private function getPeriodMultiplier($period)
    {
        // Switch statement to determine period multiplier
        switch (strtolower($period)) {
            case 'monthly':
                return $this->daysPerMonth;    // 30 days
            case 'annual':
                return $this->daysPerMonth * $this->monthsPerYear; // 365 days
            default:
                // Log warning for unknown period
                Log::warning("Unknown period: {$period}. Using monthly as default.");
                return $this->daysPerMonth;
        }
    }

    /**
     * Get all available emission factors
     *
     * @return array - Array of all emission factors
     *
     * This method returns all available emission factors for fuel types
     */
    public function getEmissionFactors()
    {
        return $this->emissionFactors;
    }

    /**
     * Add or update emission factor for a fuel type
     *
     * @param string $fuelType - Type of fuel
     * @param float $emissionFactor - Emission factor value
     * @return void
     *
     * This method allows dynamic addition or updating of emission factors
     */
    public function setEmissionFactor($fuelType, $emissionFactor)
    {
        // Validate emission factor is positive
        if ($emissionFactor <= 0) {
            throw new \InvalidArgumentException("Emission factor must be positive");
        }

        // Add or update emission factor
        $this->emissionFactors[$fuelType] = $emissionFactor;

        // Log the update
        Log::info("Emission factor updated for {$fuelType}: {$emissionFactor}");
    }

    /**
     * Validate input data for emissions calculations
     *
     * @param array $data - Input data to validate
     * @return bool - True if data is valid
     * @throws \InvalidArgumentException - If data is invalid
     *
     * This method validates input data before performing calculations
     */
    public function validateCalculationData($data)
    {
        // Check required fields
        $requiredFields = ['daily_fuel_use', 'fuel_type', 'efficiency'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Required field {$field} is missing");
            }
        }

        // Validate numeric fields
        if (!is_numeric($data['daily_fuel_use']) || $data['daily_fuel_use'] <= 0) {
            throw new \InvalidArgumentException("Daily fuel use must be a positive number");
        }

        if (!is_numeric($data['efficiency']) || $data['efficiency'] <= 0 || $data['efficiency'] > 1) {
            throw new \InvalidArgumentException("Efficiency must be between 0 and 1");
        }

        // Validate fuel type exists
        if (!isset($this->emissionFactors[$data['fuel_type']])) {
            throw new \InvalidArgumentException("Unknown fuel type: {$data['fuel_type']}");
        }

        return true;
    }
}
