<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
     * @return SuccessResponse
     */
    public function update(UpdateUserProfileRequest $request): SuccessResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }
        unset($validated['file']);
        $user->update($validated);
        return new SuccessResponse($user, 200);
    }
}
