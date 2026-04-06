<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUserAvatar(User $user, array $data): User
    {
        if (isset($data['file'])) {
            $data['avatar'] = $data['file']->store('avatars', 'public');
        }
        unset($data['file']);

        $user->update($data);

        return $user;
    }
}
