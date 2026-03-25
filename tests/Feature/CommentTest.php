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
     * Test getting the film's reviews
     */
    public function test_get_reviews_for_film(): void
    {
        $this->seed(FilmSeeder::class);
        $this->seed(CommentSeeder::class);

        $film = Film::find(Comment::first()->film_id);

        $response = $this->get("/api/comments/{$film->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['comment', 'rating', 'created_at', 'author_name']]]);
    }

    /**
     * Test adding a review
     */
    public function test_add_review_to_film(): void
    {
        $this->seed(FilmSeeder::class);
        $this->seed(RoleSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/api/comments/{$film->id}", ['comment' => str_repeat('a', 50), 'rating' => 8]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['comment', 'rating', 'created_at', 'author_name']]);
    }

    /**
     * Test adding a review
     * without logging in
     */
    public function test_add_review_to_film_not_authorized(): void
    {
         $response = $this->withHeaders(['Accept' => 'application/json'])
                            ->post("/api/comments/12", ['comment' => str_repeat('a', 10), 'rating' => 8]);

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test editing a review
     */
    public function test_update_review_to_film(): void
    {
        $this->seed(FilmSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(CommentSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();
        $comment = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id]);

        $response = $this->actingAs($user)
             ->patch("/api/comments/{$comment->id}", ['comment' => str_repeat('a', 50)]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['comment', 'rating', 'created_at', 'author_name']]);
    }

    /**
     * Test editing a review
     * without logging in
     */
    public function test_update_review_to_film_not_authorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->patch("/api/comments/12", ['comment' => str_repeat('a', 50)]);

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test editing reviews as a mod
     */
    public function test_update_review_to_film_as_moderator(): void
    {
        $this->seed(FilmSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(CommentSeeder::class);

        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $film = Film::first();
        $comment = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id]);

        $response = $this->actingAs($moderator)
            ->patch("/api/comments/{$comment->id}", ['comment' => str_repeat('a', 50)]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['comment', 'rating', 'created_at', 'author_name']]);
    }

    /**
     * Test delete a review
     */
    public function test_delete_review_to_film(): void
    {
        $this->seed(FilmSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(CommentSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();
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
    public function test_delete_review_to_film_not_authorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->delete("/api/comments/12");

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test deleting reviews as a mod
     */
    public function test_delete_review_to_film_as_moderator(): void
    {
        $this->seed(FilmSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(CommentSeeder::class);

        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $film = Film::first();

        $parent = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id]);
        $child = Comment::factory()->create(['user_id' => $user->id, 'film_id' => $film->id, 'parent_id' => $parent->id]);

        $response = $this->actingAs($moderator)
            ->delete("/api/comments/{$parent->id}");

        $this->assertNull(Comment::find($child->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }
}
