<?php

namespace App\Http\Controllers\Api\Community;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends BaseApiController
{
    public function store(StoreCommentRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['post_id'] = $post->id;

        $comment = Comment::create($data);

        return $this->success($comment, 'Comment created.');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return $this->success(null, 'Comment deleted.');
    }
}
