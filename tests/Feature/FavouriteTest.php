<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\FilmSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavouriteTest extends TestCase
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
     * Test to get films added to favourites
     */
    public function testShowFavourites(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $user->favoriteFilms()->attach($film->id);
        $response = $this->actingAs($user)->get('/api/favourite');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['id', 'name', 'poster_image', 'preview_image', 'background_image',
            'background_color', 'video_link', 'preview_video_link', 'description', 'rating', 'scores_count', 'released',
            'director', 'starring', 'run_time', 'genres', 'is_favourite']]]);
    }

    /**
     * Test to get films added to favourites
     * as un unauthorized user
     */
    public function testShowFavouritesNotAuthorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
                            ->get('/api/favourite');
        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test adding film to favourites
     */
    public function testAddFilmToFavourites(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post("/api/films/{$film->id}/favourite");

        $response->assertStatus(200);
        $this->assertDatabaseHas('favourites', [
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test adding film to favourites
     * as an unauthorized user
     */
    public function testAddFilmToFavouritesNotAuthorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/films/12/favourite');
        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test adding a non-exsistent film to favourites
     */
    public function testAddFilmToFavouritesDoesNotExist(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post("/api/films/9999/favourite");
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Запрашиваемая страница не существует.']);
    }

    /**
     * Test adding a film to favourites for the second time
     */
    public function testAddFilmToFavouritesAlreadyAdded(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $user->favoriteFilms()->attach($film);

        $response = $this->actingAs($user)->post("/api/films/{$film->id}/favourite");

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
    }

    /**
     * Test deleting a film from favourites
     */
    public function testDeleteFilmFromFavourites(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $user->favoriteFilms()->attach($film);
        $response = $this->actingAs($user)->delete("/api/films/{$film->id}/favourite");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('favourites', [
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     *  Test deleting a film from favourites
     * as an unauthorized user
     */
    public function testDeleteFilmFromFavouritesNotAuthorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->delete('/api/films/12/favourite');
        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test deleting a non-exsistent film from favourites
     */
    public function testDeleteFilmFromFavouritesDoesNotExist(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->delete("/api/films/9999/favourite");
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Запрашиваемая страница не существует.']);
    }

    /**
     * Test deleting a film from favourites again
     */
    public function testDeleteFilmFromFavouritesAlreadyDeleted(): void
    {
        $film = Film::factory()->create();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->delete("/api/films/{$film->id}/favourite");

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
    }
}
