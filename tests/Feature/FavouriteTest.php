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
     * Test to get films added to favourites
     */
    public function test_show_favourites(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(FilmSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();
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
    public function test_show_favourites_not_authorized(): void
    {
        $this->seed(FilmSeeder::class);

        $response = $this->withHeaders(['Accept' => 'application/json'])
                            ->get('/api/favourite');
        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test adding film to favourites
     */
    public function test_add_film_to_favourites(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(FilmSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();
        $response = $this->actingAs($user)->post("/api/films/{$film->id}/favourite");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'name', 'poster_image', 'preview_image', 'background_image',
            'background_color', 'video_link', 'preview_video_link', 'description', 'rating', 'scores_count', 'released',
            'director', 'starring', 'run_time', 'genres', 'is_favourite']]);
    }

    /**
     * Test adding film to favourites
     * as an unauthorized user
     */
    public function test_add_film_to_favourites_not_authorized(): void
    {
        $this->seed(FilmSeeder::class);

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/films/12/favourite');
        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test adding a non-exsistent film to favourites
     */
    public function test_add_film_to_favourites_does_not_exist(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(FilmSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post("/api/films/9999/favourite");
        $response->assertStatus(404);
    }

    /**
     * Test adding a film to favourites for the second time
     */
    public function test_add_film_to_favourites_already_added(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(FilmSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();
        $user->favoriteFilms()->attach($film);

        $response = $this->actingAs($user)->post("/api/films/{$film->id}/favourite");

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
    }

    /**
     * Test deleting a film from favourites
     */
    public function test_delete_film_from_favourites(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(FilmSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();
        $user->favoriteFilms()->attach($film);
        $response = $this->actingAs($user)->delete("/api/films/{$film->id}/favourite");

        $response->assertStatus(200);
    }

    /**
     *  Test deleting a film from favourites
     * as an unauthorized user
     */
    public function test_delete_film_from_favourites_not_authorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->delete('/api/films/12/favourite');
        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test deleting a non-exsistent film from favourites
     */
    public function test_delete_film_from_favourites_does_not_exist(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(FilmSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->delete("/api/films/9999/favourite");
        $response->assertStatus(404);
    }

    /**
     * Test deleting a film from favourites again
     */
    public function test_delete_film_from_favourites_already_deleted(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(FilmSeeder::class);

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $film = Film::first();

        $response = $this->actingAs($user)->delete("/api/films/{$film->id}/favourite");

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
    }
}
