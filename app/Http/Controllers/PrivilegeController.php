<?php

namespace App\Http\Controllers;
use App\Http\Models\business\Privilege;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Auth;

class PrivilegeController extends Controller
{ 
    private $_auth = null;
    public function __construct()
    {
        parent::__construct();
        $this->_privilege = new Privilege();
        $this->_auth = new Auth();
    }

    public function sidebar()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $sql = "select * from v1_user_role where user_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($user_id));
        $privilege = array();
        foreach ($rs as $key => $value) {
            if ($value['role_id'] == '1') {
                $sql = "select * from anjie_category where is_menu = 1 order by sort";
                $category = $this->_pdo->fetchAll($sql, array());
                $sql = "select * from anjie_method where status = 1 and is_bar= 1 order by sort";
                $method = $this->_pdo->fetchAll($sql, array());
                break;
            } else {
                $sql = "select * from v1_role_privilege where role_id = ?";
                $privilege = array_merge($this->_pdo->fetchAll($sql, array($value['role_id'])), $privilege);
            }
        }
        if (empty($method) && empty($category)) {
            $module_id = array_unique(array_column($privilege, 'module_id'));
            $mudule_id_str = "'".implode("','", $module_id)."'";
            $method_id = array_unique(array_column($privilege, 'method_id'));
            $method_id_str = "'".implode("','", $method_id)."'";
            foreach ($privilege as $key => $value) {
                if ($value['method_id'] == '0') {
                    $sql = "select * from anjie_category where id in(".$mudule_id_str.") and is_menu = 1  order by sort";
                    $category = $this->_pdo->fetchAll($sql, array());
                } else {
                    $sql = "select * from anjie_method where status = 1 and is_bar= 1 and method_id in(".$method_id_str.")";
                    $method = $this->_pdo->fetchAll($sql, array());
                }
            }
        }
        foreach ($method as $module_id => $value) {
                $path = ltrim($value['path'], '/');
                $methodurlarr = explode('/', $path);
                $method[$module_id]['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/Admin' . $value['path'];
        }
        $result['category'] = $category;
        $result['method'] = $method;
        return $this->_common->output($result, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }
}