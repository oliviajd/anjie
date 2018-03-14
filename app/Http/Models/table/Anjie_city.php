<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_city extends Model
{
    protected  $table='anjie_city';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function getInfoByProvinceid($provinceid)
    {
        $sql = "select * from anjie_city where provinceid = ?";
        $rs = $this->_pdo->fetchAll($sql, array($provinceid));
        return $rs;
    }

    public function getCityIdByCityname($cityname)
    {
        $sql = "select id from anjie_city where cityname = ?";
        $rs = $this->_pdo->fetchOne($sql, array($cityname));
        return $rs['id'];
    }
}
