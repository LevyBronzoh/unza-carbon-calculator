<?php
/**
 * Create Baseline Data Table Migration - UNZA Carbon Calculator
 *
 * This migration creates the baseline_data table for storing users' current
 * cooking methods and emission calculations before any intervention
 *
 * Developed by Levy Bronzoh, Climate Yanga
 * Version: 1.1
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
    public function up()
    {
        // Only create table if it doesn't exist
        if (!Schema::hasTable('baseline_data')) {
            Schema::create('baseline_data', function (Blueprint $table) {
                $table->id();

                // Foreign key reference
                $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

                $table->string('stove_type');
                $table->string('fuel_type');
                $table->decimal('daily_fuel_use', 8, 3);
                $table->decimal('daily_hours', 5, 2);
                $table->decimal('efficiency', 5, 3);
                $table->unsignedInteger('household_size');
                $table->decimal('emission_total', 10, 6);
                $table->timestamps();

                // Optimized indexes
                $table->index(['user_id', 'created_at'], 'baseline_user_created_idx');
                $table->index('stove_type');
                $table->index('fuel_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // First drop foreign key constraints
        Schema::table('baseline_data', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Then drop the table
        Schema::dropIfExists('baseline_data');
    }
};
