<?php

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
        // Only add column if it doesn't already exist
        if (!Schema::hasColumn('users', 'location')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('location')->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop column if it exists
        if (Schema::hasColumn('users', 'location')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('location');
            });
        }
    }
};
