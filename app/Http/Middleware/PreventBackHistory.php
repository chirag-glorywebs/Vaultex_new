<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param IlluminateHttpRequest $request
     * @param Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'no-cache, must-revalidate, no-store, max-age=0, private');
        $response->headers->set('Expires', Carbon::now()->format('D, d M Y H:i:s T'));

        return $response;

    }

}
