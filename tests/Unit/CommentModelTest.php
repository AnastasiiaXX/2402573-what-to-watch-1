<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_comment_returns_author_name(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->name, $comment->author_name);
    }

    public function test_anonymous_comment_returns_guest(): void
    {
        $comment = Comment::factory()->create(['user_id' => null]);

        $this->assertEquals('guest', $comment->author_name);
    }
}
