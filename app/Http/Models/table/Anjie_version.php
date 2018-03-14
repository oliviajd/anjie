<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_version extends Model
{
    protected  $table='anjie_version';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //根据版本号和来源获取详情
    public function getinfobyversion($version, $origin)
    {
        $sql = "select * from anjie_version where version = ? and origin = ? and is_delete =2";
        $rs = $this->_pdo->fetchOne($sql, array($version, $origin));
        return $rs;
    }
    //获取最新的版本
    public function getnewversion($origin)
    {
        $sql = "select * from anjie_version where origin = ? and is_delete =2 order by sort desc limit 1";
        $rs = $this->_pdo->fetchOne($sql, array($origin));
        return $rs;
    }
    public function checkforceupdate($origin, $sort)
    {
        $sql = "select * from anjie_version where force_update =1 and is_delete =2 and origin =? and sort > ?";
        $rs = $this->_pdo->fetchOne($sql, array($origin, $sort));
        return $rs;
    }
}
