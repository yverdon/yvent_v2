<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Event;

class VerifyReaderEvent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $eventtype_id = ($request->route()->parameter('event')->eventtype->id);

        if (Auth::guard($guard)->check()) {
            if (Auth::user()->isEventReader($eventtype_id))
            {
                return $next($request);
            }
        }

        return redirect('/');
    }
}
