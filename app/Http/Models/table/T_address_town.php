<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class T_address_town extends Model
{
    protected  $table='t_address_town';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
//通过市代码获取所有的区信息
    public function gettownBypcode($citycode)
    {
        $sql = "select * from t_address_town where cityCode =?";
        $rs = $this->_pdo->fetchAll($sql, array($citycode));
        return $rs;
    }

}
