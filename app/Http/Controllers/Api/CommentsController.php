<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;


class CommentsController extends Controller
{
    public function index() {
        return new SuccessResponse([], 200);
    }
    public function store($id) {
        return new SuccessResponse([], 200);
    }
    public function update($id) {
        $comment = Comment::findOrFail($id);

        if (Gate::allows('comment-update', $comment))
        {
            return new SuccessResponse([], 200);
        } else {
            return new ErrorResponse(403, trans('messages.not_allowed'));
        }
    }

    public function destroy($id) {
        $comment = Comment::find($id);

        if (!$comment) {
            return new ErrorResponse(404, "Comment not found");
        }

        if (Gate::allows('comment-delete', $comment))
        {
            return new SuccessResponse([], 200);
        } else {
            return new ErrorResponse(403, trans('messages.not_allowed'));
        }
    }
}
