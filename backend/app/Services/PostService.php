<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PostService
{
    /** Paginate posts and attach a derived description from body. */
    public function paginateWithDescription(int $perPage = 10): LengthAwarePaginator
    {
        $paginator = Post::with('user:id,name,email')
            ->latest('id')
            ->paginate($perPage);

        $paginator->getCollection()->transform(function (Post $post) {
            // Derive a short description on the fly (used by frontend list + hidden HTML cache)
            $post->description = Str::limit(strip_tags($post->body), 160);

            return $post;
        });

        return $paginator;
    }

    /** Return a single post with author loaded. */
    public function show(Post $post): Post
    {
        return $post->load('user:id,name,email');
    }

    /** Create a post for the given author. */
    public function create(array $data, User $author): Post
    {
        $slug = $data['slug'] ?? $this->makeUniqueSlug($data['title']);

        return Post::create([
            'user_id' => $author->id,
            'title' => $data['title'],
            'slug' => $slug,
            'body' => $data['body'],
        ]);
    }

    /** Update a post and (optionally) regenerate slug from title if not supplied. */
    public function update(Post $post, array $data): Post
    {
        if (isset($data['title']) && ! isset($data['slug'])) {
            $data['slug'] = $this->makeUniqueSlug($data['title'], $post->id);
        }

        $post->fill(array_filter($data, fn ($v) => ! is_null($v)));
        $post->save();

        return $post->fresh('user:id,name,email');
    }

    /** Delete a post. */
    public function delete(Post $post): void
    {
        $post->delete();
    }

    /** Simple unique slug generator with numeric suffix. */
    protected function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 0;

        while (
            Post::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $i++;
            $slug = $base.'-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            if (strlen($slug) > 255) {
                $slug = substr($slug, 0, 255);
            }
        }

        return $slug;
    }
}
