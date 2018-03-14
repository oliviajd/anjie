<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Api2_token extends Model
{
    protected  $table='api2_token';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    /**  
    *通过token获取信息
    *@params  token      token 
    */
    public function getInfoBytoken($token)
    {
        $sql = "select * from api2_token where token = ?";
        $rs = $this->_pdo->fetchOne($sql, array($token));
        return $rs;
    }
   
}
