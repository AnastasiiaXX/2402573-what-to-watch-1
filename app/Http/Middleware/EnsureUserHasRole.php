<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!method_exists(User::class, $role)) {
            throw new \LogicException("Метод $role отсутствует у модели пользователя");
        }

        if (!Auth::check() || !Auth::user()->$role()) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
