<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
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
        if(auth()->user()->userDetail->type === 'admin'){
            return $next($request);
        }else{
            return response()->json(['Error' => 'Forbidden, not admin'], 403);
        }
    }
}
