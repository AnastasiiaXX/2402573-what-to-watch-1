<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

/** @method static User create(array $attributes = []) */
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            abort(401, trans('auth.failed'));
        }
        $token = Auth::user()->createToken('auth-token');

        return new SuccessResponse(['token' => $token->plainTextToken], 200);
    }
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return new SuccessResponse(null, 204);
    }

    public function store(RegisterRequest $request)
    {
        $params = $request->safe()->except('file');

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('avatars', 'public');
            $params['avatar'] = $path;
        }

        $user = User::create($params);
        $token = $user->createToken('auth-token');

        return new SuccessResponse([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }
}
