<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

/** @method static User create(array $attributes = []) */
class AuthController extends Controller
{
    /**
     * Logs in the user and creates token
     *
     * @param LoginRequest $request
     * @return SuccessResponse
     */
    public function login(LoginRequest $request): SuccessResponse
    {
        if (!Auth::attempt($request->validated())) {
            abort(401, trans('auth.failed'));
        }
        $token = Auth::user()->createToken('auth-token');

        return new SuccessResponse(['token' => $token->plainTextToken], 200);
    }

    /**
     * Logs the user out
     *
     * @return SuccessResponse
     */
    public function logout(): SuccessResponse
    {
        Auth::user()->tokens()->delete();

        return new SuccessResponse(null, 204);
    }

    /**
     * Registers the user
     *
     * @param RegisterRequest $request
     * @return SuccessResponse
     */
    public function store(RegisterRequest $request): SuccessResponse
    {
        $params = $request->validated();

        if ($request->hasFile('file')) {
            $params['avatar'] = $request->file('file')->store('avatars', 'public');
        }

        $user = new User($params);

        $user->role_id = Role::where('name', 'user')->first()->id;

        $user->save();
        $token = $user->createToken('auth-token');

        return new SuccessResponse([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }
}
