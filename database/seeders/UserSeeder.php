<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create commercial maritime user
        User::create([
            'name' => 'Commercial Maritime',
            'email' => 'commercial@example.com',
            'password' => Hash::make('password'),
            'role' => 'commercial_maritime'
        ]);
    }
}
