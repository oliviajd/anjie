<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\business\Auth;
use App\Http\Models\table\Anjie_users;

class Apiprivileges
{
	protected $_pdo = null;
    private $_auth = null;
    private $_common = null;

    public function __construct()
    {
        $this->_pdo = new Pdo();
        $this->_anjie_users = new Anjie_users();
        $this->_auth = new Auth();
        $this->_common = new Common();
    }

	public function run()
    {
        //传入token先验证用户是否存在
    	$token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        //验证是否有权限
        $rs = $this->checkPermission($user_id);
        if ($rs['errorcode'] !== '0') {
            die(json_encode($rs));
        }
        return $rs;
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
    //验证是否有当前页面的权限
    public function checkPermission($userid)
    {
        $sql = "select * from v1_user_role where user_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($userid));
        $anjie_privilege = array();
        $privilege = array();
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