<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_user_role_area_privilege extends Model
{
    protected  $table='anjie_user_role_area_privilege';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    /*  
        通过user_id,role_id获取可用城市列表
    */
    public function getCityLists($user_id,$role_id)
    {
        $sql = "select city_name from {$this->table} where user_id = ? and role_id = ? and status = 1";
        $rs = $this->_pdo->fetchAll($sql, array(intval($user_id),intval($role_id)));
        return $rs;
    }
    
    public function setPrivilege($user_id,$role_id,$citys) {
        $sql = "update {$this->table} set status = 2 where user_id = ? and role_id = ?";
        $this->_pdo->execute($sql, array($user_id, $role_id));
        foreach ($citys as $k => $v) {
            $exists = $this->_pdo->fetchOne("select city_name from {$this->table} where user_id = ? and role_id = ? and city_name = ?", array(intval($user_id), intval($role_id), trim($v['name'])));
            if ($exists) {
                $this->_pdo->execute("update {$this->table} set status = 1 where user_id = ? and role_id = ? and city_name = ?", array($user_id, $role_id, trim($v['name'])));
            } else {
                $this->_pdo->execute("insert into {$this->table} set status = 1, user_id = ? , role_id = ? , city_name = ?", array($user_id, $role_id, trim($v['name'])));
            }
        }
        return true;
    }
   
}
