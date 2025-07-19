<?php
/**
 * Create Weekly Updates Table Migration - UNZA Carbon Calculator
 *
 * This migration creates the weekly_updates table for tracking
 * users' ongoing cooking behavior and fuel consumption patterns
 *
 * Developed by Levy Bronzoh, Climate Yanga
 * Version: 1.0
 * Date: July 2025
 *
 * File: database/migrations/2025_07_12_000004_create_weekly_updates_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method creates the weekly_updates table for tracking
     * regular user updates on their cooking behavior
     *
     * @return void
     */
    public function up()
    {
        // Create weekly_updates table
        Schema::create('weekly_updates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('project_data_id')->constrained()->onDelete('cascade');
    $table->decimal('fuel_consumption', 8, 2);
    $table->decimal('cooking_hours', 8, 2);
    $table->decimal('stove_usage_percentage', 5, 2);
    $table->decimal('estimated_emissions', 10, 4)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     *
     * This method drops the weekly_updates table
     *
     * @return void
     */
    public function down()
    {
        // Drop the weekly_updates table
        Schema::dropIfExists('weekly_updates');
    }




};
