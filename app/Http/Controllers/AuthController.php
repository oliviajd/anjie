<?php

namespace App\Http\Controllers;

use App\Http\Models\business\Auth;
use App\Http\Models\business\Role;
use App\Http\Models\business\Token;
use App\Http\Models\business\Message;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use Request;
use Illuminate\Contracts\View\View;
use App\Http\Models\business\Workflow;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    private $_auth = null;
    private $_role = null;
    private $_message = null;

    public function __construct()
    {
        parent::__construct();
        $this->_auth = new Auth();
        $this->_token = new Token();
        $this->_role = new Role();
        $this->_message = new Message();
        $this->_workflow = new Workflow();
    }
    /**
     * 登录
     * @param int $account   账号
     * @param int $password  姓名
     * @return user   用户信息
     * @return token  token信息.
     */
    public function index()
    {
        $account = Request::input('account', '');
        $password = Request::input('password', '');
        $issetAccount = $this->_auth->checkAccountIsset($account);  //先验证账号是否存在
        if (!$issetAccount) {
          return $this->_common->output(false, Constant::ERR_ACCOUNT_NOT_EXISTS_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_MSG);
        }
        $info = $this->_auth->checkPasswordByAccount($account, $password); //验证密码的正确性
        if (!$info) {
          return $this->_common->output(false, Constant::ERR_PASSWORD_NO_CORRECT_NO, Constant::ERR_PASSWORD_NO_CORRECT_MSG);
        }
        $setlog = $this->_auth->setLoginLog($info['account'], $info['id'], $info['name']); //写入登录日志表
        $setlastlogintime = $this->_auth->setlastlogintime($info['account']); //写入用户表中的上次登录时间
        $_SESSION['user_id'] = $info['id'];
        $_SESSION['account'] = $info['account'];
        $_SESSION['name'] = $info['name'];
        $_SESSION['passwd'] = $info['passwd'];
        $default = 'http://' . $_SERVER['HTTP_HOST'] . '/images/default.jpg';
        $info['head_portrait'] = ($info['head_portrait'] == '') ? $default : $info['head_portrait'];
        $_SESSION['head_portrait'] = $info['head_portrait'];
        $token = $this->_token->create(array('user_id'=>$info['id'], 'cache'=>$info));
        $r['app_role_list'] = $this->_role->getrolelists($info['id']);
        $r['role_type'] = $this->_role->getRoleType($info['id']); // 1是家访 2是销售  3都不是
        $r['app_role'] = $info['app_role'];    //app端选择出来的角色，1为家访组，2为销售组
        $r['haschild'] = $this->_auth->haschild($info['id']); // 1为有下属，2为无下属
        $r['highest_charge'] = $this->_auth->highest_charge($info['id']); // 1为一级主管，2不是一级主管
        $r['not_read'] = $this->_workflow->not_read($info['id']);   //是否有未读，如果有为true ，没有为false
        $r['user'] = $info;
        $r['token'] = $token;
        // $rs = $this->_common->curl_post(env('PUSH_URL').'unbind',['app_name'=>env('PUSH_APP_NAME'),'phone'=>$account]);
        // Log::info('登陆个推解绑mobile='.$account.'app_name='.env('PUSH_APP_NAME').'结果:'.$rs);
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 登录
     * @param int $account   账号
     * @param int $password  姓名
     * @return user   用户信息
     * @return token  token信息.
     */
    public function applogin()
    {
        $account = Request::input('account', '');
        $password = Request::input('password', '');
        $issetAccount = $this->_auth->checkAccountIsset($account);  //先验证账号是否存在
        if (!$issetAccount) {
          return $this->_common->output(false, Constant::ERR_ACCOUNT_NOT_EXISTS_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_MSG);
        }
        $info = $this->_auth->checkPasswordByAccount($account, $password); //验证密码的正确性
        if (!$info) {
          return $this->_common->output(false, Constant::ERR_PASSWORD_NO_CORRECT_NO, Constant::ERR_PASSWORD_NO_CORRECT_MSG);
        }
        $r['app_role_list'] = $this->_role->getrolelists($info['id']);
        $r['role_type'] = $this->_role->getRoleType($info['id']); // 1是家访 2是销售  3都不是
        $r['app_role'] = $info['app_role'];    //app端选择出来的角色，1为销售组，2为家访组
        if ($r['role_type'] == '3' && ($r['app_role'] =='' || $r['app_role'] ==null)) {
            return $this->_common->output(false, Constant::ERR_CANNOT_FIND_ROLE_NO, Constant::ERR_CANNOT_FIND_ROLE_MSG);
        }
        $setlog = $this->_auth->setLoginLog($info['account'], $info['id'], $info['name']); //写入登录日志表
        $setlastlogintime = $this->_auth->setlastlogintime($info['account']); //写入用户表中的上次登录时间
        $token = $this->_token->create(array('user_id'=>$info['id'], 'cache'=>$info));

        $r['haschild'] = $this->_auth->haschild($info['id']); // 1为有下属，2为无下属
        $r['highest_charge'] = $this->_auth->highest_charge($info['id']); // 1为一级主管，2不是一级主管
        $r['not_read'] = $this->_workflow->not_read($info['id']);   //是否有未读，如果有为true ，没有为false
        $r['user'] = $info;
        $r['token'] = $token;
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 退出
     */
    public function logout()
    {
        session_unset(); 
        return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 修改密码页面
     */
    public function changepassword()
    {
        return view('auth.changepassword')->with('title', '修改密码');
    }
    /**
     * 修改密码
     * @param int $oldpassword   旧密码
     * @param int $newpassword   新密码
     * @return object   errorcode为0时修改成功
     */
    public function changepasswordpost()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
          return $this->_common->output(false, Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $info = $this->_auth->getInfoById($user_id);
        $oldpassword = Request::input('oldpassword', '');
        $newpassword = Request::input('newpassword', '');
        if ($oldpassword == $newpassword) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '新密码不能与原密码重复');    //传参目前type只能为1或2
        }
        return $this->_auth->changepassword($info['account'], $oldpassword, $newpassword); //修改密码
    }

    /**
     * 注册
     * @param int $oldpassword   旧密码
     * @param int $newpassword   新密码
     * @return object   errorcode为0时修改成功
     */
    public function register1()
    {
        $user = array();
        $user['account'] = Request::input('account', '');
        $user['password'] = Request::input('password', '');
        $this->_common->setlog();
        if ($user['account'] == '' || $user['password'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //这个好像没有起到验证密码的作用
        if (strlen($user['password']) <6) {
            return $this->_common->output('', Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_NO, Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_MSG);
        }
        return $this->_role->addUser($user);
    }
    /**
     * 注册
     * @param int $oldpassword   旧密码
     * @param int $newpassword   新密码
     * @return object   errorcode为0时修改成功
     */
    public function register()
    {
        // $this->_common->setlog();
        $param['account'] = Request::input('account', '');
        $password = Request::input('password', '');
        if (strlen($password) < 6) {
            return $this->_common->output('', Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_NO, Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_MSG);
        }
        if (is_numeric($password)) {
            return $this->_common->output('', Constant::ERR_PASSWORD_CANNOT_NUMERIC_NO, Constant::ERR_PASSWORD_CANNOT_NUMERIC_MSG);
        }
        $param['password'] = md5(Request::input('password', ''));
        $param['type'] = '1'; //1为注册，2为重置密码
        $param['sms_code'] = Request::input('sms_code', ''); //6位短信验证码
        if ($param['account'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        return $this->_auth->register($param);
    }

    /**
     * 重置密码
     * @param int $oldpassword   旧密码
     * @param int $newpassword   新密码
     * @return object   errorcode为0时修改成功
     */
    public function resetpasswd()
    {
        // $this->_common->setlog();
        $param['account'] = Request::input('account', '');
        $password = Request::input('password', '');
        if (strlen($password) < 6) {
            return $this->_common->output('', Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_NO, Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_MSG);
        }
        // if (is_numeric($password)) {
        //     return $this->_common->output('', Constant::ERR_PASSWORD_CANNOT_NUMERIC_NO, Constant::ERR_PASSWORD_CANNOT_NUMERIC_MSG);
        // }
        if ($param['account'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        $param['password'] = md5(Request::input('password', ''));
        $param['type'] = '2'; //1为注册，2为重置密码
        $param['sms_code'] = Request::input('sms_code', ''); //6位短信验证码
        return $this->_auth->resetpassword($param);
    }
    /**
     * 注册发送短信验证码
     * @param int $account   账号
     * @return object   errorcode为0时修改成功
     */
    public function sendmessage()
    {
        $this->_common->setlog();
        $param['account'] = Request::input('account', '');
        $param['type'] = Request::input('type', ''); //1为注册，2为重置密码
        $param['sms_code'] = $this->_common->generate_code(6);  //生成短信验证码
        // $param['sms_code'] = '111111';
        if ($param['type'] == '1') {
            $param['content'] = "欢迎使用聚车信贷家访平台，手机验证码是：".$param['sms_code']."，有效时间15分钟，若非本人操作，请忽略。";
        } elseif($param['type'] == '2') {
            $checkAccountIsset = $this->_auth->checkAccountIsset($param['account']);
            if ($checkAccountIsset == false) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '账号不存在！');
            }
            $param['content'] = "您正在重置登录密码，手机验证码是：".$param['sms_code']."，有效时间15分钟，若非本人操作，请忽略。";
        }
        $r = $this->_message->sendmessage($param);
        return $r;
    }
    /**
     * 验证注册码是否正确
     * @param int $account   账号
     * @return object   errorcode为0时修改成功
     */
    public function checkmessage()
    {
        // $this->_common->setlog();
        $param['account'] = Request::input('account', '');
        $param['type'] = Request::input('type', ''); //1为注册，2为重置密码
        $param['sms_code'] = Request::input('sms_code', ''); //验证码  
        $r = $this->_message->checkmessage($param);
        if ($r == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '请输入正确的验证码');
        }
        return $this->_common->output($param, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
//获取用户信息
    public function userinfo()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
          return $this->_common->output(false, Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $info = $this->_auth->getAppinfoById($user_id);
        if ($info == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
        $info['haschild'] = $this->_auth->haschild($user_id); // 1为有下属，2为无下属
        if ($info !== false) {
            return $this->_common->output($info, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    //设置头像
    public function setheadportrait()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
          return $this->_common->output(false, Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $head_portrait =  Request::input('head_portrait', ''); //头像
        $rs = $this->_auth->setheadportrait($user_id, $head_portrait);
        if ($rs !== false) {
            $info = $this->_auth->getAppinfoById($user_id);
            return $this->_common->output($info, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }

    //设置头像
    public function setaddress()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
          return $this->_common->output(false, Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['province'] =  Request::input('province', ''); //省
        $params['city'] =  Request::input('city', ''); //市
        $params['town'] =  Request::input('town', ''); //区
        $params['area_add'] =  Request::input('area_add', ''); //除了三级以外的地址
        $rs = $this->_auth->setaddress($user_id, $params);
        if ($rs !== false) {
            $info = $this->_auth->getAppinfoById($user_id);
            return $this->_common->output($info, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
//验证token有没有过期
    public function checktoken()
    {
        $token =  Request::input('token', '');
        $auth_common = new \App\Http\Models\common\Auth;
        $rs = $auth_common->check_token($token);
        if ($rs == null) {
            return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return json_encode($rs);
        }
        


    }


}
