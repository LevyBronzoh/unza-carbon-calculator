<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Test Student',
            'email' => 'student@unza.zm',
            'password' => bcrypt('password'),
            'phone' => '+260971234567',
            'user_type' => 'student',
            'location' => 'Lusaka',
            'email_verified_at' => now(),
        ]);

        // I will add other users similarly if any
    }
}
