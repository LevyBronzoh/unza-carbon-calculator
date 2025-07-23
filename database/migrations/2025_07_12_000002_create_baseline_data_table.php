<?php
/**
 * Create Baseline Data Table Migration - UNZA Carbon Calculator
 *
 * This migration creates the baseline_data table for storing users' current
 * cooking methods and emission calculations before any intervention
 *
 * Developed by Levy Bronzoh, Climate Yanga
 * Version: 1.2
 * Date: July 2025
 *
 * File: database/migrations/2025_07_12_000002_create_baseline_data_table.php
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
        if (!Schema::hasTable('baseline_data')) {
            Schema::create('baseline_data', function (Blueprint $table) {
                $table->id();

                // Foreign key reference with cascade delete
                $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

                // Cooking equipment details
                $table->string('stove_type');
                $table->string('fuel_type');

                // Usage metrics
                $table->decimal('daily_fuel_use', 8, 4);  // kg or liters per day
                $table->decimal('daily_hours', 5, 2);     // hours per day
                $table->decimal('efficiency', 5, 4);      // efficiency as decimal (0.10 for 10%)

                // Household information
                $table->unsignedInteger('household_size');

                // Emission calculations
                $table->decimal('monthly_emissions', 10, 4);  // tCO₂e/month
                $table->decimal('annual_emissions', 10, 4);   // tCO₂e/year
                $table->decimal('emission_factor', 8, 6);     // tCO₂e per kg/liter
                $table->decimal('emission_total', 10, 6);     // Legacy field (tCO₂e)

                $table->timestamps();

                // Indexes for performance
                $table->unique('user_id');  // Ensure one baseline per user
                $table->index(['user_id', 'created_at'], 'baseline_user_created_idx');
                $table->index('stove_type');
                $table->index('fuel_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baseline_data', function (Blueprint $table) {
            // First drop foreign key constraint
            $table->dropForeign(['user_id']);
        });

        // Then drop the table
        Schema::dropIfExists('baseline_data');
    }
};
