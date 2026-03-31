<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmTest extends TestCase
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
     *  Test getting list of films
     */
    public function testShowFilms(): void
    {
        Film::factory()->create();
        Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get('/api/films');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['id', 'name', 'poster_image', 'preview_image', 'background_image',
            'background_color', 'video_link', 'preview_video_link', 'description', 'rating', 'scores_count', 'released',
            'director', 'starring', 'run_time', 'genres', 'is_favourite']], 'current_page', 'first_page_url',
            'next_page_url', 'prev_page_url', 'per_page', 'total']);
    }

    /**
     *  Test getting one film
     */
    public function testShowOne(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get("/api/films/{$film->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'name', 'poster_image', 'preview_image', 'background_image',
            'background_color', 'video_link', 'preview_video_link', 'description', 'rating', 'scores_count', 'released',
            'director', 'starring', 'run_time', 'genres', 'is_favourite']]);
    }

    /**
     *  Test getting non-existing film
     */
    public function testFilmDoesNotExist(): void
    {
        $response = $this->get("/api/films/9999");
        $response->assertStatus(404);
    }

    /**
     *  Test adding new film
     */
    public function testAddNewFilm(): void
    {
        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);
        $response = $this->actingAs($moderator)->post('/api/films', ['imdb_id' => 'tt45678']);

        $response->assertStatus(201);
        $this->assertDatabaseHas('films', ['imdb_id' => 'tt45678']);
    }

    /**
     *  Test adding new film as a regular user
     */
    public function testAddNewFilmNotModerator(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/api/films', ['imdb_id' => 'tt45678']);

        $response->assertJson(['message' => 'This action is unauthorized.']);
        $response->assertStatus(403);
    }

    /**
     *  Test adding new film with a wrong dataset
     */
    public function testAddNewFilmWithInvalidData(): void
    {
        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);
        $response = $this->actingAs($moderator)->post('/api/films', ['imdb_id' => 1]);

        $response->assertJsonValidationErrors(['imdb_id']);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
        $response->assertStatus(422);
    }

    /**
     *  Test updating a film
     */
    public function testUpdateFilm(): void
    {
        $film = Film::factory()->create();
        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($moderator)->patch(
            "/api/films/{$film->id}",
            ['imdb_id' => 'tt45678', 'status' => 'ready', 'name' => 'film']
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('films', ['imdb_id' => 'tt45678', 'status' => 'ready', 'name' => 'film']);
    }

    /**
     *  Test updating a film by a common user
     */
    public function testUpdateFilmNotModerator(): void
    {
        $film = Film::factory()->create();
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patch("/api/films/{$film->id}", ['imdb_id' => 'tt45678']);

        $response->assertJson(['message' => 'This action is unauthorized.']);
        $response->assertStatus(403);
    }

    /**
     *  Test updating a film with wrong data
     */
    public function testUpdateFilmWithInvalidData(): void
    {
        $film = Film::factory()->create();
        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($moderator)->patch(
            "/api/films/{$film->id}",
            ['imdb_id' => '12365', 'status' => 'not ready', 'name' => 415]
        );

        $response->assertJsonValidationErrors(['imdb_id', 'name', 'status']);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
        $response->assertStatus(422);
    }

    /**
     *  Test updating a non-existing film
     */
    public function testUpdateFilmDoesNotExist(): void
    {
        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($moderator)
            ->withHeaders(['Accept' => 'application/json'])
            ->patch('/api/films/9999');

        $response->assertStatus(404);
    }

    /**
     *  Test showing a promo film
     */
    public function testShowPromo(): void
    {
        Film::factory()->create(['is_promo' => true]);

        $response = $this->get('/api/promo');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'name', 'poster_image', 'preview_image', 'background_image',
            'background_color', 'video_link', 'preview_video_link', 'description', 'rating', 'scores_count', 'released',
            'director', 'starring', 'run_time', 'genres']]);
    }

    /**
     *  Test adding a promo film
     */
    public function testAddPromo(): void
    {
        $film = Film::factory()->create();
        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($moderator)->post("/api/promo/{$film->id}", ['is_promo' => true]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('films', ['id' => $film->id, 'is_promo' => true]);
    }

    /**
     *  Test adding a promo film with user rights
     */
    public function testAddPromoNotModerator(): void
    {
        $film = Film::factory()->create();
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/api/promo/{$film->id}", ['is_promo' => true]);

        $response->assertJson(['message' => 'This action is unauthorized.']);
        $response->assertStatus(403);
    }

    /**
     *  Test adding a non-exisitng promo film
     */
    public function testAddPromoDoesNotExist(): void
    {
        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($moderator)->post('/api/promo/9999');

        $response->assertStatus(404);
    }

    /**
     *  Test showing films with the same genre
     */
    public function testShowSimilar(): void
    {
        $genre = Genre::factory()->create();
        Film::factory()->hasAttached($genre)->create();
        Film::factory()->hasAttached($genre)->create();
        $film = Film::first();

        $response = $this->get("/api/films/{$film->id}/similar");
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['id', 'imdb_id', 'status']]]);
    }
}
