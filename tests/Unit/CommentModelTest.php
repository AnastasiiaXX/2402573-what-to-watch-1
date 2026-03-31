<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting comments' author name
     */
    public function testAuthorNameReturnsUserName(): void
    {
        $role = Role::factory()->create();
        $user = User::factory()->create(['role_id' => $role->id]);

        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->name, $comment->author_name);
    }

    /**
     * Test that the name is 'guest'
     * for anonymous users
     */
    public function testAuthorNameReturnsGuestForAnonymous(): void
    {
        $comment = Comment::factory()->create(['user_id' => null]);

        $this->assertEquals('guest', $comment->author_name);
    }

    /**
     * Test that comments can be
     * self-referencing
     */
    public function testCommentCanHaveParentAndChildren(): void
    {
        $parent = Comment::factory()->create();
        $child = Comment::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertTrue($parent->children->contains($child));
    }

    /**
     * Test relation with film
     */
    public function testCommentBelongsToFilm(): void
    {
        $film = Film::factory()->create();
        $comment = Comment::factory()->create(['film_id' => $film->id]);

        $this->assertInstanceOf(Film::class, $comment->film);
        $this->assertEquals($film->id, $comment->film_id);
    }
}
