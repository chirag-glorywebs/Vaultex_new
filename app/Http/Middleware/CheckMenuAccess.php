<?php

namespace App\Http\Middleware;

use Closure;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {     
        if (Auth::user() && Auth::user()->user_role==Helper::getRollId('SALES')){           
            $isAccess = SELF::find_key_value('seller_access');            
            if($isAccess){
                return $next($request);
            } else {
                return redirect('/admin/orders');
            }
        } 
        return $next($request);
    }

    public function find_key_value($key)
    {    
        $items = config('menu_aside.items');
        foreach ($items as $rkey => $rvalue) {
            if(isset($rvalue['seller_access']) && $rvalue['seller_access']) {              
                if ($rvalue['seller_access'] && (isset($rvalue['page_access']) && $rvalue['page_access'])) {
                    $routeURI = Route::current()->uri();
                    foreach ($rvalue['page_access'] as $pakey => $pavalue) {
                        if(request()->is($pavalue)){
                            return true;
                        }
                    }                  
                }
            }
        }    
        return false;
    }
  
    
}
