<?php

namespace App\Http\Middleware;

use App\World;
use Closure;
use Illuminate\Http\Request;

class CheckWorldMaintenance
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
        foreach($request->route()->parameters() as $val) {
            if($val instanceof World) {
                abort_if($val->maintananceMode, 503);
            }
        }
        return $next($request);
    }
}
