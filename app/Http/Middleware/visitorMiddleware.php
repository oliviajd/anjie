<?php

namespace App\Http\Middleware;
use App\Http\Models\common\Common;
use Closure;
use Request;

class visitorMiddleware
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
        $id = Request::input('identity');
        $common = new Common();
        $rs = json_decode($common->isIdCard($id),true);
        if(!$rs['result']){
            die(json_encode(['result'=>null,'error_no'=>'400','error_msg'=>'身份证格式不正确！']));
        }
        $request->offsetSet('id',$id);
        return $next($request);
    }
}
