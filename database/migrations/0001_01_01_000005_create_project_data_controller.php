<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('new_stove_type');
            $table->string('new_fuel_type');
            $table->float('daily_fuel_use');
            $table->float('new_stove_efficiency');
            $table->date('start_date');
            $table->float('emissions_after');
            $table->float('credits_earned');
            $table->string('verification_status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('new_stove_type');
            $table->index('new_fuel_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_data');
    }
};
