<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Comment;
use App\Models\Film;
use Illuminate\Support\Facades\Gate;

class CommentsController extends Controller
{
    /**
     * Gets comments/reviews to the film
     * from new to old
     *
     * @param Film $film
     * @return SuccessResponse
     */
    public function index(Film $film): SuccessResponse
    {
        $comments = $film->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        return new SuccessResponse($comments, 200);
    }

    /**
     * Adds a comment/review to the film
     *
     * @param Film $film
     * @param StoreCommentRequest $request
     * @return SuccessResponse
     */
    public function store(Film $film, StoreCommentRequest $request): SuccessResponse
    {
        $user = auth()->user();
        $validated = $request->validated();
        /** @var array $validated */
        $comment = $film->comments()->create([...$validated, 'user_id' => $user->id]);
        return new SuccessResponse($comment, 200);
    }

    /**
     * Edits a comment/review to the film
     *
     * @param UpdateCommentRequest $request
     * @param Comment $comment
     * @return SuccessResponse|ErrorResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment): SuccessResponse|ErrorResponse
    {
        if (Gate::allows('comment-update', $comment)) {
            $validated = $request->validated();
            $comment->update($validated);
            return new SuccessResponse($comment, 200);
        } else {
            return new ErrorResponse(403, trans('messages.not_allowed'));
        }
    }

    /**
     * Deletes a comment/review to the film
     *  Moderator is able to delete all comments and their children
     *
     * @param Comment $comment
     * @return SuccessResponse|ErrorResponse
     */
    public function destroy(Comment $comment): SuccessResponse|ErrorResponse
    {
        if (Gate::allows('comment-delete', $comment)) {
            $comment->delete();
            return new SuccessResponse([], 200);
        } else {
            return new ErrorResponse(403, trans('messages.not_allowed'));
        }
    }
}
