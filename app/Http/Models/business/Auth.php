<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use Upyun\Upyun;
use Upyun\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Constant;
use App\Http\Models\table\Anjie_users;
use App\Http\Models\table\Anjie_role;
use App\Http\Models\table\Anjie_login;
use App\Http\Models\table\Api2_token;
use App\Http\Models\table\V1_user_role;
use App\Http\Models\table\V1_role;
use App\Http\Models\table\Anjie_sms_message;

class Auth extends Model
{
  protected $_anjie_users = null;
  protected $_anjie_role = null;
  protected $_anjie_login = null;
  protected $_api2_token = null;
  protected $_v1_user_role = null;
  protected $_v1_role = null;
  protected $_anjie_sms_message = null;

  public function __construct()
  {
    parent::__construct();
    $this->_anjie_role = new Anjie_role();
    $this->_anjie_users = new Anjie_users();
    $this->_anjie_login = new Anjie_login();
    $this->_api2_token = new Api2_token();
    $this->_v1_user_role = new V1_user_role();
    $this->_v1_role = new V1_role();
    $this->_anjie_sms_message = new Anjie_sms_message();
  }
  //验证账号是否存在
    public function checkAccountIsset($account)
    {
      $rs = $this->_anjie_users->getInfoByAccount($account);
      if (empty($rs)) {
        return false;
      }
      return true;
    }

  //验证密码的正确性
  public function checkPasswordByAccount($account, $password)
  {
    $info = $this->_anjie_users->getInfoByAccount($account);
    if (isset($info['passwd']) && $info['passwd'] == $password) {
      return $info;
    } else {
      return false;
    }
  }
  //修改密码
  public function changepassword($account, $oldpassword, $newpassword)
  {
    $checkoldpassword = $this->checkPasswordByAccount($account, $oldpassword); //验证密码的正确性
    if ($checkoldpassword) {
      $rs = $this->_anjie_users->changepassword($account, $newpassword);
      if ($rs) {
        return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
      }
    } else {
      return $this->_common->output(false, Constant::ERR_PASSWORD_NO_CORRECT_NO, Constant::ERR_PASSWORD_NO_CORRECT_MSG);
    }
  }
  //写入登录日志表
  public function setLoginLog($account, $user_id, $name)
  {
    $rs = $this->_anjie_login->setLoginLog($account, $user_id, $name);
    return $rs;
  }
  //写入用户表中的上次登录时间
  public function setlastlogintime($account)
  {
    $lastlogin = $this->_anjie_login->getlastlogin($account);
    $rs = true;
    if (!empty($lastlogin)) {
      $rs = $this->_anjie_users->setlogintime($lastlogin['login_time'], $account);
    }
    return $rs;
  }
  /**  
    *通过token获取信息
    *@params  token      token 
    */
  public function getUseridBytoken($token)
  {
    $info = $this->_api2_token->getInfoBytoken($token);
    if ($info) {
      return $info['user_id'];
    } else {
      return false;
    }
  }
   /**  
    *通过token获取信息
    *@params  token      token 
    */
  public function getInfoBytoken($token)
  {
    $info = $this->_api2_token->getInfoBytoken($token);
    return $info;
  }

  public function getInfoById($user_id)
  {
    $rs = $this->_anjie_users->getInfoById($user_id);
    return $rs;
  }
  public function getAppinfoById($user_id)
  {
    $rs = $this->_anjie_users->getAppinfoById($user_id);
    if ($rs['head_portrait'] !== '') {
      if (strpos($rs['head_portrait'],'http') !== false || strpos($rs['head_portrait'],'https') !== false) {
        $rs['head_portrait'] = $rs['head_portrait'];
      } else {
        $rs['head_portrait'] =  $path = 'http://' . $_SERVER['HTTP_HOST'] .'/'. $rs['head_portrait'];
      }
    }
    $where = " where user_id = " . $user_id;
    $role = $this->_v1_user_role->getuUserrole($where);
    if (empty($role)) {
      return false;
    }
    $roleid = $role[0]['role_id'];
    $rolename = $this->_v1_role->getDetail($roleid);
    $rs['rolename'] = $rolename['title'];
    return $rs;
  }
  /**  
    *发送短信验证码
    *@params  token      token 
    */
  public function sendmessage($params)
  {
    $rs = $this->_anjie_sms_message->insertmessage($params);
    return $rs;
  }
  /**  
    *重置密码
    *@params  token      token 
    */
  public function resetpassword($params)
  {
    $isset = $this->_anjie_sms_message->checkmessage($params);
    if ($isset) {
      $userinfo = $this->_anjie_users->getInfoByAccount($params['account']);
      if ($userinfo['passwd'] == $params['password']) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '新密码不能与原密码重复');
      }
      $rs = $this->_anjie_users->changepassword($params['account'], $params['password']);
      if ($rs) {
        return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '修改密码失败');
      }
    } else {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '验证码错误');
    }
  }
  /**  
    *注册
    *@params  token      token 
    */
  public function register($params)
  {
    $isset = $this->_anjie_sms_message->checkmessage($params); 
    if ($isset) {
      $ifisset = $this->_anjie_users->getInfoByAccount($params['account']);
      if(!$ifisset) {
        $adduser = $this->_anjie_users->addUser($params);
        return $this->_common->output($adduser, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
      } else {
        return $this->_common->output('', Constant::ERR_ACCOUNT_EXISTS_NO, Constant::ERR_ACCOUNT_EXISTS_MSG);
      }
    } else {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
    }
  }
//写头像
  public function setheadportrait($user_id, $head_portrait)
  {
    $rs = $this->_anjie_users->setheadportrait($user_id, $head_portrait);
    return $rs;
  }
  //写地址
  public function setaddress($user_id, $params)
  {
    $rs = $this->_anjie_users->setaddress($user_id, $params);
    return $rs;
  }
//判断该用户是否有下属, 1为有下属，2为无下属
  public function haschild($user_id)
  {
    $roleid = $this->_v1_user_role->getRoleidByuserid($user_id);
    $haschild = '2';
    foreach ($roleid as $key => $value) {
      $rs = $this->_v1_role->getDetail($value);
      if ($rs['has_child'] == '1') {
        $haschild = '1';
      }
    }
    return $haschild;
  }
//判断该用户是否为一级家访主管
  public function highest_charge($user_id)
  {
    $roleid = $this->_v1_user_role->getRoleidByuserid($user_id);
    $highest_charge = '2';
    foreach ($roleid as $key => $value) {
      if ($value == env('VISIT_ROLE_ID')) {
        $highest_charge = '1';
      }
    }
    return $highest_charge;
  }
}

