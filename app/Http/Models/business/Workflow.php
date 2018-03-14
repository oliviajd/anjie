<?php

namespace App\Http\Models\business;

use App\Http\Models\table\Anjie_push_log;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\table\Anjie_process;
use App\Http\Models\table\Anjie_task;
use App\Http\Models\table\V1_user_role;
use App\Http\Models\table\Anjie_work;
use App\Http\Models\table\Anjie_visit;
use App\Http\Models\table\Anjie_visit_message;
use App\Http\Models\table\Anjie_visit_back;
require base_path('app/Http/Models/common/WorkFlow.php');
use App\Http\Models\common\ProcessInstance;
use App\Http\Models\common\TaskInstance;
use App\Http\Models\common\UserTask;
use App\Http\Models\table\Anjie_file;
use App\Http\Models\table\Anjie_users;
use App\Http\Models\business\Role;
use App\Http\Models\business\Auth;
use App\Http\Models\table\Anjie_task_item;
use App\Http\Models\table\Anjie_user_role_area_privilege;

class Workflow extends Model
{
  protected $_process_instance = null;
  protected $_task_instance = null;
  protected $_user_task = null;
  protected $_anjie_task = null;
  protected $_anjie_process = null;
  protected $_anjie_file = null;
  protected $_anjie_users = null;
  protected $_role = null;
  protected $_anjie_task_item = null;
  public function __construct()
  {
    parent::__construct();
    $this->_process_instance = new ProcessInstance();
    $this->_task_instance = new TaskInstance();
    $this->_user_task = new UserTask();
    $this->_anjie_task = new Anjie_task();
    $this->_anjie_process = new Anjie_process();
    $this->_v1_user_role = new V1_user_role();
    $this->_anjie_work = new Anjie_work();
    $this->_anjie_visit = new Anjie_visit();
    $this->_anjie_visit_message = new Anjie_visit_message();
    $this->_anjie_visit_back = new Anjie_visit_back();
    $this->_anjie_file = new Anjie_file();
    $this->_anjie_users = new Anjie_users();
    $this->_role = new Role();
    $this->_auth = new Auth();
    $this->_anjie_task_item = new Anjie_task_item();
    $this->_anjie_user_role_area_privilege = new Anjie_user_role_area_privilege();

  }
//查询家访任务列表  from anjie_visit as a, anjie_work as b
  public function lists($params, $user_id)
  {
  	$limitout = '';
  	if ($params['page'] && $params['size']) {
        $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
      }
  	if (($params['type'] == '6' || $params['type'] == '7') && $params['haschild'] == '2') {    //1为有下属，2为无下属
  		return $this->_common->output(false, Constant::ERR_FAILED_NO, '没有下属就不能查待分派和已分派');   //如果没有下属就不能查待分派和已分派
  	}
    $where_haspickup =  " where a.to_user_id = ". $user_id . " and a.has_pickup=1 and a.has_assign=2 and a.visit_status=2 and a.is_valid =1 and pick_up_userid = ". $user_id;   //已认领的条件
  	//查询类型
  	if ($params['type'] == '0') {   //查全部  
  		$where = " where a.to_user_id = ". $user_id . " and  a.visit_status!=4 and a.has_pickup=1 and a.has_assign=2  and a.is_valid =1";       //查询自己的全部
  	} else if ($params['type'] == '1') {   //查待认领 
  		$where = " where a.to_user_id = ". $user_id . " and a.has_pickup=2 and a.has_assign=2 and a.visit_status=2  and a.is_valid =1";    //查询未认领且未分配
  	} else if ($params['type'] == '2') {   //查已认领    
             //查询已认领且未分派（未分派则为自己认领的所有）
      $where = $where_haspickup;
  	} else if ($params['type'] == '3') {   //查待家访  
  		$where = " where a.to_user_id = ". $user_id . " and a.visit_status=2 and a.has_pickup=1 and a.has_assign=2 and a.supplement_status=1  and a.is_valid =1 and pick_up_userid = ". $user_id;       //待家访状态=2
  	} else if ($params['type'] == '4') {   //查待补件  
  		$where = " where a.to_user_id = ". $user_id . " and a.supplement_status=2 and a.has_pickup=1 and a.has_assign=2 and a.visit_status=3  and a.is_valid =1";  //待补件状态=2
  	} else if ($params['type'] == '5') {   //查历史件  
  		$where = " where a.to_user_id = ". $user_id . " and a.supplement_status in (1,3) and a.visit_status=3 and a.has_pickup=1 and a.has_assign=2  and a.is_valid =1";   //无需补件或已补件且已家访
  	} else if ($params['type'] == '6') {   //查待分派  
  		$where = " where a.to_user_id = ". $user_id . " and a.has_pickup=2 and a.has_assign=2 and a.visit_status=2  and a.is_valid =1";    //查询未认领且未分配
  	} else if ($params['type'] == '7') {   //查已分派   
  		$where = " where a.to_user_id = ". $user_id . " and a.has_assign=1  and a.is_valid =1";   //已分派=1  (包括待家访、已家访、拒件的所有件)
  	} else {
  		return $this->_common->output(false, Constant::ERR_FAILED_NO, '提交类型不对');                  //提交类型不对
  	}
  	$where_type=$where;  //type定义的where条件
  	//日期
    $where_date = '';
  	if ($params['date'] !== '0') {
      $where_date = " and a.visit_date = '". $params['date'] . "'";    //如果日期字段不为空，则查询家访日期为传入日期的件
  		// $where = $where . $where_date;
  	}
    //根据车辆类型进行筛选
    $where_carclass='';
    if ($params['product_class_number'] !== '0') {   //车辆类型
      $where_carclass = " and b.product_class_number = '" . $params['product_class_number'] . "'" ;
      $where = $where . $where_carclass;
    }
    //根据关键词进行筛选
    $where_keyword='';
    if ($params['keyword'] !== '') {
      $where_keyword = " and (b.customer_name like '%" . $params['keyword'] . "%' or b.customer_telephone like '%" . $params['keyword'] . "%' or b.request_no like '%" . $params['keyword'] . "%') ";
      $where = $where . $where_keyword;
    }
    //联表的条件
    if ($where !== '') {
      $where_id = ' and a.work_id = b.id';
      $where = $where . $where_id;
    }
    //家访列表分区
    $visitor_info = $this->_anjie_users->getInfoById($user_id);
    $citys = $this->_anjie_user_role_area_privilege->getCityLists($user_id,'98');
    if (!empty($citys)) {//如果定义了城市权限，以权限为准
        $where_partition = " and a.credit_city in ('" . implode('\',\'',array_column($citys,'city_name')) . "')";
    } else {
        $where_partition = " and a.credit_city = '" . $visitor_info['city'] . "'";
    }
    $where = $where . $where_partition;
    $order = ' order by a.' . $params['order'] . ' desc';
    $rs['rows'] = $this->_anjie_work->getAppDeatail($order, $limitout, $where);//得到该条件下的列表   联表查询
    $wheretoday = $where_haspickup . ' and a.visit_date = '.date("Ymd"). $where_carclass . $where_keyword . $where_id; //今天的已认领
    $rs['todaycount'] = $this->_anjie_work->getAppCount($wheretoday);//所查日期的数量
    $rs['count'] = $this->_anjie_work->getAppCount($where);//所查日期的数量
    $lastwhere = $where_haspickup  . ' and a.visit_date = '. date("Ymd",(time() - 3600*24)) . $where_carclass . $where_keyword . $where_id; //上一天的已认领
    $nextwhere = $where_haspickup  . ' and a.visit_date = '. date("Ymd",(time()  + 3600*24)) . $where_carclass . $where_keyword.$where_id; //下一天的已认领
    $rs['lastdatecount'] = $this->_anjie_work->getAppCount($lastwhere); //上一天的数量
    $rs['nextdatecount'] = $this->_anjie_work->getAppCount($nextwhere);//下一天的数量
    if ($params['type'] == '7') {
      foreach ($rs['rows'] as $key => $value) {
        $rs['rows'][$key]['subordinate'] = $this->_anjie_users->getsubordinate($rs['rows'][$key]['to_user_id'], $rs['rows'][$key]['work_id']);
      }
    }
    return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
  }

