<?php

namespace App\Services\LoadCommentsService;

use App\Models\Comment;
use App\Models\Film;

class LoadCommentsService
{
    /**
     * @param LoadCommentsInterface $repository
     */
    public function __construct(private LoadCommentsInterface $repository)
    {
    }

    /**
     * Inserts new comments into the database
     *
     * @param Film $film
     * @return void
     */
    public function syncComments(Film $film): void
    {
        $data = $this->repository->getComments($film->imdb_id);
        if (!$data) {
            return;
        }
        /** @psalm-suppress UndefinedMagicMethod */
        Comment::insert(array_map(fn($comment) => [
            'film_id' => $film->id,
            'comment' => $comment['text'],
            'rating' => $comment['rating'],
            'user_id' => null,
        ], $data));
    }
}
