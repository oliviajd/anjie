<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_method extends Model
{
    protected  $table='anjie_method';
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
        $sql = "select * from anjie_method " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    /*  
        通过method_id获取信息
        @params  method_id      
    */
    public function getInfoByMethodid($methodid)
    {
        $sql = "select * from anjie_method where method_id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($methodid));
        return $rs;
    }
    //add
    public function getInfoByMethodidd($methodid)
    {
        $sql = "select * from anjie_method where method_id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($methodid));
        return $rs;
    }
   
}
