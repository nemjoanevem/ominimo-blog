<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentIndexRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(private CommentService $service) {}

    /** GET /api/posts/{postId}/comments */
    public function index(CommentIndexRequest $request, int $postId): JsonResponse
    {
        $post = Post::findOrFail($postId);
        $data = $this->service->paginateForPost($post, (int)$request->integer('per_page', 10));
        return response()->json($data);
    }

    /** POST /api/posts/{postId}/comments */
    public function store(CommentStoreRequest $request, int $postId): JsonResponse
    {
        $this->authorize('create', Comment::class);

        $post = Post::findOrFail($postId);
        $comment = $this->service->create($post, $request->user(), $request->validated()['body']);

        return response()->json($comment, 201);
    }

    /** DELETE /api/comments/{comment} */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->service->delete($comment);

        return response()->json(['message' => __('messages.comment.deleted')], 200);
    }
}
