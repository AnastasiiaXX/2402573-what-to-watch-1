<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Responses\SuccessResponse;
use App\Services\UserService;

class UsersController extends Controller
{
    /**
     * Gets current user profile with their role
     *
     * @return SuccessResponse
     */
    public function show(): SuccessResponse
    {
        $user = auth()->user();
        return new SuccessResponse($user->load('role'), 200);
    }

    /**
     * Edits current user's profile
     *
     * @param UpdateUserProfileRequest $request
     * @param UserService $userService
     * @return SuccessResponse
     */
    public function update(UpdateUserProfileRequest $request, UserService $userService): SuccessResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $userUpdated = $userService->updateUserAvatar($user, $validated);

        return new SuccessResponse($userUpdated, 200);
    }
}
