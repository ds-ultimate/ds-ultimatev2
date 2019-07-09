<?php

namespace App\Http\Middleware;

use Closure;

class Translation
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
        if (\Gate::allows('translation_access')){
            return $next($request);
        }

        return redirect('/home');
    }
}
