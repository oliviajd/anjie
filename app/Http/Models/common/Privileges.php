<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;
use App\Http\Models\table\Anjie_users;
use App\Http\Models\business\Auth;

class Privileges
{
	protected $_pdo = null;

    public function __construct()
    {
        $this->_pdo = new Pdo();
        $this->_anjie_users = new Anjie_users();
        $this->_auth = new Auth();
    }

	public function run()
    {
    	if(isset($_SESSION['account'])){
    		$account = $_SESSION['account'];
    		$issetAccount = $this->issetAccount($account);  //先验证账号是否存在
    		if (!$issetAccount) {
    			$result = $this->output('登录账号不存在', '-2');
    			return $result;
    		}
    		if(isset($_SESSION['passwd'])){
    			$passwd = $_SESSION['passwd'];
    			$checkpasswd = $this->checkpasswd($account, $passwd);   //验证密码是否正确
    			if (!$checkpasswd) {
    				$result = $this->output('密码不正确', '-2');
    				return $result;
    			}
    		}
            $user_id = $_SESSION['user_id'];
    	}else{
            $token =  Request::input('token', '');
            $user_id = $this->_auth->getUseridBytoken($token);
            if (!$user_id) {
                $result = $this->output('用户不存在', '-2');
                return $result;
            }
            $rs = $this->_anjie_users->getInfoById($user_id);
            if (empty($rs)) {
                $result = $this->output('请先登录', '-2');
                return $result;
            }
    	}
        $rs = $this->checkPermission($user_id);
        if (isset($rs['errorcode']) && $rs['errorcode'] !== '0') {
            return $rs;
        }
    }
   //验证账号是否存在
    public function issetAccount($account)
    {
    	$rs = $this->_anjie_users->getInfoByAccount($account);
    	if (empty($rs)) {
    		return false;
    	}
    	return true;
    }
    //验证密码是否正确
    public function checkpasswd($account, $passwd)
    {
    	$rs = $this->_anjie_users->getInfoByAccount($account);
    	if (isset($rs['passwd']) && $rs['passwd'] == $passwd) {
    		return true;
    	} else {
    		return false;
    	}
    }  
    //输出信息整理成数组
    public function output($errormsg='', $errorcode=0)
    {
    	$result['errormsg'] = $errormsg;
    	$result['errorcode'] = $errorcode;
        $result['error_msg'] = $errormsg;
        $result['error_no'] = $errorcode;
    	return $result;
    }
    public function checkPermission($userid)
    {
        $sql = "select * from v1_user_role where user_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($userid));
        $anjie_privilege = array();
        $privilege = array();
        $method = array();
        foreach ($rs as $key => $value) {
            if ($value['role_id'] == '1') {
                $sql = "select * from anjie_privilege";
                $anjie_privilege = $this->_pdo->fetchAll($sql, array());
                break;
            } else {
                $sql = "select * from v1_role_privilege where role_id = ?";
                $privilege = array_merge($this->_pdo->fetchAll($sql, array($value['role_id'])), $privilege);
            }
        }
        //不需要检查的privilege
        
        $sql = "select * from anjie_privilege where not_check = 1";
        $rs = $this->_pdo->fetchAll($sql, array());
        $anjie_privilege = array_merge($anjie_privilege, $rs);

        foreach ($privilege as $key => $value) {
            $sql = "select * from anjie_privilege where id = ?";
            $rs = $this->_pdo->fetchAll($sql, array($value['privilege_id']));
            $anjie_privilege = array_merge($anjie_privilege, $rs);
        }

        $path = array_column($anjie_privilege, 'path');
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $parse = parse_url($url);
        $uri = rtrim($parse['path'], '/');
        if (in_array($uri, $path) == false) {
            $result = $this->output('没有权限！', '-1');
            return $result;
        }
        $result = $this->output('', '0');
        return $result;
    }


}