  //根据id获取信息
  public function getworkinfobyid($id)
  {
  	$rs = $this->_anjie_work->getdetailByid($id);
    $param = array(
      'work_id' => $id,
      'file_class_id' => '7',  //公司家访视频
    );
    $rs['company_visit_video'] = $this->_anjie_file->getInfoByWorkidAndFileclassid($param);
    $rs['company_visit_video_count'] = count($this->_anjie_file->getInfoByWorkidAndFileclassid($param));
    $param = array(
      'work_id' => $id,
      'file_class_id' => '8',  //家里家访视频
    );
    $rs['home_visit_video'] = $this->_anjie_file->getInfoByWorkidAndFileclassid($param);
    $rs['home_visit_video_count'] = count($this->_anjie_file->getInfoByWorkidAndFileclassid($param));
    $where = "where work_id = " . $id . ' and file_type = 1 and status =1' ;
    $data_collection = $this->_anjie_file->getDetail($where);
    $rs['data_collection_count'] = count($data_collection);
    if (!empty($data_collection)) {
      $rs['data_collection'] = true;   //已经完成采集
    } else {
      $rs['data_collection'] = false;  //未完成采集
    }
  	return $rs;
  }
  //引擎中的家访处理逻辑
  public function workflowvisit($params)
  {
    $visit_role_id = env('VISIT_ROLE_ID');   //家访员角色id =14
    $visit_userids = $this->_v1_user_role->getuseridsByRoleid($visit_role_id); //所有家访员最高级的角色对应的userids
    $workinfo = $this->_anjie_work->getInfoByid($params['work_id']);  //获取工作详情
    if (empty($workinfo)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该件不存在');
    }
    $params['product_class_number'] = $workinfo['product_class_number'];
    $params['credit_city'] = $workinfo['credit_city'];
    $rs = $this->_anjie_visit->workflowvisit($params, $visit_userids);
    if ($rs !== false) {
      $rs = $this->_anjie_visit_message->insertmessage($visit_userids, $params);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
  }
  //引擎中的补件逻辑
  public function workflowsupplement($params)
  {
    $where  = " where work_id = " . $params['work_id'] . " and has_pickup =1 and has_assign =2";    //如果不存在该件，则返回错误
    $visit_work = $this->_anjie_visit->getDetail($where, '', ' limit 1');
    if (empty($visit_work)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '无该待补件');            //无该待补件
    }
    $rs = $this->_anjie_visit->workflowsupplement($params);
    $pickup = $this->_common->object_array(json_decode($this->_user_task->pickup($visit_work[0]['pick_up_userid'], $params['task_instance_id'])));  //引擎中的认领任务(认领补件任务)
    //检验该任务实例是否已存在
    if ($pickup['error_no'] !== 200) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '补件任务认领失败');             //补件任务认领失败
    }

    $issettask = $this->_anjie_task->issettask($params['task_instance_id']);
    if (!empty($issettask)) {
      //如果已存在，则直接更新状态为1，不存在则往下走
      $rs = $this->_anjie_task->updatetask('1', $params['task_instance_id']);
    } else {
      $param = $this->formatparamspickup($pickup, $params['work_id']);
      $rs = $this->_anjie_task->pickupTask($param);
    }
    if ($rs == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '补件任务拾取失败');             //补件任务拾取失败
    }
    $params['user_id'] = $visit_work[0]['pick_up_userid'];
    if ($rs !== false) {
      $rs = $this->_anjie_visit_message->setsupplementmessage($params);    //如果不是主管，则推送给最后这个人
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
  }
  //格式化传入参数
  public function formatparams($params, $work_id)
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
	  	$rs[$key]['work_id'] = $work_id;
	  	$rs[$key]['item_id'] = $params['item_instance']['item_id'];
	  	$rs[$key]['item_instance_id'] = $params['item_instance']['item_instance_id'];
	  	$rs[$key]['task_title'] = $params['item']['title'];
  	}
  	return $rs;
  }
  //格式化传入参数
  public function formatparamspickup($params, $work_id)
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
    $rs[0]['work_id'] = $work_id;
    $rs[0]['item_id'] = $params['item_instance']['item_id'];
    $rs[0]['item_instance_id'] = $params['item_instance']['item_instance_id'];
    $rs[0]['task_title'] = $params['item']['title'];
    return $rs;
  }

  //获取已认领列表的数量
  public function getaccountlist($user_id, $params)
  {
    $where = " where to_user_id = ". $user_id . " and has_pickup=1 and has_assign=2  and visit_status=2";       //查询已认领且未分派（未分派则为自己认领的所有）
    if ($params['startdate'] !== '') {
      $where = $where . " and visit_date >=" . $params['startdate'];
    }
    if ($params['enddate'] !== '') {
      $where = $where . " and visit_date <=" . $params['enddate'];
    }
    $order = ' order by visit_date asc';
    $group = ' group by visit_date';
    $list = $this->_anjie_visit->getaccountlist($group, $order, $where);      //得到该条件下的列表
    return $list;
  }
  //完成家访任务  user_id  用户id, task_instance_id   任务实例id.   status  任务的状态，1为通过，2为拒绝，3为待补件
  public function complete($user_id, $task_instance_id, $data, $msg='')
  {	
    $check = $this->_anjie_task->issettask($task_instance_id);   //检验该任务是否存在
  	$complete = $this->_common->object_array(json_decode($this->_user_task->complete($user_id, $task_instance_id, json_encode($data))));
    if($complete['error_no'] != 200) {    //返回的是数字型的200
  		return false;
  	}
  	$params['task_instance_id'] = $task_instance_id;
  	$params['status'] = 2;//状态，1为认领了这个任务，2为完成了这个任务，3为放弃了这个任务
  	$params['task_status'] = $data['visit_status'];
    $workinfo = $this->_anjie_work->getdetailByid($data['work_id']);    //取工作详情
    $current_item_ids = $workinfo['current_item_id'];   //当前的任务字段
    $item_id_arr = explode(',', $current_item_ids);   //转换成数组
    foreach ($item_id_arr as $key => $value) {
      if ($value == '|'. $check['item_id']. '|') {
        unset($item_id_arr[$key]);     //剔除
      }
    }
    $params['current_item_id'] = implode(',', $item_id_arr);
    $params['work_id'] = $data['work_id'];
    $complet_work = $this->_anjie_work->workflowitem($params);    //anjie_work表中剔除已完成的
    $params['msg'] = $msg;
  	$createProcess = $this->_anjie_task->completetask($params);
  	if ($createProcess !== false) {
  		return $params;
  	} else {
  		return false;
  	}
  }
  //完成任务  user_id  用户id, task_instance_id   任务实例id.   status  任务的状态，1为通过，2为拒绝，3为待补件
  public function completeworkflow($user_id, $task_instance_id, $data, $msg='')
  { 
    $check = $this->_anjie_task->issettask($task_instance_id);   //检验该任务是否存在
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
    $workinfo = $this->_anjie_work->getdetailByid($data['work_id']);    //取工作详情
    $current_item_ids = $workinfo['current_item_id'];   //当前的任务字段
    $item_id_arr = explode(',', $current_item_ids);   //转换成数组
    foreach ($item_id_arr as $key => $value) {
      if ($value == '|' . $check['item_id'] . '|') {
        unset($item_id_arr[$key]);     //剔除
      }
    }
    $params['current_item_id'] = implode(',', $item_id_arr);
    $params['work_id'] = $data['work_id'];
    $complet_work = $this->_anjie_work->workflowitem($params);    //anjie_work表中剔除已完成的
    $params['msg'] = $msg;
    $createProcess = $this->_anjie_task->completetask($params);
    if ($createProcess == false) {
      return false;
    }
    $workComplete = $this->_anjie_task->workComplete($data['work_id']);  //查该件是否已完成
    if ($workComplete == true) {
      $completework = $this->_anjie_work->completework($data);
      if ($completework == false) {
        return false;
      }
    }
    return $params;
  }
  //家访中的认领任务逻辑
  public function pickupvisit($user_id, $params)
  {
    $haspickup = $this->_anjie_visit->haspickup($params);//验证该任务是否已经被领取
    if ($haspickup) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '任务已经被领取');
    }
    $visit = $this->_anjie_visit->pickupvisit($user_id, $params);  //在家访表中领取任务，并返回task_instance_id
    if ($visit == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该用户没有权限认领此件');
    }
    if (empty($visit)) {   //取不到则说明没有该家访任务
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '没有该家访任务');
    }
    $pickup = $this->_common->object_array(json_decode($this->_user_task->pickup($user_id, $visit['visit_task_instance_id'])));  //引擎中的认领任务
    if($pickup['error_no'] != 200) {    //返回的是数字型的200
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '任务领取失败');
    }
    //检验该任务实例是否已存在
    $issettask = $this->_anjie_task->issettask($visit['visit_task_instance_id']);
    if (!empty($issettask)) {
      //如果已存在，则直接更新状态为1，不存在则往下走
      $rs = $this->_anjie_task->updatetask('1', $visit['visit_task_instance_id'], $user_id);  
    } else {
      $param = $this->formatparamspickup($pickup, $params['work_id']);
      foreach ($param as $key => $value) {
        $param[$key]['user_id'] = $user_id;
      }
      $rs = $this->_anjie_task->pickupTask($param);
    }
    if ($rs !== false) {
      return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }
  }
  //拾取任务
  public function pickup($user_id, $params)
  {
    $pickup = $this->_common->object_array(json_decode($this->_user_task->pickup($user_id, $params['item_instance_id'])));  //引擎中的认领任务
    if($pickup['error_no'] != 200) {    //返回的是数字型的200
      return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
    }
    //检验该任务实例是否已存在
    $issettask = $this->_anjie_task->issettask($params['item_instance_id']);
    if (!empty($issettask)) {
      //如果已存在，则直接更新状态为1，不存在则往下走
      $rs = $this->_anjie_task->picktask('1', $params['item_instance_id'], $user_id);  
    } else {
      $param = $this->formatparamspickup($pickup, $params['work_id']);
      $rs = $this->_anjie_task->pickupTask($param);
    }
    if ($rs !== false) {
      return $this->_common->output(false, Constant::ERR_SUCCESS_NO, '认领成功');         //该任务已完成
    }
    return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
  }
  //丢弃任务
  public function giveup($user_id, $param)
  {
    $taskinfo = $this->_anjie_task->issettask($param['item_instance_id']);
    if ($taskinfo['work_id'] == $param['work_id'] && $taskinfo['status'] == '1') {
      $giveup = $this->_common->object_array(json_decode($this->_user_task->giveup($user_id, $param['item_instance_id']))); 
      if($giveup['error_no'] != 200) {    //返回的是数字型的200
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '丢弃任务失败');    //如果返回的是false
      }
      $rs = $this->_anjie_task->updatetask('4', $param['item_instance_id']);
      return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    } else {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '不存在需要丢弃的任务');    //如果返回的是false
    }
  }
  public function visitgiveup($user_id, $param)
  {
    $this->_pdo->beginTransaction();
    try {
      $workinfo = $this->_anjie_work->getInfoByid($param['work_id']);
      if (empty($workinfo)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请件不存在'); 
      }
      $checkpickup = $this->_anjie_visit->checkpickup($user_id, $param);
      if (empty($checkpickup)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有退回该件的权限！');
      }
      $param['haschild'] = $this->_auth->haschild($user_id); // 1为有下属，2为无下属
      $param['highest_charge'] = $this->_auth->highest_charge($user_id); // 1为一级主管，2不是一级主管
      //anjie_visit退件
      $checkfrompickup = $this->_anjie_visit->getinfoByuseridAndWorkid($user_id, $param['work_id']);
      if (empty($checkfrompickup)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '');
      }
      $param['from_user_id'] = $checkpickup['from_user_id'];
      $visitgiveup = $this->_anjie_visit->visitgiveup($user_id, $param);
      //工作流退件
      $params['item_instance_id'] = $checkpickup['visit_task_instance_id'];
      $params['work_id'] = $param['work_id'];
      $visitworkgiveup = $this->giveup($user_id, $params);
      $arr = $this->_common->object_array(json_decode($visitworkgiveup));
      if ($arr['error_no'] !== '200') {
        $this->_pdo->rollBack();
      }
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    return $visitworkgiveup;
  }

  public function editwork($params, $work_id)
  {
    $taskinfo = $this->_anjie_task->gettaskbyworkid($work_id);
    if (!empty($taskinfo)) {
      $editwork = $this->_common->object_array(json_decode($this->_user_task->edit($taskinfo['process_instance_id'], $taskinfo['process_id'], $params))); 
      if($editwork['error_no'] != 200) {    //返回的是数字型的200
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '修改申请件失败');    //如果返回的是false
      }
    }
    return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }

  //结束任务
  public function end($user_id, $param)
  {
    $end = $this->_common->object_array(json_decode($this->_process_instance->end($param['process_instance_id'])));
    if($end['error_no'] != 200) {    //返回的是数字型的200
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '结束任务失败');    //如果返回的是false
    }
    return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }
  //家访中的退回任务逻辑
  public function backvisit($user_id, $params)
  {
    //退回的前提是这个件的确是这个user_id的
    $check = $this->_anjie_visit->checkvisit($user_id, $params);
    if (empty($check) || $check['from_user_id'] == '0') {   //如果是一级主管则不能退回
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '一级主管不能退回');
    }
    $params['to_user_id'] = $check['from_user_id'];  //上一级主管的user_id
    $backvisit = $this->_anjie_visit->backvisit($user_id, $params);   //退回家访任务的逻辑
    $insertbackvisit = $this->_anjie_visit_back->insertback($user_id, $params);   //插入退回记录
    if (($backvisit == false) || ($insertbackvisit == false)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '退回家访任务失败');
    } else {
      return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }
  }
  //家访中的分派任务
  public function assignvisit($user_id, $params)
  {
    //验证这个件是否是这个人可以分派的
    $check = $this->_anjie_visit->checkassignvisit($user_id, $params); 
    if (empty($check)) {   //如果不是他的件则不能分派
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有分派该件的权限');
    } 
    //验证被分配的用户是否是这个人的下属
    $condition['userid'] = $user_id;
    $list = $this->_role->listsubordinate($condition, '', '', '');
    $subordinates = array_column($list, 'id');   //所有下属id
    if (!in_array($params['subordinate_userid'], $subordinates)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有分派该家访员的权限');
    }
    //分派的逻辑
    $params['credit_city'] = $check['credit_city'];
    $rs = $this->_anjie_visit->assignvisit($user_id,$params);   //把当前user_id对应的记录的has_assign=1，然后插入一条记录
    if ($rs == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '家访分派失败');
    }
    //如果该下属是最底层的家访员，则直接认领该任务
    if ($this->_auth->haschild($params['subordinate_userid']) == '2') {
      $params['visit_date'] = date('Ymd');
      $pickupvisit = $this->pickupvisit($params['subordinate_userid'], $params);
    }
    $params['task_instance_id'] = $check['visit_task_instance_id'];
    $params['user_id'] = $params['subordinate_userid'];
    $params['type'] = '1';   //为1表示家访
    $rs = $this->_anjie_visit_message->setsupplementmessage($params);   //被分派的时候插入消息提醒
    if ($rs == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '家访通知失败');
    }
    $work = $this->_anjie_work->getdetailByid($params['work_id']);
      $salesman = $this->_anjie_users->getInfoById($params['subordinate_userid']);
      // if(!empty($salesman)){
      //     $pushAction = 'allot';
      //     $sendData = json_encode(['work_id'=>$params['work_id'],'action'=>$pushAction,'url'=>'app/pushMessageCenter']);
      //     $push = $this->_common->curl_post(env('PUSH_URL').'message',['mobile_list'=>$salesman['account'],'data'=>$sendData,'app_name'=>env('PUSH_APP_NAME'),'title'=>'您有新的家访任务','content'=>'您有新的家访任务']);
      //     $anjiePushLogModel = new Anjie_push_log();
      //     $anjiePushLogModel->addPushLog(['mobile_list'=>$salesman['account'],'send_data'=>$sendData,'return_data'=>$push,'push_action'=>$pushAction,'user_id'=>$salesman['id'],'result'=>'您有新的家访任务','title'=>'您有新的家访任务']);
      //     Log::info('个推推送家访任务目标:'.$salesman['account'].'结果:'.$push);
      // }else{
      //     Log::info('个推推送家访任务时，不存在work_id='.$params['work_id'].'对应的家访人员数据');
      // }
    return $this->_common->output($params, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }
  //拒件家访任务
  public function refusevisit($user_id, $params)
  {
    //验证这个件是否可以被拒件，也就是是否是已认领里面的件，且未完成
    $check = $this->_anjie_visit->checkvisit($user_id, $params);
    if (empty($check)) {   //如果不是他的件则不能拒件
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有拒件该件的权限');
    }
    //本地修改visit_status=4
    $rs = $this->_anjie_visit->refusevisit($user_id, $params);
    $params['to_user_id'] = $user_id;   //拒件的时候，最后还是会回到自己
    $insertbackvisit = $this->_anjie_visit_back->insertback($user_id, $params);   //插入退回记录
    if ($rs == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '家访拒件失败');
    }
    //引擎中完成任务，并status=2，即拒绝
    $data = array(
      'visit_status' => '2',
    );
    $complete = $this->_common->object_array(json_decode($this->_user_task->complete($user_id, $check['visit_task_instance_id'], json_encode($data))));
    if($complete['error_no'] !== 200) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '拒件任务失败');
    }
    return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }
  //完成补件任务
  public function completesupplement($user_id, $params)
  {
    //验证这个件是否是这个人的待补件
    $check = $this->_anjie_visit->checksupplement($user_id, $params);
    if (empty($check)) {   //如果不是他的件则不能完成补件
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有完成该件的权限');
    }
    //完成补件，即supplement_status=3
    $rs = $this->_anjie_visit->completesupplement($user_id, $params);
    if ($rs == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '家访完全补件失败');
    }
    //引擎中的完成补件任务
    $data = array(
      'work_id' => $params['work_id'],
      'supplement_visit' => '3',   //通过
      'task_status' => '1',
    );
    $rs = $this->completeworkflow($user_id, $check['supplement_task_instance_id'], $data);
    if ($rs == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '补件任务完成失败');
    }
    return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }
  //列出所有的消息，并将所有未读消息设置为已读
  public function listmessage($user_id, $params)
  {
    $limitout = '';
    if ($params['page'] && $params['size']) {
        $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
      }
    //列出当前所有的未读
    $rs = $this->_anjie_visit_message->listByuserid($user_id, $limitout);
    //更新所有的未读为已读
    $update = $this->_anjie_visit_message->updateByuserid($user_id);
    if ($update !== false) {
      return $rs;
    }
    return false;
  }
  //判断是否有未读
  public function not_read($user_id)
  {
    $not_read = false;    //已读完
    //列出当前所有的未读
    $rs = $this->_anjie_visit_message->listByuserid($user_id, '');
    if (!empty($rs)) {    //如果非空则说明没有读取完，也就是还有未读
      $not_read = true;
    }
    return $not_read;
  }
