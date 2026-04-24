<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Session;

class TokenAllMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Session::get('userinfo')) {
            return redirect('/login');
        }
        return $next($request);
    }
}