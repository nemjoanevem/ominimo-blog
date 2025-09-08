<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Role;

class EnsureRole
{
    /**
     * Ensure the authenticated user has the given role.
     * 
     * Usage in routes: ->middleware('role:admin')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Accept either enum value or raw string
        $expected = strtolower($role);
        if (strtolower($user->role->value ?? (string) $user->role) !== $expected) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
