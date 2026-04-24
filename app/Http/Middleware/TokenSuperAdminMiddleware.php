<?php
namespace App\Http\Middleware;

use Closure;
use Session;

class TokenSuperAdminMiddleware
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
        if (! Session::get('userinfo')) {
            return redirect('/login');
        } else {
            $userinfo = Session::get('userinfo');
            $level    = session('userinfo.level');
            //SUPER ADMIN
            if (! in_array($level, ['SUPER'])) {
                return redirect('/');
            }
        }
        return $next($request);
    }
}
