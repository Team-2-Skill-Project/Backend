<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveCandidate
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user('api');

        if (! $user->is_active) {
            return response()->json(['message' => 'Your account is inactive.'], 403);
        }

        if ($user->role !== 'candidate') {
            return response()->json(['message' => 'Only candidates can access this profile.'], 403);
        }

        return $next($request);
    }
}
