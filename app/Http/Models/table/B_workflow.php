<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class B_workflow extends Model
{
    protected  $table='b_workflow';
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
    public function getinfobyfileid($FileID)
    {
        $sql = "select * from b_workflow where FileID =? order by ModifyTime";
        $rs = $this->_pdo->fetchAll($sql, array($FileID));
        return $rs;
    }
    public function getuserinfo($optid)
    {
        $sql = "select * from g_empinfo where EMPID = ?";
        $rs = $this->_pdo->fetchOne($sql, array($optid));
        return $rs;
    }
}
