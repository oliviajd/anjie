<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\table\Jcr_task;
require base_path('app/Http/Models/common/WorkFlow.php');
use App\Http\Models\common\ProcessInstance;
use App\Http\Models\common\TaskInstance;
use App\Http\Models\common\UserTask;
use App\Http\Models\table\Jcr_file;
use App\Http\Models\business\Role;
use App\Http\Models\business\Auth;

class Workflowcsr extends Model
{
  protected $_process_instance = null;
  protected $_task_instance = null;
  protected $_user_task = null;
  protected $_jcr_task = null;
  protected $_jcr_file = null;
  protected $_role = null;
  public function __construct()
  {
    parent::__construct();
    $this->_process_instance = new ProcessInstance();
    $this->_task_instance = new TaskInstance();
    $this->_user_task = new UserTask();
    $this->_jcr_task = new Jcr_task();
    $this->_jcr_file = new Jcr_file();
    $this->_role = new Role();
    $this->_auth = new Auth();

  }
  //创建和开始进程
  public function createprocess($user_id, $params)
  {
    $creaters = $this->_common->object_array(json_decode($this->_process_instance->create($user_id, '2')));
    
    if($creaters['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    $data = array(
      'customer_user_id' => $user_id,
      'csr_id' => $params['id'],
      'money_request' => $params['money'],
      'car_stock_request' => $params['car_stock'],
      'deadline_request' => $params['deadline'],
      'rate_request' => $params['rate'],
      'create_time' => $params['create_time'],
      'csr_no' => $params['csr_no'],
      'name' => $params['name'],
      'shopname' => $params['shopname'],
    );
    $startrs = $this->_common->object_array(json_decode($this->_process_instance->start($user_id, $creaters['result']['process_instance_id'], json_encode($data))));
    if($startrs['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    $issettask = $this->_jcr_task->issettask($startrs['result']['rows'][0]['item_instance']['item_instance_id']);
    if (!empty($issettask)) {
      //如果已存在，则直接更新状态为1，不存在则往下走
      $rs = $this->_jcr_task->updatetask('1', $startrs['result']['rows'][0]['item_instance']['item_instance_id']);
    } else {
      $param = $this->formatparams($startrs, $params['id']);
      $pickuprs = $this->_jcr_task->pickupTask($param);
      $data['task_status'] = '1';
      $msg = '提交融资申请';
      $rs = $this->completeworkflow($user_id, $startrs['result']['rows'][0]['item_instance']['item_instance_id'], $data, $msg);
    }
    if ($rs !== false) {
      $rs = true;
    }
    return $rs;
  }
  //格式化传入参数
  public function formatparams($params, $csr_id)
  {
    if ($params['error_no'] !== 200) {
      return false;
    }
    foreach ($params['result']['rows'] as $key => $value) {
      $params = $value;
      $rs[$key]['process_id'] = $params['item_instance']['process_id'];
      $rs[$key]['process_instance_id'] = $params['item_instance']['process_instance_id'];
      $rs[$key]['process_title'] = $params['item_instance']['title'];
      $rs[$key]['user_id'] = $params['item_instance']['user_id'];
      $rs[$key]['role_id'] = $params['item']['role_id'];
      $rs[$key]['csr_id'] = $csr_id;
      $rs[$key]['item_id'] = $params['item_instance']['item_id'];
      $rs[$key]['item_instance_id'] = $params['item_instance']['item_instance_id'];
      $rs[$key]['task_title'] = $params['item']['title'];
    }
    return $rs;
  }
  //获取引擎中的列表
  public function getworkflowlist($params, $condition)
  {
    $process_id = '2';
    $rs = $this->_common->object_array(json_decode($this->_task_instance->listsjcr($params, $params['page'], $params['size'], json_encode($condition), $process_id)));
    if($rs['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    return $rs['result'];
  }
  //拾取任务
  public function pickup($user_id, $params)
  {
    $pickup = $this->_common->object_array(json_decode($this->_user_task->pickup($user_id, $params['item_instance_id'])));  //引擎中的认领任务
    if($pickup['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    //检验该任务实例是否已存在
    $issettask = $this->_jcr_task->issettask($params['item_instance_id']);
    if (!empty($issettask)) {
      //如果已存在，则直接更新状态为1，不存在则往下走
      $rs = $this->_jcr_task->picktask('1', $params['item_instance_id'], $user_id);  
    } else {
      $param = $this->formatparamspickup($pickup, $params['csr_id']);
      $rs = $this->_jcr_task->pickupTask($param);
    }
    if ($rs !== false) {
      $rs = true;
    }
    return $rs;
  }
  //格式化传入参数
  public function formatparamspickup($params, $csr_id)
  {
    if ($params['error_no'] !== 200) {
      return false;
    }
    $params = $params['result'];
    $rs[0]['process_id'] = $params['item_instance']['process_id'];
    $rs[0]['process_instance_id'] = $params['item_instance']['process_instance_id'];
    $rs[0]['process_title'] = $params['item_instance']['title'];
    $rs[0]['user_id'] = $params['item_instance']['user_id'];
    $rs[0]['role_id'] = $params['item']['role_id'];
    $rs[0]['csr_id'] = $csr_id;
    $rs[0]['item_id'] = $params['item_instance']['item_id'];
    $rs[0]['item_instance_id'] = $params['item_instance']['item_instance_id'];
    $rs[0]['task_title'] = $params['item']['title'];
    return $rs;
  }
  //完成任务  user_id  用户id, task_instance_id   任务实例id.   status  任务的状态，1为通过，2为拒绝，3为待补件
  public function completeworkflow($user_id, $task_instance_id, $data, $msg='')
  { 
    $check = $this->_jcr_task->issettask($task_instance_id);   //检验该任务是否存在
    if (empty($check)) {
      return false;
    }
    $complete = $this->_common->object_array(json_decode($this->_user_task->complete($user_id, $task_instance_id, json_encode($data))));
    if($complete['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    $params['task_instance_id'] = $task_instance_id;
    $params['status'] = 2;//状态，1为认领了这个任务，2为完成了这个任务，3为放弃了这个任务
    $params['task_status'] = $data['task_status'];   //用于本地记录的status
    $params['csr_id'] = $data['csr_id'];
    $params['msg'] = $msg;
    $createProcess = $this->_jcr_task->completetask($params);
    if ($createProcess == false) {
      return false;
    }
    return $params;
  }
}

