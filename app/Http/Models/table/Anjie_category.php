<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_category extends Model
{
    protected  $table='anjie_category';
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
        $sql = "select * from anjie_category " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getInfoById($id)
    {
        $sql = "select * from anjie_category where id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }
    //add
    public function getInfoByIdd($id)
    {
        $sql = "select id, menu_title, @cid:=0 as cid  from anjie_category where id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }
   
}
