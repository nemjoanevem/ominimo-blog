<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** Seed users including one admin. */
    public function run(): void
    {
        // Admin (dev purpose only)
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => Role::ADMIN->value,
        ]);

        // Regular users
        User::factory()->count(10)->create();
    }
}
