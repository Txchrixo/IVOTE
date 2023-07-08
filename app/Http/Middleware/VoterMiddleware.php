<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VoterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $query = $this->container->get('auth')->checkCode($request->getQueryParams()['code']);
        if(! $query) {
            return $response->withStatus(302)->withHeader('Location', '/vote/fail');
        }
        $request = $request->withAttribute('code', $query);

        return $handler->handle($request);
        return $next($request);
    }
}
