<?php

namespace Tests\Feature;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserProfileTest extends TestCase
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
     * Test getting user's profile
     */
    public function testShowUserProfile(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get('/api/user');

        $response->assertJsonStructure(['data' => ['name', 'role', 'avatar', 'email']]);
        $response->assertStatus(200);
    }

    /**
     * Test getting user's profile without logging in
     */
    public function testShowUserProfileUnauthorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
                            ->get('/api/user');

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test updating a profile
     */
    public function testUpdateUserProfile(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
                        ->patch('/api/user', ['name'=> 'New Name', 'email' => 'newemail@bk.org']);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['name'=> 'New Name', 'email' => 'newemail@bk.org']);
    }

    /**
     * Test updating a profile without
     * authorization
     */
    public function testUpdateUserProfileUnauthorized(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->patch('/api/user');

        $response->assertJson(['message' => 'Запрос требует аутентификации.']);
        $response->assertStatus(401);
    }

    /**
     * Test updating a profile
     * with incorrect data
     */
    public function testUpdateUserProfileWithInvalidData(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patch('/api/user', ['name'=> 'New Name', 'email' => 'new_email.com']);

        $response->assertJsonValidationErrors(['email']);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
        $response->assertStatus(422);
    }

    /**
     * Test updating a profile
     * entering current e-mail
     */
    public function testUpdateUserProfileSameEmail(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patch('/api/user', ['name'=> 'New Name', 'email' => $user->email]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['name'=> 'New Name', 'email' => $user->email]);
    }

    /**
     * Test password hash when updating
     * a profile
     */
    public function testUpdateUserProfileHashedPassword(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patch('/api/user', ['name'=> 'New Name', 'email' => $user->email, 'password' => 'newpassword']);

        $hashed = Hash::check('newpassword', $user->fresh()->password);
        $response->assertStatus(200);
        $this->assertTrue($hashed);
    }
}
