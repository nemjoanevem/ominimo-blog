<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentService
{
    /** Paginate comments for a post, oldest first. */
    public function paginateForPost(Post $post, int $perPage = 10): LengthAwarePaginator
    {
        return Comment::with('user:id,name,email')
            ->where('post_id', $post->id)
            ->orderBy('id')
            ->paginate($perPage);
    }

    /** Create a comment under a post by a user. */
    public function create(Post $post, User $user, string $body): Comment
    {
        return Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'body'    => $body,
        ]);
    }

    /** Delete a comment. */
    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}
