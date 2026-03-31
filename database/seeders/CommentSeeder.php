<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $film = Film::factory()->create();
        $user = User::factory()->create();
        Comment::factory()->create([
            'film_id' => $film->id,
            'user_id' => $user->id
        ]);
    }
}
