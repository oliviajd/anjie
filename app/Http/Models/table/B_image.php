<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class B_image extends Model
{
    protected  $table='b_iamge';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo('linrun');
    }
    /**  
        获取图片列表
    **/
    public function get($where, $limit)
    {
        $sql = "select ID,RecID,Type,Name,Width,Height,Weights,IsDeleted from b_image " . $where . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    public function getinfobyid($id)
    {
        $sql = "select Data from b_image where ID = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }

}
