<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Event;

class VerifyEditorEvent
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
        $eventtype_id = ($request->route()->getParameter('event')->eventtype->id);
        // $eventtypes_list = Auth::user()->eventtypesWriteable()->pluck('id')->toArray();
        
        if (Auth::guard($guard)->check()) {
            // if (in_array($eventtype_id, $eventtypes_list))
            if (Auth::user()->isEventEditor($eventtype_id))
            {
                return $next($request);
            }
        }

        return redirect('/');
    }
}
