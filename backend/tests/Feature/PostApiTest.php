<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_posts_with_description_and_body(): void
    {
        // Arrange: users and posts
        $users = User::factory()->count(2)->create();
        Post::factory()->count(3)->recycle($users)->create();

        // Act
        $response = $this->getJson('/api/posts?per_page=10');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id','title','slug','body','description','user' => ['id','name','email']],
                ],
                'current_page','last_page','per_page','total'
            ]);
        $first = $response->json('data.0');
        $this->assertNotEmpty($first['description']);
        $this->assertNotEmpty($first['body']);
    }

    public function test_it_shows_a_post_by_id(): void
    {
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertOk()
            ->assertJsonPath('id', $post->id)
            ->assertJsonPath('title', $post->title);
    }

    public function test_it_creates_a_post_as_authenticated_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = ['title' => 'My Post', 'body' => 'Hello body'];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertCreated()
            ->assertJsonPath('title', 'My Post')
            ->assertJsonPath('user_id', $user->id);
    }

    public function test_it_updates_a_post_as_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create(['title' => 'Old']);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/posts/{$post->id}", ['title' => 'New title']);

        $response->assertOk()
            ->assertJsonPath('title', 'New title');
    }

    public function test_it_forbids_update_by_non_owner_non_admin(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->create();

        Sanctum::actingAs($other);

        $this->putJson("/api/posts/{$post->id}", ['title' => 'X'])
            ->assertStatus(403);
    }

    public function test_it_allows_admin_to_update_any_post(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => Role::ADMIN->value]);
        $post = Post::factory()->for($owner)->create();

        Sanctum::actingAs($admin);

        $this->putJson("/api/posts/{$post->id}", ['title' => 'By Admin'])
            ->assertOk()
            ->assertJsonPath('title', 'By Admin');
    }

    public function test_it_deletes_post_as_owner_and_forbids_others_except_admin(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $admin = User::factory()->create(['role' => Role::ADMIN->value]);
        $post = Post::factory()->for($owner)->create();

        // Stranger forbidden
        Sanctum::actingAs($stranger);
        $this->deleteJson("/api/posts/{$post->id}")->assertStatus(403);

        // Owner ok
        Sanctum::actingAs($owner);
        $this->deleteJson("/api/posts/{$post->id}")
            ->assertOk()
            ->assertJson(['message' => __('messages.post.deleted')]);

        // Recreate and admin ok
        $post = Post::factory()->for($owner)->create();
        Sanctum::actingAs($admin);
        $this->deleteJson("/api/posts/{$post->id}")
            ->assertOk();
    }
}
