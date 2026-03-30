<?php

namespace App\Services\LoadCommentsService;

use App\Models\Comment;
use App\Models\Film;

class LoadCommentsService
{
    public function __construct(private LoadCommentsInterface $repository)
    {}

    public function syncComments(Film $film): void
    {
        $data = $this->repository->getComments($film->imdb_id);
        if (!$data) {
            return;
        }
        Comment::insert(array_map(fn($comment) => [
            'film_id' => $film->id,
            'comment' => $comment['text'],
            'rating' => $comment['rating'],
            'user_id' => null,
        ], $data));
    }
}
