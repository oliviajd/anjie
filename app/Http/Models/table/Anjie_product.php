<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_product extends Model
{
    protected  $table='anjie_product';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function getInfo()
    {
        $sql = "select * from anjie_product";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getInfoByid($id)
    {
        $sql = "select * from anjie_product where id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }
}
