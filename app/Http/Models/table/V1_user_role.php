<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class V1_user_role extends Model
{
    protected  $table='v1_user_role';
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
    public function getuUserrole($where, $order='', $limit='')
    {
      $sql = "select * from v1_user_role " . $where . $order . $limit;
      $rs = $this->_pdo->fetchAll($sql, array());
      return $rs;
    }

    public function getCount($where)
    {
        $sql = "select count(1) as count from v1_user_role " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function getInfoByUseridAndRoleid($userid, $roleid)
    {
        $sql = "select * from v1_user_role where user_id = ? and role_id = ? limit 1";
        $rs = $this->_pdo->fetchOne($sql, array($userid, $roleid));
        return $rs;
    }

    public function addUserRole($param)
    {
        $sql = "insert into v1_user_role (user_id, role_id, create_time) values(?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($param['user_id'], $param['role_id'], $param['create_time']));
        $id = $this->_pdo->lastInsertId();
        return $id;
    }

    public function deleteByUseridAndRoleid($userid, $roleid)
    {
        $sql = "delete from v1_user_role where user_id = ? and role_id =?";
        $rs = $this->_pdo->execute($sql, array($userid, $roleid));
        $rowaccount = $this->_pdo->RowsAffected();
        return $rowaccount;
    }
    public function deleteByUserid($userid)
    {
        $sql = "delete from v1_user_role where user_id = ?";
        $rs = $this->_pdo->execute($sql, array($userid));
        $rowaccount = $this->_pdo->RowsAffected();
        return $rowaccount;
    }

    public function getuseridsByRoleid($roleid)
    {
        $sql = "select * from v1_user_role where role_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($roleid));
        $user_ids = array_column($rs, 'user_id');
        return $user_ids;
    }

    public function getRoleidByuserid($userid)
    {
        $sql = "select * from v1_user_role where user_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($userid));
        $role_ids = array_column($rs, 'role_id');
        return $role_ids;
    }
//获取所有有身份的人的user_id
    public function getidentifiedid()
    {
        $sql = "select distinct user_id from v1_user_role";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getInfoByroleid($roleid)
    {
        $sql = "select * from v1_user_role where role_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($roleid));
        return $rs;
    }
   
}
