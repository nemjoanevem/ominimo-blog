<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_unique_slug_and_attaches_description_in_pagination(): void
    {
        $svc = app(PostService::class);
        $user = User::factory()->create();

        Post::factory()->create(['user_id' => $user->id, 'title' => 'Same Title', 'slug' => 'same-title']);
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Another Title']);

        $created = $svc->create(['title' => 'Same Title', 'body' => 'Body'], $user);
        $this->assertNotEquals('same-title', $created->slug);

        $page = $svc->paginateWithDescription(10);
        $first = $page->items()[0];
        $this->assertNotNull($first->description);
    }

    public function test_it_updates_slug_when_title_changes_and_slug_not_provided(): void
    {
        $svc = app(PostService::class);
        $user = User::factory()->create();

        $post = Post::factory()->create(['user_id' => $user->id, 'title' => 'Old Title', 'slug' => 'old-title']);
        $updated = $svc->update($post, ['title' => 'Brand New']);

        $this->assertStringStartsWith('brand-new', $updated->slug);
    }
}
