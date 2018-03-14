<?php

namespace App\Http\Middleware;
use Closure;
use Request;
use Illuminate\Support\Facades\Log;

class shellMiddleware
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
        $allowIp = [
            '116.62.30.22',
        ];
        $ip = $request->getClientIp();
        if(!in_array($ip,$allowIp)){
            exit('没权限！');
            Log::info("ip=[{$ip}]无权限执行脚本");
        }
        return $next($request);
    }
}
