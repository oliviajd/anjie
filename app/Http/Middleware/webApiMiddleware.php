<?php

namespace App\Http\Middleware;
use Closure;
use App\Http\Models\common\Privileges;

class webApiMiddleware
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
        $privileges = new Privileges();
        $rs = $privileges->run();    //检查是否有权限
        if (isset($rs['errorcode']) && $rs['errorcode'] == '-2') {
            return redirect('login'); 
        }
        if (isset($rs['errorcode']) && $rs['errorcode'] !== '0') {
            echo die(json_encode($rs));exit;
        }
        return $next($request);
    }
}
