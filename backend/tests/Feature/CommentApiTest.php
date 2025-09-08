<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_comments_for_a_post_by_post_id(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(3)->create(['post_id' => $post->id]);

        $response = $this->getJson("/api/posts/{$post->id}/comments?per_page=10");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id','user_id','post_id','body','user' => ['id','name','email']],
                ],
                'current_page','last_page','per_page','total'
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_it_creates_comment_when_authenticated(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/posts/{$post->id}/comments", ['body' => 'Hi there']);

        $response->assertCreated()
            ->assertJsonPath('body', 'Hi there')
            ->assertJsonPath('post_id', $post->id)
            ->assertJsonPath('user_id', $user->id);
    }

    public function test_it_forbids_comment_creation_for_guests(): void
    {
        $post = Post::factory()->create();

        $this->postJson("/api/posts/{$post->id}/comments", ['body' => 'Hi'])
            ->assertStatus(401);
    }

    public function test_it_allows_owner_or_admin_to_delete_comment_and_forbids_others(): void
    {
        $post = Post::factory()->create();
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->create(['role' => \App\Enums\Role::ADMIN->value]);

        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $owner->id]);

        // other forbidden
        Sanctum::actingAs($other);
        $this->deleteJson("/api/comments/{$comment->id}")
            ->assertStatus(403);

        // owner ok
        Sanctum::actingAs($owner);
        $this->deleteJson("/api/comments/{$comment->id}")
            ->assertOk()
            ->assertJson(['message' => __('messages.comment.deleted')]);

        // recreate and admin ok
        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $owner->id]);
        Sanctum::actingAs($admin);
        $this->deleteJson("/api/comments/{$comment->id}")
            ->assertOk();
    }
}
