<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Route::has('admin')) {
            //if (Auth::user()) {
            if (Auth::check()){
                return redirect('/admin/dashboard');
            } else {
                return redirect('login');
            }
        }
        return $next($request);
    }
}
