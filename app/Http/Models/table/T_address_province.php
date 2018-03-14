<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class T_address_province extends Model
{
    protected  $table='t_address_province';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //获取所有的省信息
    public function getAllProvince()
    {
        $sql = "select * from t_address_province";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //通过名字找到省代码
    public function getcodeByname($city)
    {
        $sql = "select * from t_address_province where name = ?";
        $rs = $this->_pdo->fetchOne($sql, array($city));
        return $rs;
    }
    //通过code找到省代码
    public function getProvinceByCode($code)
    {
        $sql = "select * from t_address_province where code = ?";
        $rs = $this->_pdo->fetchOne($sql, array($code));
        return $rs;
    }

}
