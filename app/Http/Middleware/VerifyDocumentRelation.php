<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Event;
use App\Document;

class VerifyDocumentRelation
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
        $document_eventid = ($request->route()->parameter('document')->event->id);
        $eventid = ($request->route()->parameter('event')->id);

        if ($document_eventid == $eventid) {
                return $next($request);
            }

        return redirect('/');
    }
}
