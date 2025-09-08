<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostIndexRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private PostService $service) {}

    /** GET /api/posts */
    public function index(PostIndexRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->paginateWithDescription((int)$request->integer('per_page', 10))
        );
    }

    /** GET /api/posts/{post} (id-based binding) */
    public function show(Post $post): JsonResponse
    {
        return response()->json($this->service->show($post));
    }

    /** POST /api/posts */
    public function store(PostStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $post = $this->service->create($request->validated(), $request->user());

        return response()->json($post, 201);
    }

    /** PUT /api/posts/{post} */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        return response()->json($this->service->update($post, $request->validated()));
    }

    /** DELETE /api/posts/{post} */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $this->service->delete($post);

        return response()->json(['message' => __('messages.post.deleted')], 200);
    }
}
