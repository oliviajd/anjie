<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class B_budgetorder extends Model
{
    protected  $table='b_budgetorder';
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
    public function getinfobyRecId($RecId)
    {
        $sql = "select * from b_budgetorder where RecID = ?";
        $rs = $this->_pdo->fetchOne($sql, array($RecId));
        return $rs;
    }
   
}
