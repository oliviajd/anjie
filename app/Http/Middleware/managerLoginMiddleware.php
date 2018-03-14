<?php

namespace App\Http\Middleware;
use App\Http\Models\business\Auth;
use App\Http\Models\table\Anjie_users;
use App\Http\Models\table\Api2_token;
use Closure;

class managerLoginMiddleware
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
        $auth = new Api2_token();
        $user = $auth->getInfoBytoken($request->input('token'));
        if(empty($user)){
            die(json_encode(['result'=>null,'error_no'=>'409','error_msg'=>'请登陆后操作！']));
        }
        if($user['status'] == 2){
            die(json_encode(['result'=>null,'error_no'=>'409','error_msg'=>'该用户无效！']));
        }
        $anjieUser = new Anjie_users();
        $user = $anjieUser->getInfoById($user['user_id']);
        if(empty($user)){
            die(json_encode(['result'=>null,'error_no'=>'409','error_msg'=>'用户不存在！']));
        }
        $user['user_id'] = $user['id'];
        $request->offsetSet('_user',$user);
        return $next($request);
    }
}
