<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotReviewer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $guard = 'reviewer')
    {
        if (auth($guard)->check() && !auth($guard)->user()->status) {
            auth($guard)->logout();
            $notify[] = ['error', 'You are banned'];
            return to_route('reviewer.login')->withNotify($notify);
        }

        if (!Auth::guard($guard)->check()) {
            return to_route('reviewer.login');
        }

        return $next($request);
    }
}
