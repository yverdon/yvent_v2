<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\User;

class VerifyKey
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
        $key_user = User::where('key',$request->route()->parameter('key'))->first()->username;
        $username = $request->route()->parameter('username');

        if ($key_user == $username) {
                return $next($request);
            }

        return redirect('/');
    }
}
