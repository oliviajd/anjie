<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class G_dict extends Model
{
    protected  $table='g_dict';
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
    public function getinfobyitemid($ITEMID)
    {
        $sql = "select * from g_dict where ITEMID =? and DICID = 'ORDERSTATE'";
        $rs = $this->_pdo->fetchOne($sql, array($ITEMID));
        return $rs;
    }
   
}
