<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Linrun_image extends Model
{
    protected  $table='linrun_iamge';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    /**  
        获取图片列表
    **/
    public function addimage($id, $recid, $type, $name, $width, $height, $weights, $isdeleted)
    {
        $sql = "insert into linrun_image (ID, RecID, Type, Name, Width, Height, Weights, IsDeleted, create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
        $rs = $this->_pdo->execute($sql, array($id, $recid, $type, $name, $width, $height, $weights, $isdeleted, time(), time()));
        return $rs;
    }
    //获取图片
    public function getimage($where, $limit)
    {
        $sql = "select * from linrun_image ". $where . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    public function getusers()
    {
        $sql = "select * from g_empinfo";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    public function getinfobyId($id)
    {
        $sql = "select * from linrun_image where ID = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }
    public function updatestatus($ID)
    {
        $sql = "update linrun_image set has_transfer = 1 where ID =? ";
        $rs = $this->_pdo->execute($sql, array($ID));
        return $rs;
    }
    //出错的
    public function updatestatus3($ID)
    {
        $sql = "update linrun_image set has_transfer = 3 where ID =? ";
        $rs = $this->_pdo->execute($sql, array($ID));
        return $rs;
    }
}
