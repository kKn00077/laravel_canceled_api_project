<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonAccept
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
        // API의 경우 accept를 JSON으로 셋팅 (HTML 반환하지 않도록)
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
