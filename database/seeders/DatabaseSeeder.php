<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call your individual seeders here
        $this->call([
            EmissionFactorsSeeder::class,
            // Add other seeders as needed
        ]);
    }
}
