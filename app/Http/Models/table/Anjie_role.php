<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_role extends Model
{
    protected  $table='anjie_role';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //通过账号获取用户信息
    public function getInfoById($id)
    {
      $sql = "select * from anjie_role where id = ?";
      $rs = $this->_pdo->fetchOne($sql, array($id));
      return $rs;
    }
}
