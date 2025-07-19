<?php
/**
 * Create Project Data Table Migration - UNZA Carbon Calculator
 *
 * This migration creates the project_data table for storing users'
 * cleaner cooking interventions and calculated carbon credits
 *
 * Developed by Levy Bronzoh, Climate Yanga
 * Version: 1.1
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
    public function up()
    {
        // Only create table if it doesn't exist
        if (!Schema::hasTable('project_data')) {
            Schema::create('project_data', function (Blueprint $table) {
                $table->id();

                // Foreign key with explicit constrained reference
                $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

                $table->string('new_stove_type');
                $table->string('new_fuel_type');
                $table->decimal('fuel_use_project', 8, 3);
                $table->decimal('new_efficiency', 5, 3);
                $table->date('start_date');
                $table->decimal('emissions_after', 10, 6);
                $table->decimal('credits_earned', 10, 6);
                $table->timestamps();

                // Optimized index strategy
                $table->index(['user_id', 'start_date'], 'project_data_user_start_idx');
                $table->index('new_stove_type');
                $table->index('new_fuel_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // First drop foreign key constraints if they exist
        Schema::table('project_data', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Then drop the table
        Schema::dropIfExists('project_data');
    }
};
