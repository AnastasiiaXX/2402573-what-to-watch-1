<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CommentSeeder;
use Database\Seeders\FilmSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepares roles for users
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /**
     * Test getting the film's reviews
     */
    public function testGetReviewsForFilm(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->get("/api/comments/{$comment->film_id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['comment', 'rating', 'created_at', 'author_name']]]);
    }

    /**
     * Test adding a review
     */
    public function testAddReviewToFilm(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/api/comments/{$film->id}", ['comment' => str_repeat('a', 50), 'rating' => 8]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'film_id' => $film->id,
            'comment' => str_repeat('a', 50),
            'rating' => 8,
        ]);
    }

    /**
     * Test adding a review
     * without logging in
     */
    public function testAddReviewToFilmNotAuthorized(): void
    {
         $response = $this->withHeaders(['Accept' => 'application/json'])
                            ->post("/api/comments/12", ['comment' => str_repeat('a', 10), 'rating' => 8]);

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test editing a review
     */
    public function testUpdateReview(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $comment = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id]);

        $response = $this->actingAs($user)
             ->patch("/api/comments/{$comment->id}", ['comment' => str_repeat('a', 50)]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => str_repeat('a', 50),
        ]);
    }

    /**
     * Test editing a review
     * without logging in
     */
    public function testUpdateReviewNotAuthorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->patch("/api/comments/12", ['comment' => str_repeat('a', 50)]);

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test editing reviews as a mod
     */
    public function testUpdateReviewAsModerator(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $comment = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id]);

        $response = $this->actingAs($moderator)
            ->patch("/api/comments/{$comment->id}", ['comment' => str_repeat('a', 50)]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['comment', 'rating', 'created_at', 'author_name']]);
    }

    /**
     * Test delete a review
     */
    public function testDeleteReview(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $comment = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id]);

        $response = $this->actingAs($user)
            ->delete("/api/comments/{$comment->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }

    /**
     * Test deleting a review
     * without logging in
     */
    public function testDeleteReviewNotAuthorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->delete("/api/comments/12");

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test deleting reviews as a mod
     */
    public function testDeleteReviewAsModerator(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $parent = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id]);
        $child = Comment::factory()->create(['user_id' => $user->id,
                                            'film_id' => $film->id,
                                            'parent_id' => $parent->id]);

        $response = $this->actingAs($moderator)
            ->delete("/api/comments/{$parent->id}");

        $this->assertNull(Comment::find($child->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }
}
