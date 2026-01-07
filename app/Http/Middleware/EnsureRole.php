<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Usage: ->middleware('role:super_admin,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (empty($roles)) {
            return $next($request);
        }

        $userRole = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;

        if (! in_array($userRole, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}


