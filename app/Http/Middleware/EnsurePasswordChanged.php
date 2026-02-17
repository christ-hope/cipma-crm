<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        $allowed = ['password.change-first', 'password.change-first.update', 'logout'];

        if (in_array($request->route()->getName(), $allowed)) {
            return $next($request);
        }

        return redirect()->route('password.change-first');
    }
}
