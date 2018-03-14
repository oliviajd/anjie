<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_privilege extends Model
{
    protected  $table='anjie_privilege';
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
    public function getDetail($where, $order, $limit)
    {
        $sql = "select * from anjie_privilege " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getPrivilegeById($id)
    {
        $sql = "select * from anjie_privilege where id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }

    public function getPrivilegeByMethodId($id)
    {
        $sql = "select * from anjie_privilege where method_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($id));
        return $rs;
    }
   
}
