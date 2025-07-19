<?php

/**
 * UNZA Carbon Calculator - Emission Factors Database Seeder
 *
 * This seeder populates the database with essential emission factors and default data
 * required for accurate carbon emissions calculations as per Verra VM0042 methodology.
 *
 * Extends Laravel's Seeder class through polymorphism, inheriting database seeding
 * capabilities and overriding the run() method for specific data population logic.
 *
 * @package UNZA_Carbon_Calculator
 * @version 1.0
 * @author Developed by Levy Bronzoh, Climate Yanga
 * @date July 12, 2025
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * EmissionFactorsSeeder Class
 *
 * Polymorphism Implementation:
 * - Extends Laravel's Seeder base class
 * - Inherits database seeding functionality from parent class
 * - Overrides run() method to implement specific seeding logic
 * - Uses Laravel's DB facade for direct database operations
 */
class EmissionFactorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method populates the database with:
     * 1. Emission factors for different fuel types
     * 2. Default stove efficiency values
     * 3. Sample user data for testing
     * 4. Reference data for calculations
     *
     * @return void
     */
    public function run(): void
    {
        // Disable foreign key checks to allow seeding without constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Temporarily disable FK constraints

        /*
        |--------------------------------------------------------------------------
        | Emission Factors Table Seeding
        |--------------------------------------------------------------------------
        |
        | Create emission_factors table and populate with values from technical spec.
        | These factors are used in Verra VM0042 methodology calculations.
        |
        */

        // Create emission_factors table if it doesn't exist
        DB::statement('
            CREATE TABLE IF NOT EXISTS emission_factors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                fuel_type VARCHAR(255) NOT NULL UNIQUE,
                emission_factor DECIMAL(10, 6) NOT NULL COMMENT "tCO2e per kg or liter",
                unit VARCHAR(10) NOT NULL DEFAULT "kg",
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        '); // Creates table with proper structure for emission factors

        // Clear existing emission factors to prevent duplicates
        DB::table('emission_factors')->truncate(); // Remove all existing records

        // Insert emission factors as specified in technical documentation
        DB::table('emission_factors')->insert([
            [
                'fuel_type' => 'Wood',
                'emission_factor' => 0.001747, // tCO2e/kg as per Verra VM0042
                'unit' => 'kg',
                'description' => 'Wood biomass emission factor for cooking applications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fuel_type' => 'Charcoal',
                'emission_factor' => 0.00674, // tCO2e/kg as per technical spec
                'unit' => 'kg',
                'description' => 'Charcoal emission factor for cooking applications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fuel_type' => 'LPG',
                'emission_factor' => 0.002983, // tCO2e/kg as per technical spec
                'unit' => 'kg',
                'description' => 'Liquefied Petroleum Gas emission factor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fuel_type' => 'Electricity',
                'emission_factor' => 0.00085, // tCO2e/kWh Zambia grid average
                'unit' => 'kWh',
                'description' => 'Electricity emission factor for Zambia grid average',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fuel_type' => 'Kerosene',
                'emission_factor' => 0.002537, // tCO2e/liter calculated value
                'unit' => 'liter',
                'description' => 'Kerosene emission factor for cooking stoves',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fuel_type' => 'Ethanol',
                'emission_factor' => 0.001513, // tCO2e/liter for ethanol fuel
                'unit' => 'liter',
                'description' => 'Ethanol emission factor for clean cooking',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]); // Inserts all emission factors with proper data types

        /*
        |--------------------------------------------------------------------------
        | Stove Efficiency Table Seeding
        |--------------------------------------------------------------------------
        |
        | Create and populate stove efficiency reference data for different
        | stove types used in emissions calculations.
        |
        */

        // Create stove_efficiency table for reference data
        DB::statement('
            CREATE TABLE IF NOT EXISTS stove_efficiency (
                id INT AUTO_INCREMENT PRIMARY KEY,
                stove_type VARCHAR(255) NOT NULL UNIQUE,
                efficiency_percentage DECIMAL(5, 2) NOT NULL COMMENT "Efficiency as percentage",
                efficiency_decimal DECIMAL(5, 4) NOT NULL COMMENT "Efficiency as decimal for calculations",
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        '); // Creates table for stove efficiency reference

        // Clear existing stove efficiency data
        DB::table('stove_efficiency')->truncate(); // Remove existing records

        // Insert default stove efficiency values based on common stove types
        DB::table('stove_efficiency')->insert([
            [
                'stove_type' => '3-Stone Fire',
                'efficiency_percentage' => 10.00, // 10% efficiency for traditional 3-stone fire
                'efficiency_decimal' => 0.1000, // Decimal format for calculations
                'description' => 'Traditional 3-stone fire cooking method',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stove_type' => 'Charcoal Brazier',
                'efficiency_percentage' => 15.00, // 15% efficiency for charcoal brazier
                'efficiency_decimal' => 0.1500, // Decimal format for calculations
                'description' => 'Traditional charcoal brazier cooking method',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stove_type' => 'Improved Biomass Stove',
                'efficiency_percentage' => 35.00, // 35% efficiency for improved biomass
                'efficiency_decimal' => 0.3500, // Decimal format for calculations
                'description' => 'Improved biomass stove with better combustion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stove_type' => 'LPG Stove',
                'efficiency_percentage' => 55.00, // 55% efficiency for LPG stove
                'efficiency_decimal' => 0.5500, // Decimal format for calculations
                'description' => 'Liquefied Petroleum Gas cooking stove',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stove_type' => 'Electric Stove',
                'efficiency_percentage' => 70.00, // 70% efficiency for electric stove
                'efficiency_decimal' => 0.7000, // Decimal format for calculations
                'description' => 'Electric cooking stove',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stove_type' => 'Kerosene Stove',
                'efficiency_percentage' => 45.00, // 45% efficiency for kerosene stove
                'efficiency_decimal' => 0.4500, // Decimal format for calculations
                'description' => 'Kerosene-powered cooking stove',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]); // Inserts efficiency data for all stove types

        /*
        |--------------------------------------------------------------------------
        | Sample User Data Seeding
        |--------------------------------------------------------------------------
        |
        | Create sample users for testing the application functionality.
        | These users can be used for development and testing purposes.
        |
        */

        // Clear existing users (only if this is development environment)
        if (app()->environment('local')) { // Only seed users in local development
            DB::table('users')->truncate(); // Remove existing test users

            // Insert sample users for testing
            DB::table('users')->insert([
                [
                    'name' => 'Test Student',
                    'email' => 'student@unza.zm',
                    'phone' => '+260971234567',
                    'password' => Hash::make('password123'), // Hashed password for security
                    'user_type' => 'student',
                    'location' => 'Lusaka, Zambia',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Test Staff Member',
                    'email' => 'staff@unza.zm',
                    'phone' => '+260977654321',
                    'password' => Hash::make('password123'), // Hashed password for security
                    'user_type' => 'staff',
                    'location' => 'Lusaka, Zambia',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Test Faculty',
                    'email' => 'faculty@unza.zm',
                    'phone' => '+260979876543',
                    'password' => Hash::make('password123'), // Hashed password for security
                    'user_type' => 'faculty',
                    'location' => 'Lusaka, Zambia',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]); // Inserts sample users for testing
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Re-enable FK constraints

        // Output success message to console
        $this->command->info('✅ Emission factors and default data seeded successfully!');
        $this->command->info('📊 Seeded ' . DB::table('emission_factors')->count() . ' emission factors');
        $this->command->info('🔥 Seeded ' . DB::table('stove_efficiency')->count() . ' stove efficiency records');

        if (app()->environment('local')) {
            $this->command->info('👥 Seeded ' . DB::table('users')->count() . ' test users');
        }
    }
}

/**
 * Seeder Usage Instructions:
 *
 * File Location: database/seeders/EmissionFactorsSeeder.php
 *
 * To run this seeder:
 * 1. Register in DatabaseSeeder.php: $this->call(EmissionFactorsSeeder::class);
 * 2. Run: php artisan db:seed --class=EmissionFactorsSeeder
 * 3. Or run all seeders: php artisan db:seed
 *
 * Data Structure:
 * - emission_factors: fuel types and their CO2e emission factors
 * - stove_efficiency: stove types and their efficiency percentages
 * - users: sample test users (development environment only)
 *
 * Security Notes:
 * - Test users only created in local development environment
 * - Passwords are properly hashed using Laravel's Hash facade
 * - Foreign key constraints properly managed during seeding
 *
 * Calculation Usage:
 * - Emission factors used in Verra VM0042 methodology
 * - Stove efficiency values based on field research and manufacturer specs
 * - All values can be referenced in EmissionsCalculator service
 */
