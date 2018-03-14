<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Image_migration extends Model
{
    protected  $table='b_iamge';
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
    public function updateimage($key, $value)
    {
        $sql = "update image_migration set value = ?  where `key` = ?";
        $rs = $this->_pdo->execute($sql, array($value, $key));
        return $rs;
    }
    public function updatecounts($key,$value)
    {
        $sql = "update image_migration set counts = counts+1  where `key` = ?";
        $rs = $this->_pdo->execute($sql, array($key));
        return $rs;   
    }
    public function getmigration($key)
    {
        $sql = "select * from image_migration where `key` = ?";
        $rs = $this->_pdo->fetchOne($sql, array($key));
        return $rs;
    }
    
}
