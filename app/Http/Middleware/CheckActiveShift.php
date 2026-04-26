<?php

namespace App\Http\Middleware;

use App\Models\Shift;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveShift
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasActiveShift = Shift::where('user_id', auth()->id())
            ->where('is_closed', false)
            ->exists();

        if (!$hasActiveShift && !$request->is('shift*')) {
            return redirect()->route('shift.open.form');
        }

        return $next($request);
    }
}