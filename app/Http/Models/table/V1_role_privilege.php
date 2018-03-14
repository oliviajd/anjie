<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class V1_role_privilege extends Model
{
    protected  $table='v1_role_privilege';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    /*  
        通过file_token获取信息
        @params  file_token      文件token 
    */
    public function getprivilege($where, $order='', $limit='')
    {
      $sql = "select * from v1_role_privilege " . $where . $order . $limit;
      $rs = $this->_pdo->fetchAll($sql, array());
      return $rs;
    }

    public function getCount($where)
    {
        $sql = "select count(1) as count from v1_user_role " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function deleteByRoleid($id)
    {
        $sql = "delete from v1_role_privilege where role_id = ?";
        $rs = $this->_pdo->execute($sql, array($id));
        return $rs;
    }

    public function insetToPrivilege($privilege)
    {
        $arr = array();
        $str = '';
        foreach ($privilege as $key=>$value) {
            $arr[] = $value['role_id'];
            $arr[] = $value['module_id'];
            $arr[] = $value['module'];
            $arr[] = $value['method_id'];
            $arr[] = $value['method'];
            $arr[] = $value['status'];
            $arr[] = $value['create_time'];
            $arr[] = isset($value['privilege_id']) ? $value['privilege_id'] :0;
            $str = $str."(?, ?, ?, ?, ?, ?, ?, ?),";
        }
        $str = substr($str, 0, strlen($str)-1);
        $sql = "insert into v1_role_privilege(role_id, module_id, module, method_id, method, status, create_time, privilege_id)
               values ".$str;
        $rs = $this->_pdo->execute($sql, $arr);
        return $rs;
    }
   
}
