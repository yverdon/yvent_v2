<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Event;
use App\Log;

class VerifyLogRelation
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
        $log_eventid = ($request->route()->getParameter('log')->event->id);
        $eventid = ($request->route()->getParameter('event')->id);

        if ($log_eventid == $eventid) {
                return $next($request);
            }

        return redirect('/');
    }
}
