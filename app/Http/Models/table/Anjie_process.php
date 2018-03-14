<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_process extends Model
{
    protected  $table='anjie_process';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //创建进程
    public function createProcess($params)
    {
      $sql = "insert into anjie_process (process_id, process_instance_id, process_title, user_id, role_id, work_id, create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?, ?)";
      $rs = $this->_pdo->execute($sql, array($params['process_id'], $params['process_instance_id'], $params['title'], $params['user_id'], $params['role_id'], $params['work_id'], time(), time()));
      return $rs;
    }

    //开始任务
    public function creatProcesstask($params)
    {
        $sql = "update anjie_process set item_id = ?, item_instance_id = ?, task_instance_id =?, task_title = ?, current_task_id = ?,  current_task_description = ?, modify_time = ? where process_instance_id = ?";
        $rs = $this->_pdo->execute($sql, array($params['item_id'], $params['item_instance_id'], $params['item_instance_id'], $params['task_title'], $params['item_instance_id'], $params['task_title'], time(), $params['process_instance_id']));
      return $rs;
    }

    public function getBusinessidByInstanceid($process_instance_id)
    {
        $process_instance_id_arr = "'".implode("','", $process_instance_id)."'";
        $sql = "select * from anjie_process where process_instance_id in(".$process_instance_id_arr.")";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
}
