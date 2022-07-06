<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTypeAdmin
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
        if($request->session()->get('type_id') == 2){

            if ($request->session()->get('status_id') == 45) {
                $request->session()->flush();

                return $request->wantsJson()
                    ? new Response('', 204)
                    : redirect()->route('admin');
            }

            return $next($request);
        }else{
            return $next($request);
        }
        // return $next($request);
    }
}
