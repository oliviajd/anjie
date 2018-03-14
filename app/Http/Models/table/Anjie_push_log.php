<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_push_log extends Model
{
    protected  $table='anjie_push_log';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    /**
     * 添加个推推送日志
     * @param $pushLog
     * @return null|\PDOStatement
     */
    public function addPushLog($pushLog){
        $sql = "insert into anjie_push_log(mobile_list, send_data, return_data, add_time, push_action, user_id,result,title) values(?, ?, ?, ?, ?, ?,?,?)";
        $this->_pdo->execute($sql, [$pushLog['mobile_list'],$pushLog['send_data'],$pushLog['return_data'],time(),$pushLog['push_action'],$pushLog['user_id'],$pushLog['result'],$pushLog['title']]);
    }
}
