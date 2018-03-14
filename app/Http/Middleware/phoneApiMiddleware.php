<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Auth;
use App\Http\Models\common\Apiprivileges;

class phoneApiMiddleware
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
        $apiprivileges = new Apiprivileges();
        $apiprivileges->run();    //验证该用户是否有权限
        $auth = new Auth();
        $auth->run();                   //接口文档的限制
        return $next($request);
    }
}
