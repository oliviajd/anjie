<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_visit_back extends Model
{
    protected  $table='anjie_visit_back';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //插入退回记录
    public function insertback($user_id, $param)
    {
        $sql = "insert into anjie_visit_back (work_id, from_user_id, to_user_id, description, create_time, modify_time) values(?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($param['work_id'], $user_id, $param['to_user_id'], $param['description'], time(), time()));
        return $rs;
    }
}
