<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /** Seed comments across random posts and users. */
    public function run(): void
    {
        $posts = Post::pluck('id')->all();
        $users = User::pluck('id')->all();

        Comment::factory()
            ->count(50)
            ->state(fn() => [
                'post_id' => $posts[array_rand($posts)],
                'user_id' => $users[array_rand($users)],
            ])
            ->create();
    }
}
