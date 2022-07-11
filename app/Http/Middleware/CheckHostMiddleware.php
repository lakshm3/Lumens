<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Trip;
use Illuminate\Support\Facades\URL;

class CheckHostMiddleware
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
        if(isset($_SERVER["HTTP_X_FORWARDED_HOST"]))
        {
            $url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_X_FORWARDED_HOST"];
            URL::forceRootUrl($url);
        }
        
        return $next($request);
    }
}
