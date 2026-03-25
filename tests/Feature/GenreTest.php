<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\GenreSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting list of genres
     */
    public function test_show_genres(): void
    {
        $this->seed(GenreSeeder::class);
        $response = $this->get('/api/genres');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    /**
     * Test updating a genre as a moderator
     */
    public function test_update_genre_as_moderator(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(GenreSeeder::class);

        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);
        $genre = Genre::first();

        $response = $this->actingAs($moderator)->patch("/api/genres/{$genre->id}", ['name' => 'newName']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'name']]);
    }

    /**
     * Test updating a genre as a guest
     */
    public function test_update_unauthorized(): void
    {
        $this->seed(GenreSeeder::class);
        $genre = Genre::first();

        $response = $this->patch("/api/genres/{$genre->id}", ['name' => 'newName']);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
    }

    /**
     * Test updating a genre as a user
     */
    public function test_update_not_moderator(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(GenreSeeder::class);
        $genre = Genre::first();

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
                            ->withHeaders(['Accept' => 'application/json'])
                            ->patch("/api/genres/{$genre->id}", ['name' => 'newName']);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    /**
     * Test updating a genre with wrong data
     */
    public function test_update_with_invalid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(GenreSeeder::class);

        $role = Role::where('name', 'moderator')->first();
        $moderator = User::factory()->create(['role_id' => $role->id]);
        $genre = Genre::first();

        $response = $this->actingAs($moderator)
            ->withHeaders(['Accept' => 'application/json'])
            ->patch("/api/genres/{$genre->id}", ['name' => '']);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
    }
}
