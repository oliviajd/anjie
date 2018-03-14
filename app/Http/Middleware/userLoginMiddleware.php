<?php

namespace App\Http\Middleware;
use App\Http\Models\table\Jcr_users;
use App\Http\Models\table\Jcr_verify;
use Closure;

class userLoginMiddleware
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
        $checkUser = new Jcr_users();
        $user = $checkUser->getuserinfobytoken($request->input('token'));
        if(empty($user)){
            die(json_encode(['result'=>null,'error_no'=>'402','error_msg'=>'请登陆后操作！']));
        }
//        $jcrVerfy = new Jcr_verify();
//        $user = $jcrVerfy->getginfobyuserid($user['user_id']);
//        if(empty($user)){
//            die(json_encode(['result'=>null,'error_no'=>'400','error_msg'=>'用户不存在！']));
//        }
        $request->offsetSet('_user',$user);
        return $next($request);
    }
}
