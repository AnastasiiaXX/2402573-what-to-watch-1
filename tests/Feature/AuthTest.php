<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
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
     * Test login
     */
    public function testLoginSuccessfully(): void
    {

        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);
        $response = $this->post('/api/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    /**
     * Test login with validation error
     */
    public function testLoginWithInvalidData(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/login', ['email' => $user->email, 'password' => '']);

        $response->assertJsonValidationErrors(['password']);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
        $response->assertStatus(422);
    }

    /**
     * Test login with wrong password
     */
    public function testLoginWithWrongData(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/login', ['email' => $user->email, 'password' => 'wrongpasssword']);

        $response->assertJson(['message' => 'Неверное имя пользователя или пароль.']);
        $response->assertStatus(401);
    }

    /**
     * Test registration
     */
    public function testRegisterSuccessfully(): void
    {
        $response = $this->post('/api/register', ['name' => 'Name', 'email' => 'new_user@mail.com',
            'password' => 'password']);
        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['user', 'token']]);
        $this->assertDatabaseHas('users', ['name' => 'Name', 'email' => 'new_user@mail.com']);
    }

    /**
     * Test registration with empty fields
     */
    public function testRegisterWithInvalidData(): void
    {
        $response = $this->post('/api/register', ['name' => '', 'email' => '',
            'password' => 'password']);
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
    }

    /**
     * Test registration with existing email
     */
    public function testRegisterEmailAlreadyExists(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->post('/api/register', ['name' => 'Name', 'email' => $user->email,
            'password' => 'password']);
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Переданные данные не корректны.']);
    }

    /**
     * Test logging out
     */
    public function testLogout(): void
    {
        $role = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post('/api/logout');
        $response->assertStatus(204);
    }
}
