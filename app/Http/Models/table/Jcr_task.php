<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Jcr_task extends Model
{
    protected  $table='jcr_task';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    public function taskentry($params, $iteminfo)
    {
        $sql = "insert into jcr_task (process_id, process_instance_id, process_title, user_id, role_id, status, csr_id, item_id, item_instance_id, task_instance_id, task_title, task_description, create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($params['process_id'], $params['process_instance_id'], '', '', $iteminfo['role_id'], '4', $params['csr_id'], $params['current_item_id'], $params['item_instance_id'], $params['item_instance_id'], $iteminfo['title'], $iteminfo['title'], time(), time()));
        return $rs;
    }
//拾取任务
    public function pickupTask($params)
    {
        $arr = array();
        $str = '';
        foreach ($params as $key=>$value) {
            $params = $value;
            $arr[] = $params['process_id'];
            $arr[] = $params['process_instance_id'];
            $arr[] = $params['process_title'];
            $arr[] = $params['user_id'];
            $arr[] = $params['role_id'];
            $arr[] = 1;
            $arr[] = $params['csr_id'];
            $arr[] = $params['item_id'];
            $arr[] = $params['item_instance_id'];
            $arr[] = $params['item_instance_id'];
            $arr[] = $params['task_title'];
            $arr[] = $params['task_title'];
            $arr[] = time();
            $arr[] = time();
            $str = $str."(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),";
        }
        $str = substr($str, 0, strlen($str)-1);
        $sql = "insert into jcr_task (process_id, process_instance_id, process_title, user_id, role_id, status, csr_id, item_id, item_instance_id, task_instance_id, task_title, task_description, create_time, modify_time) values ".$str;
        $rs = $this->_pdo->execute($sql, $arr);
        return $rs;
    }
//task_status  任务状态，1为通过，2为拒绝，3为待补件
    public function completetask($params)
    {
        if (!isset($params['msg'])) {
            $params['msg'] = '';
        }
        $sql = "update jcr_task set status = 2, task_status=?, modify_time =?, msg=? where task_instance_id = ?";
        $rs = $this->_pdo->execute($sql, array($params['task_status'], time(), $params['msg'], $params['task_instance_id']));
        return $rs;
    }
//判断任务已存在
    public function issettask($task_instance_id)
    {
        $sql = "select * from jcr_task where task_instance_id =?";
        $rs = $this->_pdo->fetchOne($sql, array($task_instance_id));
        return $rs;
    }
    //更新任务状态   1为认领了这个任务，2为完成了这个任务，3为放弃了这个任务
    public function picktask($status, $task_instance_id, $user_id)
    {
        $sql = "update jcr_task set status = ?, modify_time=?, user_id=?  where task_instance_id =?";
        $rs = $this->_pdo->execute($sql, array($status, time(), $user_id, $task_instance_id));
        return $rs;
    }
//更新任务状态   1为认领了这个任务，2为完成了这个任务，3为放弃了这个任务
    public function updatetask($status, $task_instance_id, $user_id='')
    {
        $sql = "update jcr_task set status = ?, modify_time=?, user_id=?  where task_instance_id =?";
        $rs = $this->_pdo->execute($sql, array($status, time(), $user_id, $task_instance_id));
        return $rs;
    }
//通过csr_id获取数据
    public function getDetailByCsrid($params)
    {
        $sql = "select id, csr_id, user_id, task_title, create_time, modify_time, status, task_status, item_id, msg from jcr_task where csr_id = ? and process_id =? order by create_time asc";
        $rs = $this->_pdo->fetchAll($sql, array($params['csr_id'], $params['process_id']));
        foreach ($rs as $key => $value) {
            $sql = "select * from anjie_users where id=?";
            $user = $this->_pdo->fetchOne($sql, array($value['user_id']));
            if (!empty($user)) {
                $rs[$key]['name'] = $user['name'];
            } else {
                $rs[$key]['name'] = '';
            }
            $sql = "select * from jcr_task_item where v1_item_id=?";
            $task_info = $this->_pdo->fetchOne($sql, array($value['item_id']));
            if (!empty($task_info)) {
                $rs[$key]['button_title'] = $task_info['button_title'];
                $rs[$key]['button_class'] = $task_info['button_class'];
            } else {
                $rs[$key]['button_title'] = '';
                $rs[$key]['button_class'] = '';
            }
        }
        return $rs;
    }
    //获取该用户所有经手过的件
    public function getDistinctCsridsByUserid($user_id)
    {
        $sql = "select distinct csr_id from jcr_task where user_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($user_id));
        return $rs;
    }
    //查该件是否已完成
    public function workComplete($csr_id)
    {
        $gps=false;
        $returnmoney = false;
        $sql = "select * from jcr_task where csr_id = ?";
        $rs = $this->_pdo->fetchAll($sql, array($csr_id));
        foreach ($rs as $key => $value) {
            if ($value['item_id'] == '43' && $value['status']=='2' && $value['task_status'] == '1') {    //车辆GPS登记
                $gps = true;
            }
            if ($value['item_id'] == '40' && $value['status']=='2' && $value['task_status'] == '1') {    //回款确认
                $returnmoney = true;
            }
        }
        if ($gps == true && $returnmoney == true) {
            return true;
        }
        return false;
    }
    //获得最后一个审核补件的人
    public function getartificialtask($params)
    {
        $sql = "select * from jcr_task where task_status = 3 and item_id =? and csr_id = ? order by modify_time desc";
        $rs = $this->_pdo->fetchOne($sql, array($params['item_id'], $params['csr_id']));
        return $rs;
    }
    //获得最后一个审核补件的人
    public function getartificialtask1($params)
    {
        $sql = "select * from jcr_task where task_status = 3 and (item_id =9 or item_id = 37) and csr_id = ? order by modify_time desc";
        $rs = $this->_pdo->fetchOne($sql, array($params['csr_id']));
        return $rs;
    }
}
