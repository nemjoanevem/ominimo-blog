<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_authorizes_owner_or_admin_for_update_and_delete(): void
    {
        $policy = app(PostPolicy::class);

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $post = Post::factory()->for($owner)->create();

        $this->assertTrue($policy->update($owner, $post));
        $this->assertTrue($policy->delete($owner, $post));

        $this->assertFalse($policy->update($other, $post));
        $this->assertFalse($policy->delete($other, $post));

        $this->assertTrue($policy->update($admin, $post));
        $this->assertTrue($policy->delete($admin, $post));
    }
}
