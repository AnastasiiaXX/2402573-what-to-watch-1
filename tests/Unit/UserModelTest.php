<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user is a moderator
     */
    public function testIsModeratorReturnsTrueForModeratorRole(): void
    {
        $role = Role::factory()->create(['name' => 'moderator']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($user->isModerator());
    }

    /**
     * Test regular user does not have a role
     * 'moderator'
     */
    public function testIsModeratorReturnsFalseForRegularUser(): void
    {
        $role = Role::factory()->create(['name' => 'user']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertFalse($user->isModerator());
    }
}
