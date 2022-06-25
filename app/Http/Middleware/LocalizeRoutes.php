<?php

namespace App\Http\Middleware;

use App\Util\BasicFunctions;

use Closure;
use Illuminate\Http\Request;

class LocalizeRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        BasicFunctions::local();
        
        return $next($request);
    }
}
