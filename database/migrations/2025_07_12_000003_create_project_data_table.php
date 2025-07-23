<?php
/**
 * Create Project Data Table Migration - UNZA Carbon Calculator
 *
 * This migration creates the project_data table for storing users'
 * cleaner cooking interventions and calculated carbon credits
 *
 * Developed by Levy Bronzoh, Climate Yanga
 * Version: 1.2
 * Date: July 2025
 *
 * File: database/migrations/2025_07_12_000003_create_project_data_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create table if it doesn't exist
        if (!Schema::hasTable('project_data')) {
            Schema::create('project_data', function (Blueprint $table) {
                $table->id();

                // Foreign key reference with cascade delete
                $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

                // Intervention details
                $table->string('new_stove_type');
                $table->string('new_fuel_type');

                // Usage metrics
                $table->decimal('fuel_use_project', 8, 4);  // kg or liters per day
                $table->decimal('new_efficiency', 5, 4);    // efficiency as decimal (0.25 for 25%)
                $table->date('start_date');

                // Emission calculations
                $table->decimal('monthly_emissions', 10, 4);  // tCO₂e/month
                $table->decimal('annual_emissions', 10, 4);   // tCO₂e/year
                $table->decimal('emissions_after', 10, 6);    // Legacy field (tCO₂e)

                // Carbon credit calculations
                $table->decimal('monthly_reduction', 10, 4);  // tCO₂e/month
                $table->decimal('annual_reduction', 10, 4);   // tCO₂e/year
                $table->decimal('percentage_reduction', 5, 2); // %
                $table->decimal('total_credits', 10, 4);      // Cumulative credits
                $table->decimal('credits_earned', 10, 6);     // Legacy field (tCO₂e)
                $table->decimal('emission_factor', 8, 6);     // tCO₂e per kg/liter

                $table->timestamps();

                // Indexes and constraints
                $table->unique('user_id');  // Ensure one project entry per user
                $table->index(['user_id', 'start_date'], 'project_user_start_idx');
                $table->index('new_stove_type');
                $table->index('new_fuel_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_data', function (Blueprint $table) {
            // First drop foreign key constraint
            $table->dropForeign(['user_id']);
        });

        // Then drop the table
        Schema::dropIfExists('project_data');
    }
};
