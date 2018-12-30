<?php

namespace App\Http\Middleware;

use Closure;

class isManagerOrCook
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
        if (auth()->guard('api')->user()->type !== 'manager' && auth()->guard('api')->user()->type !== 'cook') {
            return response('Unauthorized', '401');
        }


        return $next($request);
    }
}
