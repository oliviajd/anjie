<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_service extends Model
{
    protected  $table='anjie_service';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //通过账号获取用户信息
    public function getDetail($where, $limit='', $order='')
    {
      $sql = "select * from anjie_service " . $where . $limit. $order;
      $rs = $this->_pdo->fetchAll($sql, array());
      return $rs;
    }
}
