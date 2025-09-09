<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /** Seed posts owned by random users. */
    public function run(): void
    {
        $users = User::pluck('id')->all();

        Post::factory()
            ->count(20)
            ->state(fn () => ['user_id' => $users[array_rand($users)]])
            ->create();
    }
}
