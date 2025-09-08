<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin with known credentials
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password', // hashed by model cast
        ]);

        // Regular users
        $users = User::factory()->count(5)->create();

        // Posts
        $posts = Post::factory()
            ->count(20)
            ->state(function () use ($users) {
                // Assign an existing user as author (avoids creating extra users)
                return ['user_id' => $users->random()->id];
            })
            ->create();

        // Comments for posts
        foreach ($posts as $post) {
            Comment::factory()
                ->count(fake()->numberBetween(0, 5))
                ->state(function () use ($users, $admin, $post) {
                    return [
                        'post_id' => $post->id,
                        'user_id' => collect([$admin->id, ...$users->pluck('id')])->random(),
                    ];
                })
                ->create();
        }
    }
}
