<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_province extends Model
{
    protected  $table='anjie_province';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function getAllProvinceInfo()
    {
        $sql = "select * from anjie_province";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getProvinceIdByProvincename($provincename)
    {
        $sql = "select id from anjie_province where provincename = ?";
        $rs = $this->_pdo->fetchOne($sql, array($provincename));
        return $rs['id'];
    }

}
