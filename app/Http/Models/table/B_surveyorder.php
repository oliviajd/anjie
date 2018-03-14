<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class B_surveyorder extends Model
{
    protected  $table='b_surveyorder';
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
    public function getneed()
    {
        $sql = "select * from b_surveyorder where OrderState in ('11', '13', '15', '17')";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
   
}
