<?php

namespace App\Http\Controllers\Api\Community;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Post::with('user')->withCount('comments');

        if ($q = $request->query('q')) {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        $posts = $query->orderByDesc('created_at')->paginate(15);

        return $this->success($posts);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $data['image_path'] = $path;
        }

        $post = Post::create($data);

        return $this->success($post, 'Post created.');
    }

    public function show(Post $post)
    {
        $post->load('comments.user', 'user');
        return $this->success($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            // remove old image
            try { if ($post->image_path) Storage::disk('public')->delete($post->image_path); } catch (\Throwable $_) {}
            $data['image_path'] = $request->file('image')->store('posts', 'public');
        }

        $post->update(array_filter($data, function ($v) { return $v !== null; }));

        return $this->success($post, 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        try { if ($post->image_path) Storage::disk('public')->delete($post->image_path); } catch (\Throwable $_) {}
        $post->delete();

        return $this->success(null, 'Post deleted.');
    }
}
