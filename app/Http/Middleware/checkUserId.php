<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class checkUserId
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
        if (!isset($request->user_id) || $request->user_id === null) {
            return new Response(array(
                'message' => 'You have to be logged',
                'status_code' => Response::HTTP_FORBIDDEN,
            ), Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
