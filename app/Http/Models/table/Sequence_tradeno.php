<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Sequence_tradeno extends Model
{
    protected  $table='sequence_tradeno';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    /**  
    *自增id
    */
    public function sequence_increment($work_id)
    {
        $sql = "select * from bank_tradeno where work_id =?";
        $rs = $this->_pdo->fetchOne($sql, array($work_id));
        if (!empty($rs)) {
            return $rs;
        }
        $res = array();
        $sql = "insert into bank_tradeno (work_id) value(?)";
        $rs = $this->_pdo->execute($sql, array($work_id));
        $res['id'] = $this->_pdo->lastInsertId();
        $res['tradeno'] = '002' . env('IFCAR_BANK_TRADENO') . sprintf("%06d", intval($res['id']));
        $sql = "update bank_tradeno set tradeno =? where id =?";
        $update = $this->_pdo->execute($sql, array($res['tradeno'], $res['id']));
        return $res;
    }
   
}
