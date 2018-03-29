<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Event;
use App\Slot;

class VerifySlotRelation
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
        $slot_eventid = ($request->route()->parameter('slot')->event->id);
        $eventid = ($request->route()->parameter('event')->id);

        if ($slot_eventid == $eventid) {
                return $next($request);
            }

        return redirect('/');
    }
}