//获取引擎中的列表
  public function getworkflowlist($params, $condition)
  {
    $process_id = '1';
    $rs = $this->_common->object_array(json_decode($this->_task_instance->lists($params, $params['page'], $params['size'], json_encode($condition), $process_id)));
    if($rs['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    return $rs['result'];
  }
//引擎平台修改某个件的任务状态逻辑
  public function workflowitem($params)
  {
    $current_item_id = $params['current_item_id'];
    //验证是否有这条记录
    $where = " where a.id = " . $params['work_id'];
    $check = $this->_anjie_work->getdetail($where, '', '');
    if (empty($check)) {
      return false;
    }
    if ($check[0]['current_item_id'] !== '' && $check[0]['current_item_id'] !== null) {
      $params['current_item_id'] = $check[0]['current_item_id'] . ',|'. $params['current_item_id'].'|';
    } else {
      $params['current_item_id'] = '|'. $params['current_item_id'].'|';
    }
    $workflowitem = $this->_anjie_work->workflowitem($params);
    if ($workflowitem == false) {
      return false;
    }
    //检验该任务实例是否已存在
    Log::info('==========anjie_task进入脚本通知接口==========');
    $issettask = $this->_anjie_task->issettask($params['item_instance_id']);
    Log::info("检查anjie_task是否有数据[item_instance_id={$params['item_instance_id']}],结果:".json_encode($issettask));
    if (!empty($issettask)) {
      //如果已存在，则直接更新状态为1，不存在则往下走
        Log::info("anjie_task中存在数据，开始调用updatetask");
      $rs = $this->_anjie_task->updatetask('4', $params['item_instance_id']);
      if($rs){
          Log::info("anjie_task调用updatetask结果：成功!");
      }else{
          Log::info("anjie_task调用updatetask结果：失败!");
      }
    } else {
      $iteminfo = $this->_anjie_task_item->getShowByItemid($current_item_id);
      if (empty($iteminfo)) {
        return false;
      }
      $params['current_item_id'] = $current_item_id;
        Log::info("anjie_task中不存在数据，开始调用taskentry插入化数据");
      $rs = $this->_anjie_task->taskentry($params, $iteminfo);
      if ($rs == false) {
          Log::info("anjie_task插入数据结果：失败！");
        return false;
      }else{
          Log::info("anjie_task插入数据结果：成功！");
      }
    }
    if ($check[0]['salessupplement_status'] == '1' && ($current_item_id == '9' || $current_item_id =='37')) {
      $artificialdata['work_id'] = $params['work_id'];
      $artificialdata['item_id'] = $current_item_id;
      $artificial = $this->_anjie_task->getartificialtask($artificialdata);
      if (!empty($artificial)) {
        $user_id = $artificial['user_id'];
        $pickup = $this->pickup($user_id, $params);
        if ($pickup == false) {
          return false;         //该任务认领失败
        }
      }
    }
    Log::info('==========anjie_task脚本通知接口调用完成==========');
    return $workflowitem;
  }
//创建和开始进程
  public function createprocess($user_id, $params)
  {
    $creaters = $this->_common->object_array(json_decode($this->_process_instance->create($user_id, '1')));
    if($creaters['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    $data = array(
      'work_id' => $params['work_id'],
      'customer_telephone' => $params['customer_telephone'],
      'request_no' => $params['request_no'],
      'customer_name' => $params['customer_name'],
      'product_id' => $params['product_id'],
      'product_name' => $params['product_name'],
      'create_time' => $params['create_time'],
      'credit_user_id' => $user_id,
      'credit_city' => $params['credit_city'],
      'loan_bank' => $params['loan_bank'],
      'zhaohui_user_id' => $params['zhaohui_user_id'],
    );
    $startrs = $this->_common->object_array(json_decode($this->_process_instance->start($user_id, $creaters['result']['process_instance_id'], json_encode($data))));
    if($startrs['error_no'] != 200) {    //返回的是数字型的200
      return false;
    }
    $issettask = $this->_anjie_task->issettask($startrs['result']['rows'][0]['item_instance']['item_instance_id']);
    if (!empty($issettask)) {
      //如果已存在，则直接更新状态为1，不存在则往下走
      $rs = $this->_anjie_task->updatetask('1', $startrs['result']['rows'][0]['item_instance']['item_instance_id']);
    } else {
      $param = $this->formatparams($startrs, $params['work_id']);
      $pickuprs = $this->_anjie_task->pickupTask($param);
      $data['task_status'] = '1';
      $msg = '已提交';
      $rs = $this->completeworkflow($user_id, $startrs['result']['rows'][0]['item_instance']['item_instance_id'], $data, $msg);
    }
    if ($rs !== false) {
      $rs = true;
    }
    return $rs;
  }
  //APP里面的销售的征信列表查询接口
  public function listinquire($user_id, $params)
  {
    $params['start'] = intval(($params['page'] - 1) * $params['size']);  //开始的数据
    $params['end'] = $params['page'] * $params['size'];                  //结束的数据
    if ($params['type'] == '0') {
      $workflowlist = $this->listinquireall($user_id, $params);     //全部
    } elseif ($params['type'] == '1') {
      $workflowlist = $this->listnotinquire($user_id, $params);    //待征信
    } elseif ($params['type'] == '2') {
      $workflowlist = $this->listhasinquire($user_id, $params);    //已征信
    }
    if (empty($workflowlist)) {
      return $workflowlist;
    }
    $rs = array();
    $rs['rows'] = array();
    $workplatform = new Workplatform();
    foreach ($workflowlist['rows'] as $key => $value) {
      $rs['rows'][$key] = $this->_anjie_work->getInfoByid($value['fields']['work_id']); 
      $rs['rows'][$key]['item_instance_id'] = $value['item_instance_id'];
      $rs['rows'][$key]['type'] = $params['type'];
      $rs['rows'][$key]['has_completed'] = $value['has_completed']['id'];   //1已完成 ，2未完成
      $rs['rows'][$key]['flow'] = $workplatform->listtasks(['work_id'=>$value['fields']['work_id'],'process_id'=>'1']);
    }
    $rs['total'] = $workflowlist['total'];
    return $rs;
  }
  //查全部  待认领 +（ role_id=80  + user_id = user_id ）
  public function listinquireall($user_id, $params)
  {
    $param = $params;
    $param['page'] =  1;   //第几页
    $param['size'] =  1;  //每一页的数量
    $workflowlist_not_pickup = $this->not_pickup($user_id, $param);  //查待认领
    $workflowlist_has_pickup = $this->has_pickup($user_id, $param);  //经手过的件
    if ($workflowlist_not_pickup['total'] >= $param['end']) {    //则全部查第一种条件
      $rs = $this->not_pickup($user_id, $params);  //查待认领
    } elseif (($workflowlist_not_pickup['total'] <= $param['end']) && ($workflowlist_not_pickup['total'] >= $param['start'])) {
      $diff = $param['end'] - $workflowlist_not_pickup['total'];
      $rs_not_pickup = $this->not_pickup($user_id, $params);  //查待认领
      $param['page'] = 1;
      $param['size'] = intval($diff);
      $rs = $this->has_pickup($user_id, $param);
      $rs['rows'] = array_merge($rs_not_pickup['rows'], $rs['rows']);
    } else {     //经手过的件    全部查第二种
      $param['page'] = 1;
      $param['size'] = $param['end'] - $workflowlist_not_pickup['total']; 
      $rs = $this->has_pickup($user_id, $param);
      $rs['rows'] = array_slice($rs['rows'], $param['size']-$params['size'], $params['size']);
    }
    $rs['total'] = $workflowlist_not_pickup['total'] + $workflowlist_has_pickup['total'];
    return $rs;
  }
  //查待征信  == 待认领 + 待处理
  public function listnotinquire($user_id, $params)
  {
    $params['user_id'] = '';
    $params['role_id'] = $params['role_id'];
    $params['has_locked'] = '';
    $params['has_completed'] = 2;
    $params['has_stoped'] = '';
    $condition = array();
    $condition['credit_user_id'] = $user_id;
    if ($params['keyword'] == '') {
      $rs = $this->getworkflowlist($params, $condition);   //待认领的list
      return $rs;
    }
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      $condition['request_no'] = $params['keyword'];         //申请编号
    }
    $rs1 = $this->getworkflowlist($params, $condition);   //待认领的list
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      unset($condition['request_no']);
      $condition['customer_name'] = $params['keyword'];         //申请编号
    }
    $rs2 = $this->getworkflowlist($params, $condition);   //待认领的list
    $rs['total'] = $rs1['total'] + $rs2['total'];
    $rs['rows'] = array_merge($rs1['rows'], $rs2['rows']);
    return $rs;
  }
  //查已征信  role_id=80  + user_id = user_id + has_complete=1
  public function listhasinquire($user_id, $params)
  {
    $params['user_id'] = '';
    $params['role_id'] = $params['role_id'];
    $params['has_locked'] = '';
    $params['has_completed'] = 1;
    $params['has_stoped'] = '';
    $condition = array();
    $condition['credit_user_id'] = $user_id;
    if ($params['keyword'] == '') {
      $rs = $this->getworkflowlist($params, $condition);   //待认领的list
      return $rs;
    }
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      $condition['request_no'] = $params['keyword'];         //申请编号
    }
    $rs1 = $this->getworkflowlist($params, $condition);   //待认领的list
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      unset($condition['request_no']);
      $condition['customer_name'] = $params['keyword'];         //申请编号
    }
    $rs2 = $this->getworkflowlist($params, $condition);   //待认领的list
    $rs['total'] = $rs1['total'] + $rs2['total'];
    $rs['rows'] = array_merge($rs1['rows'], $rs2['rows']);
    return $rs;
  }

  //查待认领
  public function not_pickup($user_id, $params)
  {
    //查待认领
    $params['user_id'] = '';
    $params['role_id'] = $params['role_id'];
    $params['has_locked'] = 2;
    $params['has_completed'] = 2;
    $params['has_stoped'] = '';
    $condition = array();
    $condition['credit_user_id'] = $user_id;
    if ($params['keyword'] == '') {
      $rs = $this->getworkflowlist($params, $condition);   //待认领的list
      return $rs;
    }
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      $condition['request_no'] = $params['keyword'];         //申请编号
    }
    $rs1 = $this->getworkflowlist($params, $condition);   //待认领的list
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      unset($condition['request_no']);
      $condition['customer_name'] = $params['keyword'];         //申请编号
    }
    $rs2 = $this->getworkflowlist($params, $condition);   //待认领的list
    $rs['total'] = $rs1['total'] + $rs2['total'];
    $rs['rows'] = array_merge($rs1['rows'], $rs2['rows']);
    return $rs;
  }
  //经手过的件
  public function has_pickup($user_id, $params)
  {
    //查待认领
    $params['user_id'] = '';
    $params['role_id'] = $params['role_id'];
    $params['has_locked'] = '1';
    $params['has_completed'] = '';
    $params['has_stoped'] = '';
    $condition = array();
    $condition['credit_user_id'] = $user_id;
    if ($params['keyword'] == '') {
      $rs = $this->getworkflowlist($params, $condition);   //待认领的list
      return $rs;
    }
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      $condition['request_no'] = $params['keyword'];         //申请编号
    }
    $rs1 = $this->getworkflowlist($params, $condition);   //待认领的list
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      unset($condition['request_no']);
      $condition['customer_name'] = $params['keyword'];         //申请编号
    }
    $rs2 = $this->getworkflowlist($params, $condition);   //待认领的list
    $rs['total'] = $rs1['total'] + $rs2['total'];
    $rs['rows'] = array_merge($rs1['rows'], $rs2['rows']);
    return $rs;
  }

  //待处理的件
  public function not_inquire($user_id, $params)
  {
    //查待处理的件
    $params['user_id'] = '';
    $params['role_id'] = $params['role_id'];
    $params['has_locked'] = 1;
    $params['has_completed'] = 2;
    $params['has_stoped'] = '';
    $condition = array();
    $condition['credit_user_id'] = $user_id;
    if ($params['keyword'] == '') {
      $rs = $this->getworkflowlist($params, $condition);   //待认领的list
      return $rs;
    }
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      $condition['request_no'] = $params['keyword'];         //申请编号
    }
    $rs1 = $this->getworkflowlist($params, $condition);   //待认领的list
    if ($params['keyword'] !== '' && $params['keyword'] !== null) {
      unset($condition['request_no']);
      $condition['customer_name'] = $params['keyword'];         //申请编号
    }
    $rs2 = $this->getworkflowlist($params, $condition);   //待认领的list
    $rs['total'] = $rs1['total'] + $rs2['total'];
    $rs['rows'] = array_merge($rs1['rows'], $rs2['rows']);
    return $rs;
  }

  public function getrolelist()
  {
    $rolelist = $this->_anjie_task_item->getrolelist();
    return $rolelist;
  }


}

