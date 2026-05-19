<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($permissions === [] || $user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, __('messages.unauthorized_action'));
    }
}
