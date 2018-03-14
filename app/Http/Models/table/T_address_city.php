<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class T_address_city extends Model
{
    protected  $table='t_address_city';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //通过省代码找到市
    public function getcityBypcode($provincecode)
    {
        $sql = "select * from t_address_city where provinceCode = ?";
        $rs = $this->_pdo->fetchAll($sql, array($provincecode));
        return $rs;
    }
    //通过城市名找到对应的城市代码
    public function getcodeByname($province)
    {
        $sql = "select * from t_address_city where name = ?";
        $rs = $this->_pdo->fetchOne($sql, array($province));
        return $rs;
    }
    //通过城市名找到对应的城市代码
    public function getCityByCode($code)
    {
        $sql = "select * from t_address_city where code = ?";
        $rs = $this->_pdo->fetchOne($sql, array($code));
        return $rs;
    }

    //查找所有城市
    public function getAllCity()
    {
        $sql = "select * from t_address_city";
        $rs = $this->_pdo->fetchAll($sql);
        return $rs;
    }

}
