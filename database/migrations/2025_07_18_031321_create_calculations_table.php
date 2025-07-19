<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['baseline', 'project', 'quick', 'weekly_update']);
            $table->json('data');
            $table->decimal('monthly_emissions', 12, 6)->nullable();
            $table->decimal('annual_emissions', 12, 6)->nullable();
            $table->decimal('weekly_emissions', 12, 6)->nullable();
            $table->decimal('emission_reduction', 12, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calculations');
    }
}
