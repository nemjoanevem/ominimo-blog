<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        // Generate a unique-ish title first
        $title = $this->faker->unique()->sentence(6, true);

        // Derive slug from title and add a short unique suffix to avoid DB unique collisions
        $baseSlug = Str::slug($title);
        $slug = $baseSlug.'-'.$this->faker->unique()->numerify('####');

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => $slug,
            'body' => $this->faker->paragraphs(3, true),
        ];
    }
}
