<?php

namespace App\Http\Models\business;

use App\Http\Models\table\Anjie_push_log;
use App\Http\Models\table\V1_user_role;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Common;
use App\Http\Models\common\Zhaohuibank;
use App\Http\Models\common\Ficopost;
use App\Http\Models\common\Bairongpost;
use App\Http\Models\business\Workflow;
use App\Http\Models\business\File;
use App\Http\Models\business\Auth;
use App\Http\Models\table\Anjie_work;
use App\Http\Models\table\Anjie_users;
use App\Http\Models\table\Anjie_product;
use App\Http\Models\table\Anjie_file;
use App\Http\Models\table\Anjie_visit;
use App\Http\Models\table\Anjie_file_class;
use App\Http\Models\table\Anjie_task;
use App\Http\Models\table\Sequence_fseqno;
use App\Http\Models\table\Sequence_tradeno;
use App\Http\Models\table\Anjie_task_item;
use App\Http\Models\table\Anjie_service;
use App\Http\Models\table\Tb_yuqi;
use App\Http\Models\table\Tb_huankuan;
use App\Http\Models\table\Anjie_user_role_area_privilege;
use App\Http\Models\table\T_address_province;
use App\Http\Models\table\T_address_city;
use App\Http\Models\table\Anjie_query_userid;
use App\Http\Models\table\Credit_query;
use App\Http\Models\table\Credit_query_log;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;
use Mail;

class Workplatform extends Model
{
  protected $_workflow = null;
  protected $_file = null;
  protected $_anjie_business = null;
  protected $_anjie_users = null;
  protected $_anjie_product = null;
  protected $_anjie_file = null;
  protected $_anjie_visit = null;
  protected $_anjie_file_class = null;
  protected $_anjie_task = null;
  protected $_sequence_fseqno = null;
  protected $_anjie_task_item = null;
  protected $_anjie_service = null;
  protected $_anjie_push_log = null;
  protected $_artificial_refuse_reason = array(
    '1' => '客户否认申请',
    '2' => '非本人签名',
    '3' => '申请人主动取消申请',
    '4' => '黑名单',
    '5' => '人行征信有不良记录',
    '6' => '申请人不配合调查',
    '7' => '公安网信息有误',
    '8' => '无法联系申请人',
    '9' => '其他',
  );
  

  public function __construct()
  {
    parent::__construct();
    $this->_workflow = new Workflow();
    $this->_file = new File();
    $this->_auth = new Auth();
    $this->_common = new Common();
    $this->_ficopost = new Ficopost();
    $this->_anjie_work = new Anjie_work();
    $this->_anjie_users = new Anjie_users();
    $this->_anjie_product = new Anjie_product();
    $this->_anjie_file = new Anjie_file();
    $this->_anjie_visit = new Anjie_visit();
    $this->_anjie_file_class = new Anjie_file_class();
    $this->_anjie_task = new Anjie_task();
    $this->_sequence_fseqno = new Sequence_fseqno();
    $this->_sequence_tradeno = new Sequence_tradeno();
    $this->_anjie_task_item = new Anjie_task_item();
    $this->_anjie_service = new Anjie_service();
    $this->_tb_yuqi = new Tb_yuqi();
    $this->_tb_huankuan = new Tb_huankuan();
    $this->_bairongpost = new Bairongpost();
    $this->_anjie_push_log = new Anjie_push_log();
    $this->_anjie_user_role_area_privilege = new Anjie_user_role_area_privilege();
    $this->_t_address_province = new T_address_province();
    $this->_t_address_city = new T_address_city();
    $this->_anjie_query_userid = new Anjie_query_userid();
    $this->_common->setlog();
  }
  public function fixcurrentid()
  {
    $rs = $this->_anjie_work->getall();
    foreach ($rs as $key => $value) {
      $value['work_id'] = $value['id'];
      $workitem = $this->_anjie_work->workflowitem($value);
    }
  }
  //fico数据请求
  public function ficopost($work_id)
  {
    $workinfo = $this->_anjie_work->getdetailByid($work_id);
    //查看需要向fico发送数据请求
    if ($workinfo['need_new_request'] == '2') {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该件已经评分');
    }
    //请求fico数据
    $rs = $this->_common->object_array($this->_ficopost->ficoquery($workinfo));
    if (!empty($rs)) {
      //若查得评分
      if ($rs['@attributes']['retCode'] == '000') {     //请求成功
        $params['retCode'] = $rs['@attributes']['retCode'];
        $params['need_new_request'] = '2';
        $reason = explode(',', $rs['@attributes']['reason']);
        foreach ($reason as $key => $value) {
          if (isset($this->_ficopost->reason_code[$value])) {
            $reason[$key] = $this->_ficopost->reason_code[$value];
          }
        }
        $params['reason'] = implode('，', $reason);
        $params['reasonback'] = $rs['@attributes']['reason'];
        $params['score'] = $rs['@attributes']['score'];
        $params['scoreID'] = $rs['@attributes']['scoreID'];
        if (isset($rs['@attributes']['recAction'])) {
          $params['recAction'] = $rs['@attributes']['recAction'];
        } else {
          $params['recAction'] = '';
        }
      //若是评分系统未就绪或者评分系统忙，则需要重新请求
      }elseif($rs['@attributes']['retCode'] == '901' || $rs['@attributes']['retCode'] == '902' || $rs['@attributes']['retCode'] == '909'){  //系统未就绪或者系统忙
        $params['need_new_request'] = '1';
        $params['reasonback'] = '';
        $params['reason'] = '';
        $params['score'] = '';
        $params['scoreID'] = '';
        $params['recAction'] = '';
        $params['work_id'] = $work_id;
        $params['retCode'] = $rs['@attributes']['retCode'];
        $params['work_id'] = $work_id;
      } else {
        //未查得评分、运行错误则不需要再次请求
        $params['need_new_request'] = '2';
        $params['reasonback'] = '';
        $params['reason'] = '';
        $params['score'] = '';
        $params['scoreID'] = '';
        $params['recAction'] = '';
        $params['work_id'] = $work_id;
        $params['retCode'] = $rs['@attributes']['retCode'];
        $params['work_id'] = $work_id;
        $params['errMsg'] = $rs['@attributes']['errMsg'];
        if ($rs['@attributes']['retCode'] == '101' || $rs['@attributes']['retCode'] == '102' || $rs['@attributes']['retCode'] == '201' || $rs['@attributes']['retCode'] == '202' || $rs['@attributes']['retCode'] == '205' || $rs['@attributes']['retCode'] == '206' || $rs['@attributes']['retCode'] == '207' || $rs['@attributes']['retCode'] == '400') {   //运行异常
          $flag = Mail::send('common.ficoemail',['arr'=>$params],function($message){
              $to = 'jiangd@ifcar99.com';
              $message->to($to)->subject('fico数据运行错误');
          });
        }
      }
      //更新业务表
      $updateficodata = $this->_anjie_work->updateficodata($work_id, $params);
      return $this->_common->output($params, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    } else {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '请求失败');
    }
  }

  public function _bairongcredit($id_card, $tel, $name)
  {
    // $id_card = '610402198105070801';
    // $tel= '18629529790';
    // $name = '陈银屏';
    $rs = $this->_bairongpost->bairongcredit($id_card, $tel, $name);
    return $rs;
  }

  public function bairongcreditpost($work_id)
  {
    $workinfo = $this->_anjie_work->getdetailByid($work_id);
    if (empty($workinfo)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请件不存在');
    }
    if ($workinfo['bairong_new_request'] == '2') {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该申请件不需重复查询');
    }
    $bairongcredit = $this->_bairongcredit($workinfo['customer_certificate_number'], $workinfo['customer_telephone'], $workinfo['customer_name']);
    if ($bairongcredit['result'] == true) {
      $obj = $this->_common->object_array(json_decode($bairongcredit['obj']));
      if ($obj['code'] == '200000') {
        $bairong_new_request = '1';
      } else {
        $bairong_new_request = '2';
      }
      //bad是失信执行人。execut是执行人
      $obj['ex_bad1_name'] = isset($obj['ex_bad1_name']) ? $obj['ex_bad1_name'] : '';
      $obj['ex_bad1_casenum'] = isset($obj['ex_bad1_casenum']) ? $obj['ex_bad1_casenum'] : '';
      $obj['ex_bad1_court'] = isset($obj['ex_bad1_court']) ? $obj['ex_bad1_court'] : '';
      $obj['ex_bad1_time'] = isset($obj['ex_bad1_time']) ? substr($obj['ex_bad1_time'],0,10) : '';

      $obj['ex_execut1_name'] = isset($obj['ex_execut1_name']) ? $obj['ex_execut1_name'] : '';
      $obj['ex_execut1_casenum'] = isset($obj['ex_execut1_casenum']) ? $obj['ex_execut1_casenum'] : '';
      $obj['ex_execut1_court'] = isset($obj['ex_execut1_court']) ? $obj['ex_execut1_court'] : '';
      $obj['ex_execut1_time'] = isset($obj['ex_execut1_time']) ? substr($obj['ex_execut1_time'],0,10) : '';
      $obj['ex_execut1_money'] = isset($obj['ex_execut1_money']) ? $obj['ex_execut1_money'] : '';
      $obj['ex_execut1_statute'] = isset($obj['ex_execut1_statute']) ? $obj['ex_execut1_statute'] : '';
      $bairongrequest = $this->_anjie_work->bairongrequest($bairong_new_request, $obj, $work_id);
    } else {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '访问接口失败');
    }
    return $this->_common->output($bairongcredit, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }
  public function endwork($user_id, $params)
  {
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    if (empty($workinfo)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请件不存在！');
    }
    $taskinfo = $this->_anjie_task->gettaskbyworkid($params['work_id']);
    if (empty($taskinfo)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请件未进入审批流！');
    }
    $updateworkstatus = $this->_anjie_work->updateworkstatus('0', $params['work_id']);   //anjie_work逻辑删除
    if ($updateworkstatus == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除申请件失败');
    }
    $endwork = $this->_workflow->end($user_id, $taskinfo);  //结束任务
    return $endwork;
  }
  /**
   * 征信申请
   * @param page 页码
   * @param size 每一页的条数
   * @param condition 查询的条件
   * @return array 
   */
  public function creditrequestsubmit($user_id, $params)
  {
    if ($params['product_name'] == '新车') {
      $params['product_id'] = '1';
    } else {
      $params['product_id'] = '2';
    }
    $this->_pdo->beginTransaction();
    try {
      if ($params['bondsman_name'] == '') {
        $params['customer_has_bondsman'] = '2';
      } else {
        $params['customer_has_bondsman'] = '1';
      }
      if ($params['spouse_name'] !== '') {
        $params['customer_marital_status'] = '1';
      } else {
        $params['customer_marital_status'] = '';
      }
      $hasIdCard = $this->_anjie_work->hasIdCard($params['customer_certificate_number']);
      if (!empty($hasIdCard)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '处于审核状态中的客户不能重复申请');
      }
      $check_telephone_number = json_decode($this->_common->check_telephone_number($params['customer_telephone']));       //验证是否手机号码
      if ($check_telephone_number->error_no !== 200) {
        return json_encode($check_telephone_number);
      }
      $isIdCard = json_decode($this->_common->isIdCard($params['customer_certificate_number']));     //验证是否身份证号码
      if ($isIdCard->error_no !== 200) {
        return json_encode($isIdCard);
      }
      if (!isset($params['imgs']['1']) && !isset($params['imgs']['file_1'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '身份证照片必传');
      }
      if (!isset($params['imgs']['2']) && !isset($params['imgs']['file_2'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '授权书照片必传');
      }
      $params['customer_sex'] = $this->_common->get_xingbie($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      $params['customer_age'] = $this->_common->getAgeByID($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      //写入业务信息
      $salesinfo = $this->_anjie_users->getInfoById($user_id);  //业务员信息,暂时没有用到
      $productinfo = $this->_anjie_product->getInfoByid($params['product_id']);   //产品信息
      $params['product_class_number'] = $productinfo['product_class_number'];
      $params['merchant_class_number'] = 'HB';
      $params['customer_birthdate'] =  intval(substr($params['customer_certificate_number'],6,8));
      $requestrs = $this->_anjie_work->creditrequest($params, $salesinfo);    //写入业务申请，添加业务
      //插入图片信息
      $imagers = $this->addimage($params['imgs'], $requestrs['id'], $user_id);  //添加图片
      if ($imagers !== true) {
        return $imagers;
      }
      //整理各种编号
      $time = strtotime(date('Ymd'));   //今天凌晨的时间戳
      $count = $this->_anjie_work->getAccounttoday($requestrs['id'], $params['merchant_class_number'], $time);   //获取今天的第几笔记录
      $merchant_no = 'HB' . date('Ymd') . sprintf("%05d", $count['count']);      //商机编号
      $productcount = $this->_anjie_work->getAccountByproduct($params['product_id'], $requestrs['id']);    //获取该产品的第几笔
      $product_no = $productinfo['product_number']. sprintf("%05d", $productcount['count']);       //产品编号
      $productclasscount = $this->_anjie_work->getAccountByproductclass($params['product_class_number'], $requestrs['id']);    //获取该来源的第几笔
      $request_no = $productinfo['product_number'] . sprintf("%08d", $productcount['count']); //申请编号
      $result = $this->_anjie_work->setnumbers($merchant_no, $product_no, $request_no, $requestrs['id']);
      $rs = $this->_anjie_work->getdetailByid($requestrs['id']);
      $params['work_id'] = $requestrs['id'];
      $params['product_id'] = $rs['product_id'];
      $params['product_name'] = $rs['product_name'];
      $params['request_no'] = $rs['request_no'];
      $params['customer_telephone'] = $rs['customer_telephone'];
      $params['customer_name'] = $rs['customer_name'];
      $params['create_time'] = $rs['create_time'];
      $params['credit_city'] = $salesinfo['city'];
      $params['loan_bank'] = $rs['loan_bank'];
      $params['zhaohui_user_id'] = env('ZHAOHUI_USERID');
      $createprocess = $this->_workflow->createprocess($user_id, $params);
      if ($createprocess == false) {
        $this->_pdo->rollBack();
      }
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($result !== false) {
      $rs = $this->_anjie_work->getdetailByid($requestrs['id']);
        $userRoleModel = new V1_user_role();
        $visit_userids = $userRoleModel->getuseridsByRoleid(env('VISIT_ROLE_ID')); //所有家访员最高级的角色对应的userids
        if($visit_userids){
            $idList = implode(',',$visit_userids);
            $where = ' where id in('.$idList.') and province = "'.$salesinfo['province'].'" and city = "'.$salesinfo['city'].'"';
            $managerList = $this->_anjie_users->getdetail($where,'','');
            // if(!empty($managerList)){
            //     $phoneList = implode(',',array_column($managerList,'account'));
            //     $pushAction = 'submit';
            //     $sendData = json_encode(['work_id'=>$requestrs['id'],'action'=>$pushAction,'url'=>'app/pushMessageCenter']);
            //     $push = $this->_common->curl_post(env('PUSH_URL').'message',['mobile_list'=>$phoneList,'data'=>$sendData,'app_name'=>env('PUSH_APP_NAME'),'title'=>'林润审批征信申请','content'=>'您收到一条林润审批征信申请信息']);
            //     $this->_anjie_push_log->addPushLog(['mobile_list'=>$phoneList,'send_data'=>$sendData,'return_data'=>$push,'push_action'=>$pushAction,'user_id'=>0,'result'=>'您收到一条林润审批征信申请信息','title'=>'林润审批征信申请']);
            //     Log::info('征信申请提交信息，个推推送给家访主管目标:'.$phoneList.'结果:'.$push);
            // }else{
            //     Log::info('征信申请提交的时候没有查询到业务地区='.$salesinfo['province'].'-'.$salesinfo['city'].'的家纺主管');
            // }
        }
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '征信申请提交失败');
    }
  }
//传入imgs和work_id插入图片
  public function addimage($imgs, $work_id, $user_id)
  {
    $workinfo = $this->_anjie_work->getdetailByid($work_id);
    $file_type = $this->_anjie_file_class->listimagetype();  //文件类型
    $file_types = array();
    foreach ($file_type as $key => $value) {
      if ($value['id'] == '19' && $workinfo['loan_bank'] == '04') {
        $value['min_length'] = '1';
      }
      $file_types[$value['id']] = $value;
    }
    if (!is_array($imgs)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
    }
    foreach ($imgs as $key => $value) {
      if (!isset($value['source_lists']) || !isset($value['source_type'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
      }
      if (!is_array($value['source_lists'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
      }
      foreach ($value['source_lists'] as $k => $v) {
        if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
        }
      }
    }
    foreach ($imgs as $key => $value) {
      $keytrim = ltrim($key,'file_');
      if (!isset($file_types[$keytrim])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该文件类型不存在');                //该文件类型不存在
      }
      $param['file_class_id'] = $keytrim;
      $param['work_id'] = $work_id;
      $curren_length = $this->_anjie_file->countByWorkidAndFileclassid($param);
      $max_length = intval($file_types[$keytrim]['max_length']);  //该文件类型的最大上传数量
      $min_length = intval($file_types[$keytrim]['min_length']);  //该文件类型的最大上传数量
      $count = count($imgs[$key]['source_lists']) + intval($curren_length['count(*)']);    //该文件类型的上传数量
      if ($count > $max_length) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');                //文件数量超过最大上传张数
      }
      if ($count < $min_length) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');                  //文件数量小于最小上传张数
      }
    }
    $imagers= true;
    foreach ($imgs as $key => $value) {
      $keytrim = ltrim($key,'file_');
      foreach ($value['source_lists'] as $k => $v) {
        $param['add_userid'] = $user_id; //添加者的userid
        $param['file_type_name'] = $value['source_type'];
        $param['file_type'] =  ($param['file_type_name'] == 'image') ? '1' : '2';
        $param['file_class_id'] = $keytrim;
        $param['file_path'] = $v['org'];
        $param['file_id'] = $v['alt'];
        $param['work_id'] = $work_id;
        $imagers = $this->_anjie_file->addimage($param);  //添加图像逻辑
      }
    }
    if ($imagers == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加图片失败!'); 
    }
    return true;
  }
  //传入imgs和work_id删除图片
  public function deleteimage($imgs, $user_id)
  {
    $imagers= true;
    foreach ($imgs as $key => $value) {
      foreach ($value['source_lists'] as $k => $v) {
        if (isset($v['id'])) {
        $param['delete_userid'] = $user_id;
        $param['file_id'] = $v['id'];
        $imagers = $this->_anjie_file->deleteimage($param);  //删除图像逻辑
      }
    }
    }
    return $imagers;
  }
//写入合同编号
  public function setconstractno($params)
  {
    $rs = $this->_anjie_work->setconstractno($params);
    return $rs;
  }
  //写入拜访总结
  public function setvisitdescrip($params)
  {
    $rs = $this->_anjie_work->setvisitdescrip($params);
    return $rs;
  }
  //写入客户地址
  public function setcustomeraddress($params)
  {
    $rs = $this->_anjie_work->setcustomeraddress($params);
    return $rs;
  }

  /**
   * 设置家访员位置
   * @param lat 纬度
   * @param lng 经度
   * @param userid 用户ID
   */
  public function setPoint($param)
  {
    $this->_common->setlog();
    $param['address'] = $this->getAddress($param['lat'], $param['lng']);
    // $url = "http://api.map.baidu.com/geocoder/v2/?ak=t0s350uHhbl2eLG8FAlse5zmEjGqjHGY&location=".$param['lat'].",".$param['lng']."&output=json&pois=1";
    // $rs = $this->_common->curltest($url);
    // if ($rs['rinfo']['http_code'] !== 200) {
    //   $result['errorcode'] = 400;
    //   $result['errormsg'] = "请求百度地图失败";
    //   return $result;
    // }
    // $data = json_decode($rs['data']);
    // $param['address'] = $data->result->formatted_address;
    $rs = $this->_anjie_users->setLocation($param);
    if ($rs !== false) {
      return $this->_common->output($param, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      return $this->_common->output('', Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
    }
  }
  public function getAddress($lat, $lng)
  {
    $url = "http://api.map.baidu.com/geocoder/v2/?ak=t0s350uHhbl2eLG8FAlse5zmEjGqjHGY&location=".$lat.",".$lng."&output=json&pois=1";
    $rs = $this->_common->curltest($url);
    if ($rs['rinfo']['http_code'] !== 200) {
      $result['errorcode'] = 400;
      $result['errormsg'] = "请求百度地图失败";
      return $result;
    }
    $data = json_decode($rs['data']);
    $address = $data->result->formatted_address;
    return $address;
  }
  /**
   * 开始结束家访
   * @param type 1为开始，2为结束
   */
  public function beginendvisit($param, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $param['address'] = $this->getAddress($param['lat'], $param['lng']);
      if ($param['type'] == '1') {
        $visit = $this->_anjie_work->setbeginvisit($param, $user_id); //开始拜访
      } else {
        $check = $this->_workflow->getworkinfobyid($param['work_id']); //验证能否结束家访
        if ($check['visit_arrive_time'] == '') {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '没有开始家访不能结束家访');    //没有开始家访不能结束家访
        }
        if ($check['customer_address'] == '' ||  $check['data_collection'] == false) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '资料不全，不能结束家访');    //资料不全，不能结束家访
        }
        $visitinfo = $this->_anjie_visit->getInfoByworkid($param['work_id']);  //任务详情
        if (empty($visitinfo)) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '家访信息不存在');     //家访信息不存在 
        }
        $workinfo = $this->_anjie_work->getdetailByid($param['work_id']);
        $file_type = $this->_anjie_file_class->listimagetype();  //文件类型
        foreach ($file_type as $key => $value) {
          if ($value['id'] == '19' && $workinfo['loan_bank'] == '04') {
            $value['min_length'] = '1';
          }
          $param['file_class_id'] = $value['id'];
          $curren_length = $this->_anjie_file->countByWorkidAndFileclassid($param);
          $max_length = intval($value['max_length']);  //该文件类型的最大上传数量
          $min_length = intval($value['min_length']);  //该文件类型的最大上传数量
          $count = intval($curren_length['count(*)']);    //该文件类型的上传数量
          if ($count > $max_length) {
            $this->_pdo->rollBack();
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');    //文件数量超过最大上传张数
          }
          if ($count < $min_length) {
            $this->_pdo->rollBack();
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');    //文件数量小于最小上传张数
          }
        }
        $visit_task_instance_id = $visitinfo[0]['visit_task_instance_id'];
        $data = array(
          'visit_status' => '1',
          'work_id' => $param['work_id'],
        );
        $workinfo = $this->_anjie_work->getdetailByid($param['work_id']);
        $msg = '抵达地点：' . $workinfo['visit_arrive_address'] .'(' . date('Y-m-d H:i:s', intval($workinfo['visit_arrive_time'])) . ')';
        $msg = $msg . '<br/>离开地点：' . $param['address'] . '(' .date('Y-m-d H:i:s') . ')';
        $completevisit = $this->_workflow->complete($user_id, $visit_task_instance_id, $data, $msg);  //完成这个任务
        if ($completevisit == false) {  
          $this->_pdo->rollBack();
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '完成家访任务失败');          //完成家访任务失败
        }
        $visit = $this->_anjie_visit->completevisit($user_id, $param);   //家访表完成任务
        $visit = $this->_anjie_work->setendvisit($param, $user_id);   //结束拜访
      }
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($visit !== false) {
      return $this->_common->output($param, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }
    $this->_pdo->rollBack();
    return $this->_common->output(false, Constant::ERR_FAILED_NO, '结束家访失败');        //结束家访失败
  }

  public function getAppinfoById($work_id)
  {
    $rs = $this->_anjie_work->getAppinfoById($work_id);  //通过work_id获得app里面需要的详情信息
    return $rs;
  }
//获取征信查询的列表
  public function getinquirelist($params, $inputs)
  {
    $condition = array();
    $user_id = $params['user_id'];
    //1查待认领，2查待处理
    if ($params['type'] == '1') {
        $params['user_id'] = '';
        $params['role_id'] = $params['roleid'];
        $params['has_locked'] = 2;
        $params['has_completed'] = 2;
        $params['has_stoped'] = 2;
        $params['order'] = 'create_time';
        /*
        if ($params['role_id'] == 80) {
            //地区属性
            $userinfo = $this->_anjie_users->getInfoById($user_id);
            $province_code = $this->_t_address_province->getcodeByname($userinfo['province']);
            $citys = $this->_t_address_city->getcityBypcode($province_code['code']);
            if (!empty($citys)) {
                $condition['credit_city'] = array_column($citys,'name');
            }
        }
         * 
         */
        $citys = $this->_anjie_user_role_area_privilege->getCityLists($user_id,$params['role_id']);
        /*
         * 如果所有人使用城市权限，启用这段代码
         */
        //$condition['credit_city'] = !empty($citys) ? array_column($citys,'city_name') : '没有城市权限';
        /* 看情况启用这段代码
         * 
         */
        if (!empty($citys)) {//如果定义了城市权限，以权限为准
            $condition['credit_city'] = array_column($citys,'city_name');
        } else {
            $partition = $this->_anjie_task_item->getInfoByRoleid($params['role_id']);
            if (!empty($partition) && $partition['partition']== '1') {//如果流程分区，以用户自己的城市为准
              $userinfo = $this->_anjie_users->getInfoById($user_id);
              $condition['credit_city'] = $userinfo['city'];
            } else {//如果流程不分区，可设置全访问或者全不能访问
                //$condition['credit_city'] =  '没有城市权限';
            }
        }
    } elseif($params['type'] == '2') {
        $params['user_id'] = $params['user_id'];
        $params['role_id'] = $params['roleid'];
        $params['has_locked'] = 1;
        $params['has_completed'] = 2;
        $params['has_stoped'] = 2;
        $params['order'] = 'lock_time';
    }
    if (isset($inputs['request_no']) && $inputs['request_no'] !== '') {
      $condition['request_no'] = $inputs['request_no'];
    }
    if (isset($inputs['customer_name']) && $inputs['customer_name'] !== '') {
      $condition['customer_name'] = $inputs['customer_name'];
    }
    if (isset($inputs['product_name']) && $inputs['product_name'] !== '') {
      if ($inputs['product_name'] == '1') {
        $condition['product_name'] = '新车';
      } elseif($inputs['product_name'] == '2') {
        $condition['product_name'] = '二手车';
      }
    }
    if (isset($inputs['start_time']) && $inputs['start_time'] !== '') {
      $condition['start_time'] = $inputs['start_time'];
    }
    if (isset($inputs['end_time']) && $inputs['end_time'] !== '') {
      $condition['end_time'] = $inputs['end_time'];
    }
    // if ($params['credit_self'] == '1') {    //只能查自己
    //   $condition['credit_user_id'] = $user_id;
    // }
    /*
    $partition = $this->_anjie_task_item->getInfoByRoleid($params['role_id']);
    if (!empty($partition) && $partition['partition']== '1') {
      $userinfo = $this->_anjie_users->getInfoById($user_id);
      $condition['credit_city'] = $userinfo['city'];
    }
     * 
     */
    $workflowlist = $this->_workflow->getworkflowlist($params, $condition);
    if (empty($workflowlist)) {
      return $workflowlist;
    }
    $rs = array();
    $rs['rows'] = array();
    foreach ($workflowlist['rows'] as $key => $value) {
      $rs['rows'][$key] = $this->_anjie_work->getInfoByid($value['fields']['work_id']); 
      $rs['rows'][$key]['item_instance_id'] = $value['item_instance_id'];
      $rs['rows'][$key]['type'] = $params['type'];   
      $rs['rows'][$key]['receive_time'] = $value['receive_time'];  
      $province_code = $this->_t_address_city->getcodeByname($rs['rows'][$key]['credit_city']);
      $province_name = $this->_t_address_province->getProvinceByCode($province_code['provinceCode']);
      $rs['rows'][$key]['credit_province'] = $province_name['name'];
    }
    $rs['total'] = $workflowlist['total'];
    return $rs;
  }
  //申请件查询 
  public function taskquery($params, $inputs)
  {
    $limitout = '';
    if ($params['page'] && $params['size']) {
      $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
    }
    if (isset($inputs['province_name']) && $inputs['province_name'] !== '') {
        $province_code = $this->_t_address_province->getcodeByname($inputs['province_name']);
        if (!empty($province_code)) {
          $citys = $this->_t_address_city->getcityBypcode($province_code['code']);
          if (!empty($citys)) {
              $inputs['credit_city'] = array_column($citys,'name');
          }
        }
      }
      if (isset($inputs['city_name']) && $inputs['city_name'] !== '') {
        unset($inputs['credit_city']);
      }
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $where  = $where . ' and RecId is null';
    $order = ' order by a.create_time desc';
    $rs = $this->_anjie_work->getdetail($where, $order, $limitout);
    foreach ($rs as $key => $value) {
      $province_code = $this->_t_address_city->getcodeByname($value['credit_city']);
      $province_name = $this->_t_address_province->getProvinceByCode($province_code['provinceCode']);
      $rs[$key]['credit_province'] = $province_name['name'];
    }
    return $rs;
  }
//申请件查询的件数
  public function taskquerycount($inputs)
  {
    if (isset($inputs['province_name']) && $inputs['province_name'] !== '') {
        $province_code = $this->_t_address_province->getcodeByname($inputs['province_name']);
        if (!empty($province_code)) {
          $citys = $this->_t_address_city->getcityBypcode($province_code['code']);
          if (!empty($citys)) {
              $inputs['credit_city'] = array_column($citys,'name');
          }
        }
      }
      if (isset($inputs['city_name'])) {
        unset($inputs['credit_city']);
      }
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $where  = $where . ' and RecId is null';
    $rs = $this->_anjie_work->getCount($where);
    return $rs;
  }
  //申请件查询 
  public function taskquerypartition($params, $inputs)
  {
    $limitout = '';
    if ($params['page'] && $params['size']) {
      $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
    }
    $userinfo = $this->_anjie_users->getInfoById($params['user_id']);
    $inputs['credit_city'] = $userinfo['city'];
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $where  = $where . ' and RecId is null';
    $order = ' order by a.create_time desc';
    $rs = $this->_anjie_work->getdetail($where, $order, $limitout);
    foreach ($rs as $key => $value) {
      $province_code = $this->_t_address_city->getcodeByname($value['credit_city']);
      $province_name = $this->_t_address_province->getProvinceByCode($province_code['provinceCode']);
      $rs[$key]['credit_province'] = $province_name['name'];
    }
    return $rs;
  }
//申请件查询的件数
  public function taskquerypartitioncount($params, $inputs)
  {
    $userinfo = $this->_anjie_users->getInfoById($params['user_id']);
    $inputs['credit_city'] = $userinfo['city'];
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $where  = $where . ' and RecId is null';
    $rs = $this->_anjie_work->getCount($where);
    return $rs;
  }
   //申请件查询 
    public function taskqueryprovince($params, $inputs)
    {
      $limitout = '';
      if ($params['page'] && $params['size']) {
        $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
      }
      $userinfo = $this->_anjie_users->getInfoById($params['user_id']);
      
      $province_code = $this->_t_address_province->getcodeByname($userinfo['province']);
      $citys = $this->_t_address_city->getcityBypcode($province_code['code']);
      if (!empty($citys)) {
          $inputs['credit_city'] = array_column($citys,'name');
      }
      $query_arrays = $this->_anjie_query_userid->getallbyuserid($params['user_id']); 
      if (!empty($query_arrays)) {
        $cityslist = array();
        foreach ($query_arrays as $key => $value) {
          if ($value['type'] == '1') {
            $province_code = $this->_t_address_province->getcodeByname($value['name']);
            $citys = $this->_t_address_city->getcityBypcode($province_code['code']);
            $citys_column = array_column($citys,'name');
            $cityslist = array_merge($cityslist, $citys_column);
          } else {
            $cityslist[] = $value['name'];
          }
        }
        $inputs['credit_city'] = $cityslist;
      }
      $where = $this->condition_taskquery($inputs);  //获得where条件
      $where  = $where . ' and RecId is null';
      $order = ' order by a.create_time desc';
      $rs = $this->_anjie_work->getdetail($where, $order, $limitout);
      foreach ($rs as $key => $value) {
        $province_code = $this->_t_address_city->getcodeByname($value['credit_city']);
        $province_name = $this->_t_address_province->getProvinceByCode($province_code['provinceCode']);
        $rs[$key]['credit_province'] = $province_name['name'];
      }
      return $rs;
    }
  //申请件查询的件数
    public function taskqueryprovincecount($params, $inputs)
    {
      $userinfo = $this->_anjie_users->getInfoById($params['user_id']);
      $province_code = $this->_t_address_province->getcodeByname($userinfo['province']);
      $citys = $this->_t_address_city->getcityBypcode($province_code['code']);
      if (!empty($citys)) {
          $inputs['credit_city'] = array_column($citys,'name');
      }
      $where = $this->condition_taskquery($inputs);  //获得where条件
      $where  = $where . ' and RecId is null';
      $rs = $this->_anjie_work->getCount($where);
      return $rs;
    }
  //老系统数据 
  public function beforedata($params, $inputs)
  {
    $limitout = '';
    if ($params['page'] && $params['size']) {
      $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
    }
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $where  = $where . ' and RecId is not null';
    $order = ' order by a.create_time desc';
    $rs = $this->_anjie_work->getdetail($where, $order, $limitout);
    return $rs;
  }
//老系统数据
  public function beforedatacount($params, $inputs)
  {
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $where  = $where . ' and RecId is not null';
    $rs = $this->_anjie_work->getCount($where);
    return $rs;
  }
   //申请件查询 查自己的件
  public function taskqueryself($params, $inputs)
  {
    $limitout = '';
    if ($params['page'] && $params['size']) {
      $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
    }
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $workid = $this->_anjie_task->getDistinctWorkidsByUserid($params['user_id']);  //获取工作id的数组
    $workids = array_column($workid, 'work_id');
    $workidsarr = "'".implode("','", $workids)."'";
    $where = $where . " and a.id in (".$workidsarr.")";
    $where  = $where . ' and RecId is null';
    $order = ' order by a.create_time desc';
    $rs = $this->_anjie_work->getdetail($where, $order, $limitout);
    foreach ($rs as $key => $value) {
      $province_code = $this->_t_address_city->getcodeByname($value['credit_city']);
      $province_name = $this->_t_address_province->getProvinceByCode($province_code['provinceCode']);
      $rs[$key]['credit_province'] = $province_name['name'];
    }
    return $rs;
  }
//申请件查询的件数   查自己的件
  public function taskqueryselfcount($params, $inputs)
  {
    $where = $this->condition_taskquery($inputs);  //获得where条件
    $workid = $this->_anjie_task->getDistinctWorkidsByUserid($params['user_id']);  //获取工作id的数组
    $workids = array_column($workid, 'work_id');
    $workidsarr = "'".implode("','", $workids)."'";
    $where = $where . " and a.id in (".$workidsarr.")";
    $where  = $where . ' and RecId is null';
    $rs = $this->_anjie_work->getCount($where);
    return $rs;
  }

//查询的条件
  public function condition_taskquery($inputs)
  {
    $where = ' where a.status=1 and a.request_no is not null';
    if (isset($inputs['request_no']) && $inputs['request_no'] !== '') {
      $where = $where . " and a.request_no like '%". $inputs['request_no']. "%'";   //申请编号
    }
    if (isset($inputs['merchant_no']) && $inputs['merchant_no'] !== '') {
      $where = $where . "  and a.merchant_no ='" . $inputs['merchant_no']. "'";   //商户编号
    }
    if (isset($inputs['customer_name']) && $inputs['customer_name'] !== '') {
      $where = $where . "  and a.customer_name like '%" . $inputs['customer_name']. "%'";   //客户姓名
    }
    if (isset($inputs['customer_certificate_number']) && $inputs['customer_certificate_number'] !== '') {
      $where = $where . "  and a.customer_certificate_number ='" . $inputs['customer_certificate_number']. "'";   //身份证号码
    }
    if (isset($inputs['product_name']) && $inputs['product_name'] !== '') {
      $where = $where . "  and a.product_id ='" . $inputs['product_name']. "'";   //产品名称
    }
    if (isset($inputs['loan_date']) && $inputs['loan_date'] !== '') {
      $where = $where . "  and a.loan_date ='" . $inputs['loan_date']. "'";   //贷款期数
    }
    if (isset($inputs['loan_prize']) && $inputs['loan_prize'] !== '') {
      $where = $where . "  and a.loan_prize ='" . $inputs['loan_prize']. "'";   //贷款申请金额
    }
    if (isset($inputs['current_item_id']) && $inputs['current_item_id'] !== '') {
      $where = $where . "  and a.current_item_id like '%" . $inputs['current_item_id']. "%'";   //v1_process_item里面的id
    }
    if (isset($inputs['item_status']) && $inputs['item_status'] !== '') {
      $where = $where . "  and a.item_status ='" . $inputs['item_status']. "'";   //状态。1为审核中，2为已完成
    }
    if (isset($inputs['start_time']) && $inputs['start_time'] !== '' && isset($inputs['end_time']) && $inputs['end_time'] !== '') {
      $where = $where . "  and a.create_time >='" . $inputs['start_time'] . "' && a.create_time <= '" . $inputs['end_time']. "'";   //进件时间
    }
    if (isset($inputs['credit_city']) && $inputs['credit_city'] !== '') {
      if (is_array($inputs['credit_city'])) {
        $credit_city = "'".implode("','", $inputs['credit_city'])."'";
        $where = $where . "  and a.credit_city in(".$credit_city.")";   //销售人员的城市 else {}
      } else {
        $where = $where . "  and a.credit_city ='" . $inputs['credit_city']. "'";   //销售人员的城市
      }
      
    }
    if (isset($inputs['city_name'])  && $inputs['city_name'] !== '') {
      $where = $where . " and a.credit_city like '%" . $inputs['city_name'] . "%'";
    }
    return $where;
  }
  /**
   * 获取申请件信息
   * @param work_id 工作id
   */
  public function getInfoById($params)
  {
    $rs = $this->_anjie_work->getdetailByid($params['work_id']);
    if (!empty($rs)) {
      $visitorinfo = $this->_anjie_users->getInfoById($rs['visitor_id']);
      $salesmaninfo = $this->_anjie_users->getInfoById($rs['salesman_id']);
      $rs['salesman_name'] = empty($salesmaninfo) ? '' : $salesmaninfo['name'];
      $rs['salesman_city'] = empty($salesmaninfo) ? '' : $salesmaninfo['city'];
      $rs['visitor_name'] = empty($visitorinfo) ? '' : $visitorinfo['name'];
    } else {
      return array();
    }
    return $rs;
  }
  /**
   * 认领征信件
   * @param work_id 工作id
   */
  public function pickup($user_id, $params)
  {
    $issettask = $this->_anjie_task->issettask($params['item_instance_id']);
    if (empty($issettask)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该申请件暂不可认领，请稍候重试！');         //该任务已完成
    }
    $rs = $this->_workflow->pickup($user_id, $params);   //拾取任务
    return $rs;
  }
  /**
   * 工行认领和推送
   * @param work_id 工作id
   */
  public function bankpickupandpush($user_id, $params)
  {
    $this->_zhaohuibank = new Zhaohuibank();
    //下载word文档
    $wordcreate = $this->_file->bankwordcreate($params, $user_id);
    //上传word文档
    $putfile = $this->_zhaohuibank->putword($wordcreate);
    if ($putfile !== true) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, 'word文档上传失败'); 
    }
    //银行征信查询申请提交
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    $workinfo['word_filename'] = $wordcreate['filename']; 
    $bank = $this->_zhaohuibank->applyCredit($workinfo);
    //工作流完成该任务
    $issettask = $this->_anjie_task->issettask($params['item_instance_id']);
    if (empty($issettask)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该申请件暂不可认领，请稍候重试！');         //该任务已完成
    }
    $rs = $this->_workflow->pickup($user_id, $params);   //拾取任务
    return $rs;
  }
  public function creditquerylog()
  {
    $this->_credit_query_log = new Credit_query_log();
    $last_query_id = $this->_credit_query_log->getlast();
    $where = ' where id > ' . $last_query_id['lastqueryid'];
    $this->_credit_query = new Credit_query();
    $rs = $this->_credit_query->getdata($where);
    foreach ($rs as $key => $value) {
      // if ($value['result'] == '001') {
        //根据work_id获取task_item_id
        $where = " where item_id = 60 and work_id= '" . $value['orderno'] . "'";
        $taskinfo = $this->_anjie_task->getdetail($where);
        $updateworkcredit = $this->_anjie_work->updateworkcredit($value);
        $params['inquire_result'] = '1';
        $params['inquire_description'] = '';
        $params['inquire_status'] = '1';
        $params['work_id'] = $value['orderno'];
        $updatework = $this->_anjie_work->inquire($params);
        if (!empty($taskinfo)) {
          $data = array();
          $msg = '';
          $completecredittask = $this->_workflow->complete(env('ZHAOHUI_USERID'), $taskinfo['task_instance_id'], $data, $msg);  //完成这个任务
        }
      // }
      $updatecreditlog = $this->_credit_query_log->updatelog($value['id']);
    }
    return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
  }
  //数据发送到朝晖支行
  public function transtozhaohuibank($params)
  {
    $this->_zhaohuibank = new Zhaohuibank();
    //获取申请件详情信息
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    if ($workinfo['loan_bank'] !== '04') {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该申请件不是杭州朝晖支行的');         //该任务已完成
    }
//通用业务
    $pictures = $this->_anjie_file->getzhaohuibankimages($params['work_id']);
    foreach ($pictures as $key => $value) {
      $file_name = explode('/', $value['file_path']);
      $cnt = count($file_name);
      $pictures[$key]['real_file_name'] = $file_name[$cnt-1];
    }
    $putpictures = $this->_zhaohuibank->putfile($pictures);
    if ($putpictures == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件传送失败');         //该任务已完成
    }
    $workinfo['pictures'] = array();
    foreach ($pictures as $key => $value) {
      $filearr['picid'] = $value['zhaohui_bank_catagory'];
      $filearr['picurl'] = env('ZHAOHUIBANK_FILEPATH') . '?imageName=' . $value['real_file_name'];
      $workinfo['pictures'][] = $filearr;
    }
    $workinfo['resubmit'] = '0';
    //业务申请接口
    if ($workinfo['product_id'] == '1') {
      //新车
      $applyDiviGeneral = $this->_zhaohuibank->applyDiviGeneralForFirst($workinfo);
    } else {
      //二手车
      $applyDiviGeneral = $this->_zhaohuibank->applyDiviGeneralForSecond($workinfo);
    }
//专项卡申请
    $card_applications = $this->_anjie_file->getcardapplications($params['work_id']);
    foreach ($card_applications as $key => $value) {
      $file_name = explode('/', $value['file_path']);
      $cnt = count($file_name);
      $card_applications[$key]['real_file_name'] = $file_name[$cnt-1];
    }
    $putcard_applications = $this->_zhaohuibank->putfile($card_applications);
    if ($putcard_applications == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件传送失败');         //该任务已完成
    }
    $workinfo['card_applications'] = array();
    foreach ($card_applications as $key => $value) {
      $filearr['picid'] = '9001';
      $filearr['picurl'] = env('ZHAOHUIBANK_FILEPATH') . '?imageName=' . $value['real_file_name'];
      $workinfo['card_applications'][] = $filearr;
    }
    //专项卡申请信息上送接口
    $creditCardApply = $this->_zhaohuibank->creditCardApply($workinfo);
    return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG); 
  }
  //补录
  public function suppletozhaohuibank($params)
  {
    $this->_zhaohuibank = new Zhaohuibank();
    //获取申请件详情信息
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    if ($workinfo['loan_bank'] !== '04') {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '该申请件不是杭州朝晖支行的');         //该任务已完成
    }
//通用业务
    $workinfo['pictures'] = array();
    $workinfo['resubmit'] = '2';
    //业务申请接口
    if ($workinfo['product_id'] == '1') {
      //新车
      $applyDiviGeneral = $this->_zhaohuibank->applyDiviGeneralForFirst($workinfo);
    } else {
      //二手车
      $applyDiviGeneral = $this->_zhaohuibank->applyDiviGeneralForSecond($workinfo);
    }
    return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG); 
  }
  /**
   * 征信查询的逻辑：修改anjie_work表、添加图片、complete这个任务
     * @param type                  1为提交，2为暂存，3为退件 必传
     * @param work_id               工作id，必传
     * @param item_instance_id      任务实例id，必传
     * @param inquire_result        征信结果：正常、不正常，必传
     * @param inquire_description   征信备注  
     * @param imgs                  影像件资料 
   */
  public function inquire($user_id, $params)
  {
    $this->_pdo->beginTransaction();
    try {
      $param = $params;
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      if ($params['type'] == '1') { 
        $params['inquire_status'] = '1';
      } elseif ($params['type'] == '3') {
        $params['inquire_status'] = '2';
      } else {
        $params['inquire_status'] = '0';
      }
      $inquire = $this->_anjie_work->inquire($params);   //无论哪种状态都可以先修改anjie_work表
      if ($inquire == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '征信提交有误');                      //征信提交有误
      }
      $imgs = $params['imgs'];
      if (!is_array($imgs)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
      }
      if (!isset($imgs['delete']) || !isset($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '所传数组必须包含删除和新增');
      }
      if (!is_array($imgs['delete']) || !is_array($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除和新增图片必须为数组');
      }
      foreach ($imgs['add'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      foreach ($imgs['delete'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      //插入图片信息
      foreach ($params['imgs']['delete'] as $key => $value) {
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['id'])) {
            foreach ($params['imgs']['add'][$key]['source_lists'] as $keyword => $val) {
              if ($val['src'] == $v['src']) {
                unset($params['imgs']['add'][$key]['source_lists'][$keyword]); 
                unset($params['imgs']['delete'][$key]['source_lists'][$k]); 
              }
            }
          }
        }
      }
      //删除图片
      $deleteimgs = $this->deleteimage($params['imgs']['delete'], $user_id);  //删除图片
      if ($deleteimgs == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片删除有误');                        //图片删除有误
      }
      //插入图片信息
      $addimgs = $this->addimage($params['imgs']['add'], $params['work_id'], $user_id);  //添加图片
      if ($addimgs !== true) {
        return $addimgs;
      }  
      $file_type = $this->_anjie_file_class->listimagetypeinquire();  //文件类型
      foreach ($file_type as $key => $value) {
        $param['file_class_id'] = $value['id'];
        $param['work_id'] = $params['work_id'];
        $curren_length = $this->_anjie_file->countByWorkidAndFileclassid($param);
        $max_length = intval($value['max_length']);  //该文件类型的最大上传数量
        $min_length = intval($value['min_length']);  //该文件类型的最大上传数量
        $count = intval($curren_length['count(*)']);    //该文件类型的上传数量
        if ($count > $max_length) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');    //文件数量超过最大上传张数
        }
        if ($count < $min_length) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');    //文件数量小于最小上传张数
        }
      }
      $completers = 'true';
      $result_inquire = ($param['inquire_result'] == '1') ? '通过' : '拒绝';
      $msg = '征信结果：'. $result_inquire;
        if ($param['inquire_description'] !== '') {
          $msg = $msg . '<br/>备注：' . $param['inquire_description'];
        }
      if ($params['inquire_status'] == '1') {    //提交，且通过，complete这个任务  status=1
        $data = array(
          'work_id' => $params['work_id'],
          'inquire_status' => '1',   //通过
          'task_status' => '1',
        );
        $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
      } elseif ($params['inquire_status'] == '2') {   //退件，complete这个任务  status=2
        $data = array(
          'work_id' => $params['work_id'],
          'inquire_status' => '1',   //拒件
          'task_status' => '1',
        );
        // $refusework = $this->_anjie_work->refusework($params);
        $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '提交类型不对');                  //提交类型不对
      }

    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    $title = $param['inquire_result']==1?'征信通过':'征信未通过';
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      $salesman = $this->_anjie_users->getInfoById($rs['salesman_id']);
      // if(!empty($salesman)){
      //     $pushAction = 'report';
      //     $content = '申请编号为:'.$rs['request_no'].'的申请件'.$title;
      //     $sendData = json_encode(['work_id'=>$params['work_id'],'action'=>$pushAction,'url'=>'app/pushMessageCenter']);
      //     $push = $this->_common->curl_post(env('PUSH_URL').'message',['mobile_list'=>$salesman['account'],'data'=>$sendData,'app_name'=>env('PUSH_APP_NAME'),'title'=>$title,'content'=>$content]);
      //     $this->_anjie_push_log->addPushLog(['mobile_list'=>$salesman['account'],'send_data'=>$sendData,'return_data'=>$push,'push_action'=>$pushAction,'user_id'=>$salesman['id'],'result'=>$content,'title'=>$title]);
      //     Log::info('个推推送征信报告目标:'.$salesman['account'].'结果:'.$push);
      // }else{
      //     Log::info('个推推送征信报告时，不存在work_id='.$params['work_id'].'对应的销售人员数据');
      // }
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '征信报告完成失败');                //征信报告完成失败
    }
  }
  public function salessupplement($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $checkparams = $this->checkinputrequestparams($params);   //整理参数
      $checkparams['work_id'] = $params['work_id'];
      $checkformat = $this->checkformat($checkparams);    //验证传入参数的格式是否正确
      if ($checkformat !== true && $params['type'] !== '2') {
        return $checkformat;
      }
      $checkparams['customer_sex'] = $this->_common->get_xingbie($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      $checkparams['contact_sex'] = $this->_common->get_xingbie($params['contacts_man_certificate_number']);  //联系人性别
      $checkparams['spouse_sex'] = $this->_common->get_xingbie($params['spouse_certificate_number']);  //配偶性别
      $checkparams['customer_age'] = $this->_common->getAgeByID($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      $inputrequest = $this->_anjie_work->inputrequest($checkparams, $params['work_id']);   //申请录入修改anjie_work表
      if ($inputrequest == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '销售补件提交有误');                      //申请录入提交有误
      }
      $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
      $imgs = $params['imgs'];
      if (!is_array($imgs)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
      }
      if (!isset($imgs['delete']) || !isset($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '所传数组必须包含删除和新增');
      }
      if (!is_array($imgs['delete']) || !is_array($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除和新增图片必须为数组');
      }
      foreach ($imgs['add'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      foreach ($imgs['delete'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      //插入图片信息
      foreach ($params['imgs']['delete'] as $key => $value) {
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['id'])) {
            foreach ($params['imgs']['add'][$key]['source_lists'] as $keyword => $val) {
              if ($val['src'] == $v['src']) {
                unset($params['imgs']['add'][$key]['source_lists'][$keyword]); 
                unset($params['imgs']['delete'][$key]['source_lists'][$k]); 
              }
            }
          }
        }
      }
      //删除图片
      $deleteimgs = $this->deleteimage($params['imgs']['delete'], $user_id);  //删除图片
      if ($deleteimgs == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片删除有误');                        //图片删除有误
      }
      $addimgs = $this->addimage($params['imgs']['add'], $params['work_id'], $user_id);  //添加图片
      if ($addimgs !== true) {
        return $addimgs;
      }
      $file_type = $this->_anjie_file_class->listimagetype();  //文件类型
      foreach ($file_type as $key => $value) {
        if ($value['id'] == '19' && $workinfo['loan_bank'] == '04') {
          $value['min_length'] = '1';
        }
        $param['file_class_id'] = $value['id'];
        $param['work_id'] = $params['work_id'];
        $curren_length = $this->_anjie_file->countByWorkidAndFileclassid($param);
        $max_length = intval($value['max_length']);  //该文件类型的最大上传数量
        $min_length = intval($value['min_length']);  //该文件类型的最大上传数量
        $count = intval($curren_length['count(*)']);    //该文件类型的上传数量
        if (($count > $max_length) && ($params['type'] !== '2')) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');    //文件数量超过最大上传张数
        }
        if (($count < $min_length) && ($params['type'] !== '2')) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');    //文件数量小于最小上传张数
        }
      }
      $completers = 'true';
      $artificial = $this->_anjie_task->getartificialtask1($params);  //找到当时审核时打回的记录
      if ($params['type'] == '1') {    //提交，且通过，complete这个任务  status=1
        $data = array(
          'work_id' => $params['work_id'],
          'task_status' => '1',
          'loan_prize' => $params['loan_prize'],   //贷款金额
          'inputrequest_status' => '3',
        );
        if ($artificial['item_id'] == '9') {
          $data['supplement_sale_one_status'] = '1';
        } elseif ($artificial['item_id'] == '37') {
          $data['supplement_sale_one_status'] = '3';
          $data['supplement_sale_two_status'] = '1';
        }
        $msg = '已补件';
        $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
      } elseif ($params['type'] == '2') {   //暂存，保存到数据库，不要complete
        # code...
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '提交类型不对');                  //提交类型不对
      }
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $ficoquery = $this->ficopost($params['work_id']);
      $bairongquery = $this->bairongcreditpost($params['work_id']);
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '补件完成有误');                //申请录入完成有误
    }
  }
  //修改申请件
  public function editwork($user_id, $params)
  {
    $this->_pdo->beginTransaction();
    try {
      $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
      if (empty($workinfo)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请件不存在！');
      }
      $checkparams = $this->checkinputrequestparams($params);   //整理参数
      $checkparams['work_id'] = $params['work_id'];
      $checkformat = $this->checkformat($checkparams);    //验证传入参数的格式是否正确
      if ($checkformat !== true && $params['type'] !== '2') {
        return $checkformat;
      }
      $checkparams['customer_sex'] = $this->_common->get_xingbie($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      $checkparams['contact_sex'] = $this->_common->get_xingbie($params['contacts_man_certificate_number']);  //联系人性别
      $checkparams['spouse_sex'] = $this->_common->get_xingbie($params['spouse_certificate_number']);  //配偶性别
      $checkparams['customer_age'] = $this->_common->getAgeByID($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      $inputrequest = $this->_anjie_work->inputrequest($checkparams, $params['work_id']);   //申请录入修改anjie_work表
      if ($inputrequest == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请录入提交有误');                      //申请录入提交有误
      }
      $this->_workflow->editwork($checkparams, $params['work_id']);
      $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
      $imgs = $params['imgs'];
      if (!is_array($imgs)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
      }
      if (!isset($imgs['delete']) || !isset($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '所传数组必须包含删除和新增');
      }
      if (!is_array($imgs['delete']) || !is_array($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除和新增图片必须为数组');
      }
      foreach ($imgs['add'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      foreach ($imgs['delete'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      //插入图片信息
      foreach ($params['imgs']['delete'] as $key => $value) {
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['id'])) {
            foreach ($params['imgs']['add'][$key]['source_lists'] as $keyword => $val) {
              if ($val['src'] == $v['src']) {
                unset($params['imgs']['add'][$key]['source_lists'][$keyword]); 
                unset($params['imgs']['delete'][$key]['source_lists'][$k]); 
              }
            }
          }
        }
      }
      //删除图片
      $deleteimgs = $this->deleteimage($params['imgs']['delete'], $user_id);  //删除图片
      if ($deleteimgs == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片删除有误');                        //图片删除有误
      }
      $addimgs = $this->addimage($params['imgs']['add'], $params['work_id'], $user_id);  //添加图片
      if ($addimgs !== true) {
        return $addimgs;
      }
      $file_type = $this->_anjie_file_class->listimagetype();  //文件类型
      foreach ($file_type as $key => $value) {
        if ($value['id'] == '19' && $workinfo['loan_bank'] == '04') {
          $value['min_length'] = '1';
        }
        $param['file_class_id'] = $value['id'];
        $param['work_id'] = $params['work_id'];
        $curren_length = $this->_anjie_file->countByWorkidAndFileclassid($param);
        $max_length = intval($value['max_length']);  //该文件类型的最大上传数量
        $min_length = intval($value['min_length']);  //该文件类型的最大上传数量
        $count = intval($curren_length['count(*)']);    //该文件类型的上传数量
        if ($count > $max_length) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');    //文件数量超过最大上传张数
        }
        if ($count < $min_length) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');    //文件数量小于最小上传张数
        }
      }
      $params['process_id'] = '1';
      $params['user_id'] = $user_id;
      $params['work_id'] = $params['work_id'];
      $params['msg'] = '修改申请件';
      $this->_anjie_task->editwork($params);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    return $this->_common->output($workinfo, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }
  /**
   * 申请录入的逻辑：修改anjie_work表、添加图片、complete这个任务
   */
  public function inputrequest($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $checkparams = $this->checkinputrequestparams($params);   //整理参数
      $checkparams['work_id'] = $params['work_id'];
      $checkformat = $this->checkformat($checkparams);    //验证传入参数的格式是否正确
      if ($checkformat !== true && $params['type'] !== '2') {
        return $checkformat;
      }
      $checkparams['customer_sex'] = $this->_common->get_xingbie($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      $checkparams['contact_sex'] = $this->_common->get_xingbie($params['contacts_man_certificate_number']);  //联系人性别
      $checkparams['spouse_sex'] = $this->_common->get_xingbie($params['spouse_certificate_number']);  //配偶性别
      $checkparams['customer_age'] = $this->_common->getAgeByID($params['customer_certificate_number']);  //根据身份证号码获取客户性别
      $inputrequest = $this->_anjie_work->inputrequest($checkparams, $params['work_id']);   //申请录入修改anjie_work表
      if ($inputrequest == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请录入提交有误');                      //申请录入提交有误
      }
      $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
      $imgs = $params['imgs'];
      if (!is_array($imgs)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
      }
      if (!isset($imgs['delete']) || !isset($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '所传数组必须包含删除和新增');
      }
      if (!is_array($imgs['delete']) || !is_array($imgs['add'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除和新增图片必须为数组');
      }
      foreach ($imgs['add'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      foreach ($imgs['delete'] as $key => $value) {
        if (!isset($value['source_lists']) || !isset($value['source_type'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
        }
        if (!is_array($value['source_lists'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
        }
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
          }
        }
      }
      //插入图片信息
      foreach ($params['imgs']['delete'] as $key => $value) {
        foreach ($value['source_lists'] as $k => $v) {
          if (!isset($v['id']) && isset($params['imgs']['add'][$key])) {
            foreach ($params['imgs']['add'][$key]['source_lists'] as $keyword => $val) {
              if ($val['src'] == $v['src']) {
                unset($params['imgs']['add'][$key]['source_lists'][$keyword]); 
                unset($params['imgs']['delete'][$key]['source_lists'][$k]); 
              }
            }
          }
        }
      }
      //删除图片
      $deleteimgs = $this->deleteimage($params['imgs']['delete'], $user_id);  //删除图片
      if ($deleteimgs == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片删除有误');                        //图片删除有误
      }
      $addimgs = $this->addimage($params['imgs']['add'], $params['work_id'], $user_id);  //添加图片
      if ($addimgs !== true) {
        return $addimgs;
      }
      $file_type = $this->_anjie_file_class->listimagetype();  //文件类型
      foreach ($file_type as $key => $value) {
        if ($value['id'] == '19' && $workinfo['loan_bank'] == '04') {
          $value['min_length'] = '1';
        }
        $param['file_class_id'] = $value['id'];
        $param['work_id'] = $params['work_id'];
        $curren_length = $this->_anjie_file->countByWorkidAndFileclassid($param);
        $max_length = intval($value['max_length']);  //该文件类型的最大上传数量
        $min_length = intval($value['min_length']);  //该文件类型的最大上传数量
        $count = intval($curren_length['count(*)']);    //该文件类型的上传数量
        if (($count > $max_length) && ($params['type'] !== '2')) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');    //文件数量超过最大上传张数
        }
        if (($count < $min_length) && ($params['type'] !== '2')) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');    //文件数量小于最小上传张数
        }
      }
      $completers = 'true';
      if ($params['type'] == '1') {    //提交，且通过，complete这个任务  status=1
        $data = array(
          'work_id' => $params['work_id'],
          'inputrequest_status' => '1',   //通过
          'task_status' => '1',
          'loan_prize' => $params['loan_prize'],   //贷款金额
          'request_user_id' => $user_id,
        );
        $msg = '已录入';
        $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
      } elseif ($params['type'] == '2') {   //暂存，保存到数据库，不要complete
        # code...
      } elseif ($params['type'] == '3') {   //退件，complete这个任务  status=2
        $data = array(
          'work_id' => $params['work_id'],
          'inputrequest_status' => '2',   //拒件
          'task_status' => '2',
          'loan_prize' => $params['loan_prize'],   //贷款金额
          'request_user_id' => $user_id,
        );
        $refusework = $this->_anjie_work->refusework($params);
        $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data);
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '提交类型不对');                  //提交类型不对
      }
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $ficoquery = $this->ficopost($params['work_id']);
      $bairongquery = $this->bairongcreditpost($params['work_id']);
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请录入完成有误');                //申请录入完成有误
    }
  }
  /**
   * 人工审核
   */
  public function artificial($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
      if ($params['artificial'] == '1' and $params['artificial_status'] == '1') {
        //如果是一审，需要判断一下是否已经进行征信处理
        $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
        if (empty($workinfo)) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '该件不存在');         //拒绝理由不能为空
        } elseif($workinfo['inquire_result'] !== '1') {
          //如果征信未通过，则不能提交一审
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '该件未征信或征信未通过，不能提交');         //拒绝理由不能为空
        }
      }
      $ficoquery = $this->ficopost($params['work_id']);
      $bairongquery = $this->bairongcreditpost($params['work_id']);
      if ($params['artificial_status'] == '2'  && $params['artificial_refuse_reason'] == ''  && $params['artificial'] == '1') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '拒绝理由不能为空');         //拒绝理由不能为空
      }
      if ($params['artificial_status'] == '2'  && $params['artificialtwo_refuse_reason'] == ''  && $params['artificial'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '拒绝理由不能为空');         //拒绝理由不能为空
      }
      if ($params['artificial_status'] == '3'  && $params['artificial_description'] == '' && $params['artificial'] == '1') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '待补件备注不能为空');         //拒绝理由不能为空
      }
      if ($params['artificial_status'] == '3'  && $params['artificialtwo_description'] == '' && $params['artificial'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '待补件备注不能为空');         //拒绝理由不能为空
      }
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      if ($params['artificial'] == '1') {
        $artificial = $this->_anjie_work->artificialone($params, $params['work_id']);   //申请录入修改anjie_work表
      } elseif ($params['artificial'] == '2') {
        $artificial = $this->_anjie_work->artificialtwo($params, $params['work_id']);   //申请录入修改anjie_work表
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '审核类型不存在');         //该任务已完成
      }
      if ($artificial == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '人工审核提交有误');                      //人工审核提交有误
      }
        $sp = '一审';
        if($params['artificial'] == 2){
            $sp = '二审';
        }
        $salesman = $this->_anjie_users->getInfoById($workinfo['salesman_id']);
      $completers = 'true';
      if ($params['type'] == '1') {    //提交
        if ($params['artificial_status'] == '2') {   //拒件 ，complete这个任务  status=2
          $data = array(
            'work_id' => $params['work_id'],
            'artificialone_status' => '2',   //拒件
            'task_status' => '2',
          );
          $refusearr = explode(',', $params['artificial_refuse_reason']);
          if ($params['artificial'] =='2') {    //人工审核二审
            $data = array(
              'work_id' => $params['work_id'],
              'artificialtwo_status' => '2',   //拒件
              'task_status' => '2',
            );
            $refusearr = explode(',', $params['artificialtwo_refuse_reason']);
          }
          $msg = '审核结果：拒绝' ;
          $arr = array();
          if(!empty($refusearr)) {
            foreach ($refusearr as $key => $value) {
              if (isset($this->_artificial_refuse_reason[$value])) {
                $arr[] = $this->_artificial_refuse_reason[$value];
              }
            }
            $msg = $msg . '<br/>拒绝理由：' . implode(',', $arr);
          }
          if ($params['artificial_description'] !== '') {
            $msg = $msg . '<br/>备注：'. $params['artificial_description'];
          }
          $refusework = $this->_anjie_work->refusework($params);
          // if(!empty($salesman)){
          //     $pushAction = 'refuse';
          //     $content = '申请编号为:'.$workinfo['request_no'].'的申请件审核未通过';
          //     $sendData = json_encode(['work_id'=>$params['work_id'],'action'=>$pushAction,'url'=>'app/pushMessageCenter']);
          //     $push = $this->_common->curl_post(env('PUSH_URL').'message',['mobile_list'=>$salesman['account'],'data'=>$sendData,'app_name'=>env('PUSH_APP_NAME'),'title'=>'已拒件','content'=>$content]);
          //     $this->_anjie_push_log->addPushLog(['mobile_list'=>$salesman['account'],'send_data'=>$sendData,'return_data'=>$push,'push_action'=>$pushAction,'user_id'=>$salesman['id'],'result'=>$content,'title'=>'已拒件']);
          //     Log::info('个推推送审批'.$sp.'拒件目标:'.$salesman['account'].'结果:'.$push);
          // }else{
          //     Log::info('个推推送审批'.$sp.'拒件结果时，不存在work_id='.$params['work_id'].'对应的销售人员数据');
          // }
        } elseif ($params['artificial_status'] == '3') {    //待补件，complete这个任务  status=3
          $data = array(
            'work_id' => $params['work_id'],
            'artificialone_status' => '3',   //待补件
            'task_status' => '3',
          );
          if ($params['artificial'] =='2') {    //人工审核二审
            $data = array(
              'work_id' => $params['work_id'],
              'artificialtwo_status' => '3',   //待补件
              'task_status' => '3',
            );
          }
          $data['supplement_salesman'] = 1;
          $msg = '审核结果：待补件' ;
          if ($params['artificial_description'] !== '' && $params['artificial'] =='1') {
            $msg = $msg . '<br/>备注：'. $params['artificial_description'];
          }
          if ($params['artificialtwo_description'] !== '' && $params['artificial'] =='2') {
            $msg = $msg . '<br/>备注：'. $params['artificialtwo_description'];
          }
          // if(!empty($salesman)){
          //     $title = '待补件';
          //     $content = '申请编号为:'.$workinfo['request_no'].'的申请件需要补充资料';
          //     $pushAction = 'supply';
          //     $sendData = json_encode(['work_id'=>$params['work_id'],'action'=>$pushAction,'url'=>'app/pushMessageCenter']);
          //     $push = $this->_common->curl_post(env('PUSH_URL').'message',['mobile_list'=>$salesman['account'],'data'=>$sendData,'app_name'=>env('PUSH_APP_NAME'),'title'=>$title,'content'=>$content]);
          //     $this->_anjie_push_log->addPushLog(['mobile_list'=>$salesman['account'],'send_data'=>$sendData,'return_data'=>$push,'push_action'=>$pushAction,'user_id'=>$salesman['id'],'result'=>$content,'title'=>$title]);
          //     Log::info('个推推送审批'.$sp.'待补件目标:'.$salesman['account'].'结果:'.$push);
          // }else{
          //     Log::info('个推推送审批'.$sp.'待补件时，不存在work_id='.$params['work_id'].'对应的销售人员数据');
          // }
        } else {     //通过，complete这个任务  status=1
          $data = array(
            'work_id' => $params['work_id'],
            'artificialone_status' => '1',   //通过
            'task_status' => '1',
          );
          if ($params['artificial'] =='2') {    //人工审核二审
            $data = array(
              'work_id' => $params['work_id'],
              'artificialtwo_status' => '1',   //通过
              'task_status' => '1',
            );
          }
          $msg = '审核结果：通过' ;
          if ($params['artificial_description'] !== '') {
            $msg = $msg . '<br/>备注：'. $params['artificial_description'];
          }
        }
        $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
        //二审通过需要发送一审不需要
        if($params['artificial'] == 2){
            $title = '审核通过';
            // if(!empty($salesman)){
            //     $pushAction = 'cross';
            //     $content = '申请编号为:'.$workinfo['request_no'].'的申请件'.$title;
            //     $sendData = json_encode(['work_id'=>$params['work_id'],'action'=>$pushAction,'url'=>'app/pushMessageCenter']);
            //     $push = $this->_common->curl_post(env('PUSH_URL').'message',['mobile_list'=>$salesman['account'],'data'=>$sendData,'app_name'=>env('PUSH_APP_NAME'),'title'=>$title,'content'=>$content]);
            //     $this->_anjie_push_log->addPushLog(['mobile_list'=>$salesman['account'],'send_data'=>$sendData,'return_data'=>$push,'push_action'=>$pushAction,'user_id'=>$salesman['id'],'result'=>$content,'title'=>$title]);
            //     Log::info('个推推送审批'.$sp.'通过目标:'.$salesman['account'].'结果:'.$push);
            // }else{
            //     Log::info('个推推送审批'.$sp.'通过时，不存在work_id='.$params['work_id'].'对应的销售人员数据');
            // }
        }
      } elseif ($params['type'] == '2') {   //暂存，保存到数据库，不要complete
        # code...
      } elseif ($params['type'] == '4') {   //取消认领
        $completers = $this->_workflow->giveup($user_id, $params['item_instance_id'], $data);
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '提交类型不对');                  //提交类型不对
      }
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      if ($params['artificial_status'] == '3') {
        $params['need_new_request'] = '1';
        $params['bairong_new_request'] = '1';
        $updateficostatus = $this->_anjie_work->updateficostatus($params);
      }
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);  //返回客户详情
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '人工审核完成有误');                //人工审核完成有误
    }
  }
  /**
   * 申请打款
   */
  public function applyremittance($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $pickup = $this->pickup($user_id, $params);
      if ($pickup == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务认领失败');         //该任务认领失败
      }
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $params['applyremittance_status'] = '1';
      $applyremittance = $this->_anjie_work->applyremittance($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($applyremittance == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请打款提交有误');                      //财务打款提交有误
      }
      $completers = 'true';
      $data = array(
        'work_id' => $params['work_id'],
        'applyremittance_status' => '1',   //通过
        'task_status' => '1',
      );
      $msg = '已申请';
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请打款完成有误');                //财务打款完成有误
    }
  }
  /**
   * 打款审核
   */
  public function moneyaudit($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $pickup = $this->pickup($user_id, $params);
      if ($pickup == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务认领失败');         //该任务认领失败
      }
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $moneyaudit = $this->_anjie_work->moneyaudit($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($moneyaudit == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请打款提交有误');                      //财务打款提交有误
      }
      $completers = 'true';
      if ($params['moneyaudit_status'] == '1') {
        $data = array(
          'work_id' => $params['work_id'],
          'moneyaudit_status' => '1',   //通过
          'task_status' => '1',
        );
        $msg = '已审核';
      } elseif($params['moneyaudit_status'] == '2') {
        $data = array(
          'work_id' => $params['work_id'],
          'moneyaudit_status' => '2',   //通过
          'task_status' => '2',
        );
        $msg = '已拒件';
        $refusework = $this->_anjie_work->refusework($params);
      }
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '打款审核完成有误');                //财务打款完成有误
    }
  }
  /**
   * 财务打款
   */
  public function finance($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $params['finance_status'] = '1';
      $finance = $this->_anjie_work->finance($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($finance == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '财务打款提交有误');                      //财务打款提交有误
      }
      $completers = 'true';
      $data = array(
        'work_id' => $params['work_id'],
        'finance_status' => '1',   //通过
        'task_status' => '1',
      );
      $msg = '已打款';
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
        $salesman = $this->_anjie_users->getInfoById($rs['salesman_id']);
        // if(!empty($salesman)){
        //     $pushAction = 'pay';
        //     $title = '财务已打款';
        //     $content = '申请编号为:'.$rs['request_no'].'的申请件'.$title;
        //     $sendData = json_encode(['work_id'=>$params['work_id'],'action'=>$pushAction,'url'=>'app/pushMessageCenter']);
        //     $push = $this->_common->curl_post(env('PUSH_URL').'message',['mobile_list'=>$salesman['account'],'data'=>$sendData,'app_name'=>env('PUSH_APP_NAME'),'title'=>$title,'content'=>$content]);
        //     $this->_anjie_push_log->addPushLog(['mobile_list'=>$salesman['account'],'send_data'=>$sendData,'return_data'=>$push,'push_action'=>$pushAction,'user_id'=>$salesman['id'],'result'=>$content,'title'=>$title]);
        //     Log::info('个推推送财务打款目标:'.$salesman['account'].'结果:'.$push);
        // }else{
        //     Log::info('个推推送财务打款结果时，不存在work_id='.$params['work_id'].'对应的销售人员数据');
        // }
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '财务打款完成有误');                //财务打款完成有误
    }
  }
  /**
   * 回款确认
   */
  public function returnmoney($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $params['returnmoney_status'] = '1';
      $returnmoney = $this->_anjie_work->returnmoney($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($returnmoney == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '回款确认提交有误');                      //回款确认提交有误
      }
      $completers = 'true';
      $data = array(
        'work_id' => $params['work_id'],
        'returnmoney_status' => '1',   //通过
        'task_status' => '1',
      );
      $msg = '已确认';
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '回款确认完成有误');                //回款确认完成有误
    }
  }
  /**
   * 寄件登记
   */
  public function courier($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $params['courier_status'] = '1';
      $courier = $this->_anjie_work->courier($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($courier == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '寄件登记提交有误');                      //寄件登记提交有误
      }
      $completers = 'true';
      $data = array(
        'work_id' => $params['work_id'],
        'courier_status' => '1',   //通过
        'task_status' => '1',
      );
      $msg = '已登记';
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '寄件登记完成有误');                //寄件登记完成有误
    }
  }
  /**
   * 抄单登记
   */
  public function copytask($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $params['copytask_status'] = '1';
      $courier = $this->_anjie_work->copytask($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($courier == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '抄单登记提交有误');                      //抄单登记提交有误
      }
      $completers = 'true';
      $data = array(
        'work_id' => $params['work_id'],
        'copytask_status' => '1',   //通过
        'task_status' => '1',
      );
      $msg = '已登记';
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '抄单登记完成有误');                //抄单登记完成有误
    }
  }
  /**
   * 车辆gps登记
   */
  public function gps($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $params['gps_status'] = '1';
      $courier = $this->_anjie_work->gps($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($courier == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '车辆gps登记提交有误');                      //车辆gps登记提交有误
      }
      $completers = 'true';
      $data = array(
        'work_id' => $params['work_id'],
        'gps_status' => '1',   //通过
        'task_status' => '1',
      );
      $msg = '已登记';
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '车辆gps登记完成有误');                //车辆gps登记完成有误
    }
  }
  /**
   * 抵押登记
   */
  public function mortgage($params, $user_id)
  {
    $this->_pdo->beginTransaction();
    try {
      $has_completed = $this->_anjie_task->issettask($params['item_instance_id']);
      if (empty($has_completed)) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
      } elseif ($has_completed['status'] == '2') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
      } elseif($has_completed['user_id'] !== $user_id) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
      }
      $params['mortgage_status'] = '1';
      $courier = $this->_anjie_work->mortgage($params, $params['work_id']);   //申请录入修改anjie_work表
      if ($courier == false) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '抵押登记提交有误');                      //抵押登记提交有误
      }
      $completers = 'true';
      $data = array(
        'work_id' => $params['work_id'],
        'mortgage_status' => '1',   //通过
        'task_status' => '1',
      );
      $msg = '已登记';
      $completers = $this->_workflow->completeworkflow($user_id, $params['item_instance_id'], $data, $msg);
    } catch (Exception $e) {
      $this->_pdo->rollBack();
    }
    if ($completers !== false) {
      $rs = $this->_anjie_work->getdetailByid($params['work_id']);
      return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      $this->_pdo->rollBack();
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '抵押登记完成有误');                //抵押登记完成有误
    }
  }
  //获取逾期列表
  public function getyuqi($user_id, $params)
  {
    $limitout = '';
    if ($params['page'] && $params['size']) {
      $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
    }
    $order = " order by tb_yuqi.id desc";
    $where = $this->_yuqicondition($params);
    $rs = $this->_tb_yuqi->getDetail($where, $order, $limitout);
    foreach ($rs as $key => $value) {
      $huankuan = $this->_tb_huankuan->getinfobyidcard($value['id_card']);
      if (!empty($huankuan)) {
        $rs[$key]['huankuan'] = $huankuan['huankuan'];
      } else {
        $rs[$key]['huankuan'] = '';
      }
    }
    return $rs;
  }
  //获取逾期列表
  public function getyuqicount($user_id, $params)
  {
    $where = $this->_yuqicondition($params);
    $rs = $this->_tb_yuqi->getcount($where);
    return $rs;
  }
  //获取还款列表
  public function gethuankuan($user_id, $params)
  {
    $limitout = '';
    if ($params['page'] && $params['size']) {
      $limitout = " limit ". intval(($params['page'] - 1) * $params['size']). ', '. intval($params['size']);
    }
    $order = " order by tb_yuqi.id desc";
    $where = $this->_huankuancondition($params);
    $rs = $this->_tb_yuqi->getDetail($where, $order, $limitout);
    foreach ($rs as $key => $value) {
      $huankuan = $this->_tb_huankuan->getinfobyidcard($value['id_card']);
      if (!empty($huankuan)) {
        $rs[$key]['huankuan'] = $huankuan['huankuan'];
      } else {
        $rs[$key]['huankuan'] = '';
      }
    }
    return $rs;
  }
  //获取还款列表
  public function gethuankuancount($user_id, $params)
  {
    $where = $this->_huankuancondition($params);
    $rs = $this->_tb_huankuan->getcount($where);
    return $rs;
  }
   //还款列表的查询条件
  private function _huankuancondition($params) 
  {
      $where = "where 1=1 ";
      if ($params['request_no'] !=='' && isset($params['request_no'])) {
        $where = $where . " and anjie_work.request_no = '" . $params['request_no'] . "'";
      }
      if ($params['customer_name'] !== '' && isset($params['customer_name'])) {
        $where = $where . " and tb_yuqi.name = '".$params['customer_name'] ."'";
      }
      if ($params['product_name'] !== '' && isset($params['product_name'])) {
        $where = $where . " and anjie_work.product_id = '".$params['product_name']."'";
      }
      if ($params['customer_certificate_number'] !== '' && isset($params['customer_certificate_number'])) {
        $where = $where . " and anjie_work.customer_certificate_number = '".$params['customer_certificate_number']."'";
      }
      return $where;
  }
  //逾期列表的查询条件
  private function _yuqicondition($params) 
  {
      $where = "where yuqi_money > 0 ";
      if ($params['request_no'] !=='' && isset($params['request_no'])) {
        $where = $where . " and anjie_work.request_no = '" . $params['request_no'] . "'";
      }
      if ($params['customer_name'] !== '' && isset($params['customer_name'])) {
        $where = $where . " and tb_yuqi.name = '".$params['customer_name'] ."'";
      }
      if ($params['product_name'] !== '' && isset($params['product_name'])) {
        $where = $where . " and anjie_work.product_id = '".$params['product_name']."'";
      }
      if ($params['customer_certificate_number'] !== '' && isset($params['customer_certificate_number'])) {
        $where = $where . " and anjie_work.customer_certificate_number = '".$params['customer_certificate_number']."'";
      }
      return $where;
  }
  /**
   * 列举任务流
   */
  public function listtasks($params)
  {
    $rs = $this->_anjie_task->getDetailByWorkid($params);   //联表查询anjie_task和anjie_users查询任务流的
    return $rs;
  }
  //列出下属任务列表
  public function listsubordinatetask($user_id, $params)
  {
    $params['haschild'] = $this->_auth->haschild($user_id);   //判断是否有下属 , 1为有下属，2为无下属
    $params['type'] = '2';   //0查全部，1为查待认领，2为查已认领，3为查待家访，4为查待补件，5为查历史件，6查待分派， 7为已分派，默认查全部    //待分派等于待认领，所以1==6
    $params['date'] = '';   //需要查询的日期，不传则默认查全部
    $params['product_class_number'] = '0';   //车辆类型 产品类别编号,XC:新车，ES:二手车，不传为查全部
    $params['keyword'] = '';   //关键词
    $params['order'] = 'create_time';   //排序依据
    $worklist = $this->_workflow->lists($params, $params['subordinate_userid']);  //查询家访任务列表
    return $worklist;
  }
  //添加需要向银行上传资料的队列
  public function transtobank($params)
  {
    //下载远程图片
    $where = " where work_id = " . $params['work_id'] . " and anjie_file_class.is_bank = 1";
    $allimages = $this->_anjie_file->getall($where);
    $downarr = array();
    foreach ($allimages as $key => $value) {
      if(strpos($value['file_path'],'http') !== false || strpos($value['file_path'],'https') !== false){
        $download = $this->_file->UploadbankVideo($value);
        $downarr[] = $download['path'] . $download['file_id'] . '.' . $download['suffix'];
      }
    }
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    $where = " LEFT JOIN file_upload ON file_upload.file_id = anjie_file.file_id LEFT JOIN anjie_file_class ON anjie_file.file_class_id = anjie_file_class.id WHERE anjie_file.work_id = " . $params['work_id']. " AND file_upload.sync_to_bank_status = 0  and anjie_file_class.is_bank = 1";
    $images = $this->_anjie_file->getDetail2($where);
    //审批指令提交
    $totalNum = 1;
    $tradeno_increment = $this->_sequence_tradeno->sequence_increment($params['work_id']);
    $tradeNo = $tradeno_increment['tradeno'];
    $transdate = date('Ymd');
    $params = array();
    $params['fSeqno'] = date('Ymd') .'01'. $this->_sequence_fseqno->sequence_increment();
    $params['TradeNo'] = $tradeNo;
    $params['ApplyDate'] = date('Ymd', $workinfo['create_time']);
    $params['aName'] = $workinfo['customer_name'];
    $params['aAge'] = $this->_common->getAgeByID($workinfo['customer_certificate_number']);
    $params['aSex'] = ($workinfo['customer_sex'] == '男') ? '1' : '2';
    $params['aHuji'] = $workinfo['hukou'];
    $params['aCertType'] = '000';
    $params['aCertNum'] = $workinfo['customer_certificate_number'];
    $params['aAddress'] = $workinfo['customer_address'];
    $params['aCorp'] = $workinfo['customer_company_name'];
    $params['aPhone'] = $workinfo['customer_telephone'];
    $params['CarBrand'] = $workinfo['car_brand']. $workinfo['car_type'];   //车辆品牌+车辆型号
    $params['CarID'] = $workinfo['car_vehicle_identification_number'];     //车架号
    $params['CarPrice'] = $workinfo['car_price'];
    $params['FirstPay'] = $workinfo['first_pay'];
    $params['CardNum'] = '6222888888888888';
    $params['DiviAmt'] = $workinfo['loan_prize'];
    $params['Term'] = intval($workinfo['loan_date']);
    $params['FeeRate'] = floatval($workinfo['loan_rate']);
    $params['FeeAmt'] = floatval($workinfo['fee']);
    $params['IsAmort'] = '1';
    $params['AmortDetail'] = '车辆登记证书';
    $params['IsAssure'] = '1';
    $params['AssureCorp'] = '航标集团';
    $params['CoName'] = '林润';
    $params['FeeMode'] = '1';
    $params['TellerNum'] = $workinfo['TellerNum'];
    $params['PicNum'] = $totalNum;
    $params['work_id'] = $workinfo['id'];
    $this->_pdo->beginTransaction();
    try{
      $getdiviapply = $this->_file->getdiviapply($params);
      if ($getdiviapply!==false) {
          if ($getdiviapply['status'] !== '0') {
              $flag = Mail::send('common.email',['arr'=>$getdiviapply],function($message){
                  $to = 'jiangd@ifcar99.com';
                  $message->to($to)->subject('银行推送数据错误');
              });
              Log::info("事务操作失败，事务回滚！");
              $this->_pdo->rollBack();
              return false;
          }
      }
    }catch (Exception $e){
        Log::info("事务操作失败，事务回滚！");
        $this->_pdo->rollBack();
        return false;
    }
    
    // Log::info('获取到相应的图片');
    $uploadtobank = $this->_file->uploadsToBankQueue($images);
    if($uploadtobank){
        try{
            //图像提交确认
            foreach ($images as $key => $value) {
                if ($value['file_type'] == '1') {
                  $value['suffix'] = 'jpg';
                }
                $param = array();
                $param['fSeqno'] = $transdate . '01' . $this->_sequence_fseqno->sequence_increment();  //这个字段有问题，到底同一个件的fSeqno需不需要相同?目前看来，传重复的好像也可以的样子
                $param['Seqno'] = $this->_sequence_fseqno->Seqno_increment();
                $param['TotalNum'] = $totalNum;
                $param['ImageID'] = $value['imageid'];
                $param['ImageType'] = $value['file_type'];
                // $param['ImageFile'] = '1.jpg';
                $param['ImageFile'] = $value['file_id'] . '.' . $value['suffix'];
                $param['ImagePath'] = $value['path'];
                $param['TradeNo'] = $tradeNo;
                $param['Serialno'] = $this->_sequence_fseqno->Serialno_increment();
                $param['Catagory'] = $value['bank_catagory'];
                $param['Note'] = $value['file_name'];
                $param['work_id'] = $params['work_id'];
                $getimagelist = $this->_file->getimagelist($param);
                $rs['getimagelist'][] = $param;
            }
            
            //整个workid数据全部上传和数据确认之后把状态修改为成功
            $this->_anjie_work->setSuccessStatus($params['work_id']);
            $rs['getdiviapply'] = $params;
            $this->_pdo->commit();
            return $rs;
        }catch (Exception $e){
            Log::info("事务操作失败，事务回滚！");
            $this->_pdo->rollBack();
            return false;
        }
    }
    foreach ($downarr as $key => $value) {
      if (file_exists(public_path($value))) {
        unlink(public_path($value));
      }
    }
  }
  public function suppletobank($params)
  {
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    //补录接口
    $totalNum = 1;
    $tradeNo = '002' . sprintf("%09d", intval($params['work_id']));
    $transdate = date('Ymd', strtotime("+1405 minutes"));
    $params = array();
    $params['fSeqno'] = date('Ymd') . $this->_sequence_fseqno->sequence_increment();
    $params['TradeNo'] = $tradeNo;
    $params['ApplyDate'] = date('Ymd', $workinfo['create_time']);
    $params['aName'] = $workinfo['customer_name'];
    $params['aAge'] = $this->_common->getAgeByID($workinfo['customer_certificate_number']);
    $params['aSex'] = ($workinfo['customer_sex'] == '男') ? '1' : '2';
    $params['aHuji'] = $workinfo['hukou'];
    $params['aCertType'] = '000';
    $params['aCertNum'] = $workinfo['customer_certificate_number'];
    $params['aAddress'] = $workinfo['customer_address'];
    $params['aCorp'] = $workinfo['customer_company_name'];
    $params['aPhone'] = $workinfo['customer_telephone'];
    $params['CarBrand'] = $workinfo['car_brand']. $workinfo['car_type'];   //车辆品牌+车辆型号
    $params['CarID'] = $workinfo['car_vehicle_identification_number'];     //车架号
    $params['CarPrice'] = $workinfo['car_price'];
    $params['FirstPay'] = $workinfo['first_pay'];
    $params['CardNum'] = '6222888888888888';
    $params['DiviAmt'] = $workinfo['loan_prize'];
    $params['Term'] = intval($workinfo['loan_date']);
    $params['FeeRate'] = floatval($workinfo['loan_rate']);
    $params['FeeAmt'] = floatval($workinfo['fee']);
    $params['IsAmort'] = '1';
    $params['AmortDetail'] = '车辆登记证书';
    $params['IsAssure'] = '1';
    $params['AssureCorp'] = '航标集团';
    $params['CoName'] = '林润';
    $params['FeeMode'] = '1';
    $params['AmortNum'] = $workinfo['AmortNum'];
    $params['TellerNum'] = $workinfo['TellerNum'];
    $params['PicNum'] = $totalNum;
    $params['work_id'] = $workinfo['id'];
    $this->_pdo->beginTransaction();
    try{
      $getdivisupple = $this->_file->divisupple($params);
      if ($getdivisupple!==false) {
          if ($getdivisupple['status'] !== '0') {
              $flag = Mail::send('common.email',['arr'=>$getdivisupple],function($message){
                  $to = 'jiangd@ifcar99.com';
                  $message->to($to)->subject('银行推送数据错误');
              });
              Log::info("事务操作失败，事务回滚！");
              $this->_pdo->rollBack();
              return false;
          }
      }
    }catch (Exception $e){
        Log::info("事务操作失败，事务回滚！");
        $this->_pdo->rollBack();
        return false;
    }
  }
  //添加需要向银行上传资料的队列
  public function retranstobank($params)
  {
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
      $where = " LEFT JOIN file_upload ON file_upload.file_id = anjie_file.file_id LEFT JOIN anjie_file_class ON anjie_file.file_class_id = anjie_file_class.id WHERE anjie_file.work_id = " . $params['work_id']. " AND file_upload.sync_to_bank_status = 1";
    $images = $this->_anjie_file->getDetail($where);
    // Log::info('获取到相应的图片');
    // $uploadtobank = $this->_file->uploadsToBankQueue($images);
    // if($uploadtobank){
        $this->_pdo->beginTransaction();
        try{
            $totalNum = count($images);
            $tradeNo = '002' . sprintf("%09d", intval($params['work_id']));
            $transdate = date('Ymd', strtotime("+1405 minutes"));
            //图像提交确认
            // foreach ($images as $key => $value) {
            //     $param = array();
            //     $param['fSeqno'] = $transdate . $this->_sequence_fseqno->sequence_increment();  //这个字段有问题，到底同一个件的fSeqno需不需要相同?目前看来，传重复的好像也可以的样子
            //     $param['Seqno'] = $key;
            //     $param['TotalNum'] = $totalNum;
            //     $param['ImageID'] = $value['id'];
            //     $param['ImageType'] = $value['file_type'];
            //     // $param['ImageFile'] = '1.jpg';
            //     $param['ImageFile'] = $value['file_id'] . '.' . $value['suffix'];
            //     $param['ImagePath'] = '/upload/'. $value['path'];
            //     $param['TradeNo'] = $tradeNo;
            //     $param['Serialno'] = strval($key+1);
            //     $param['Catagory'] = $value['bank_catagory'];
            //     $param['Note'] = $value['file_name'];
            //     $getimagelist = $this->_file->getimagelist($param);
            //     $rs['getimagelist'][] = $param;
            // }
            //审批指令提交
            $params = array();
            $params['fSeqno'] = date('Ymd') . $this->_sequence_fseqno->sequence_increment();
            $params['TradeNo'] = $tradeNo;
            $params['ApplyDate'] = date('Ymd', $workinfo['create_time']);
            $params['aName'] = $workinfo['customer_name'];
            $params['aAge'] = $this->_common->getAgeByID($workinfo['customer_certificate_number']);
            $params['aSex'] = ($workinfo['customer_sex'] == '男') ? '1' : '2';
            $params['aHuji'] = $workinfo['hukou'];
            $params['aCertType'] = '000';
            $params['aCertNum'] = $workinfo['customer_certificate_number'];
            $params['aAddress'] = $workinfo['customer_address'];
            $params['aCorp'] = $workinfo['customer_company_name'];
            $params['aPhone'] = $workinfo['customer_telephone'];
            $params['CarBrand'] = $workinfo['car_brand']. $workinfo['car_type'];   //车辆品牌+车辆型号
            $params['CarID'] = $workinfo['car_vehicle_identification_number'];     //车架号
            $params['CarPrice'] = $workinfo['car_price'];
            $params['FirstPay'] = $workinfo['first_pay'];
            $params['CardNum'] = '6222888888888888';
            $params['DiviAmt'] = $workinfo['loan_prize'];
            $params['Term'] = intval($workinfo['loan_date']);
            $params['FeeRate'] = floatval($workinfo['loan_rate']);
            $params['FeeAmt'] = floatval($workinfo['fee']);
            $params['IsAmort'] = '1';
            $params['AmortDetail'] = '车辆登记证书';
            $params['IsAssure'] = '1';
            $params['AssureCorp'] = '航标集团';
            $params['CoName'] = '林润';
            $params['FeeMode'] = '1';
            $params['TellerNum'] = '160201989';
            $params['PicNum'] = $totalNum;
            $params['work_id'] = $workinfo['id'];
            $divisupple = $this->_file->divisupple($params);
            if ($divisupple!==false) {
                if ($divisupple['status'] !== '0') {
                    $flag = Mail::send('common.email',['arr'=>$divisupple],function($message){
                        $to = 'jiangd@ifcar99.com';
                        $message->to($to)->subject('银行推送数据错误');
                    });
                    Log::info("事务操作失败，事务回滚！");
                    $this->_pdo->rollBack();
                    return false;
                }
            }
            //整个workid数据全部上传和数据确认之后把状态修改为成功
            // $this->_anjie_work->setSuccessStatus($params['work_id']);
            $rs['divisupple'] = $params;
            $this->_pdo->commit();
            return $rs;
        }catch (Exception $e){
            Log::info("事务操作失败，事务回滚！");
            $this->_pdo->rollBack();
            return false;
        }
    // }
  }

  public function getservice($params)
  {
    $where = ' where is_delete=2';
    if ($params['type'] == '1') {
      $where = $where . ' and type = 1';
    } elseif($params['type'] == '2') {
      $where = $where . ' and type = 2';
    } elseif($params['type'] == '3') {
      $where = $where . ' and type = 3';
    }
    $order = ' order by sort asc';
    $limit = '';
    $rs = $this->_anjie_service->getDetail($where, $limit, $order);
    $path = 'http://' . $_SERVER['HTTP_HOST'];
    foreach ($rs as $key => $value) {
      if ($value['icon_url'] !== '') {
        if(strpos($value['icon_url'],'http') !== false || strpos($value['icon_url'],'https') !== false){
          $rs[$key]['icon_url'] = $value['icon_url'];
        } else {
          $rs[$key]['icon_url'] = $path . $value['icon_url'];
        }
      }
    }
    return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }

  //验证申请录入中的参数
  public function checkinputrequestparams($params)
  {
    $rs['customer_marital_status'] = isset($params['customer_marital_status']) ? $params['customer_marital_status'] : '';   //婚姻状况，1为已婚，2为未婚'
    $rs['customer_has_bondsman'] = isset($params['customer_has_bondsman']) ? $params['customer_has_bondsman'] : '';   //客户是否有担保人
    $rs['customer_name'] = isset($params['customer_name']) ? $params['customer_name'] : '';   //客户姓名
    $rs['customer_certificate_number'] = isset($params['customer_certificate_number']) ? $params['customer_certificate_number'] : '';   //身份证号
    $rs['customer_telephone'] = isset($params['customer_telephone']) ? $params['customer_telephone'] : '';   //手机号
    $rs['customer_address'] = isset($params['customer_address']) ? $params['customer_address'] : '';   //户口所在地
    $rs['hukou'] = isset($params['hukou']) ? $params['hukou'] : '';   //户口所在地
    $rs['customer_company_name'] = isset($params['customer_company_name']) ? $params['customer_company_name'] : '';   //客户公司地址
    $rs['customer_company_phone_number'] = isset($params['customer_company_phone_number']) ? $params['customer_company_phone_number'] : '';   //客户公司电话
    $rs['company_address'] = isset($params['company_address']) ? $params['company_address'] : '';   //客户公司地址
    $rs['spouse_name'] = isset($params['spouse_name']) ? $params['spouse_name'] : '';   //配偶姓名
    $rs['spouse_certificate_number'] = isset($params['spouse_certificate_number']) ? $params['spouse_certificate_number'] : '';   //配偶身份证号码
    $rs['spouse_telephone'] = isset($params['spouse_telephone']) ? $params['spouse_telephone'] : '';   //'配偶电话号码'
    $rs['spouse_company_name'] = isset($params['spouse_company_name']) ? $params['spouse_company_name'] : '';   //配偶公司名称
    $rs['spouse_company_telephone'] = isset($params['spouse_company_telephone']) ? $params['spouse_company_telephone'] : '';   //配偶公司电话
    $rs['spouse_company_address'] = isset($params['spouse_company_address']) ? $params['spouse_company_address'] : '';   //配偶公司地址
//联系人信息
    $rs['contacts_man_name'] = isset($params['contacts_man_name']) ? $params['contacts_man_name'] : '';   //联系人姓名
    $rs['contacts_man_relationship'] = isset($params['contacts_man_relationship']) ? $params['contacts_man_relationship'] : '';   //关系
    $rs['contacts_man_certificate_number'] = isset($params['contacts_man_certificate_number']) ? $params['contacts_man_certificate_number'] : '';   //联系人身份证号
    $rs['contacts_man_telephone'] = isset($params['contacts_man_telephone']) ? $params['contacts_man_telephone'] : '';   //联系人手机号
    $rs['bondsman_name'] = isset($params['bondsman_name']) ? $params['bondsman_name'] : '';   //'担保人姓名',
    $rs['bondsman_certificate_number'] = isset($params['bondsman_certificate_number']) ? $params['bondsman_certificate_number'] : '';   //担保人身份证号码
    $rs['bondsman_telephone'] = isset($params['bondsman_telephone']) ? $params['bondsman_telephone'] : '';   //担保人电话
    $rs['bondsman_company_name'] = isset($params['bondsman_company_name']) ? $params['bondsman_company_name'] : '';   //担保人公司名称
    $rs['bondsman_company_telephone'] = isset($params['bondsman_company_telephone']) ? $params['bondsman_company_telephone'] : '';   //担保人公司电话
    $rs['bondsman_company_address'] = isset($params['bondsman_company_address']) ? $params['bondsman_company_address'] : '';   //担保人公司地址
    $rs['bondsman_spouse_name'] = isset($params['bondsman_spouse_name']) ? $params['bondsman_spouse_name'] : '';   //担保人配偶姓名
    $rs['bondsman_spouse_idcard'] = isset($params['bondsman_spouse_idcard']) ? $params['bondsman_spouse_idcard'] : '';   //担保人配偶公司
    $rs['car_brand'] = isset($params['car_brand']) ? $params['car_brand'] : '';   //车辆品牌
    $rs['car_type'] = isset($params['car_type']) ? $params['car_type'] : '';   //车辆型号
    $rs['deposit_bank'] = isset($params['deposit_bank']) ? $params['deposit_bank'] : '';   //开户银行
    $rs['bank_account_name'] = isset($params['bank_account_name']) ? $params['bank_account_name'] : '';   //银行户名',
    $rs['card_number'] = isset($params['card_number']) ? $params['card_number'] : '';   //打款卡号
    $rs['constract_no'] = isset($params['constract_no']) ? $params['constract_no'] : '';   //合同编号
    $rs['car_brand'] = isset($params['car_brand']) ? $params['car_brand'] : '';   //车辆品牌
    $rs['car_type'] = isset($params['car_type']) ? $params['car_type'] : '';   //车辆型号
    $rs['car_price'] = isset($params['car_price']) ? $params['car_price'] : '';   //车辆价格
    $rs['car_vehicle_identification_number'] = isset($params['car_vehicle_identification_number']) ? $params['car_vehicle_identification_number'] : '';   //车架号
    $rs['loan_prize'] = isset($params['loan_prize']) ? $params['loan_prize'] : '';   //贷款金额
    $rs['loan_date'] = isset($params['loan_date']) ? $params['loan_date'] : '';   //贷款期数
    $rs['first_pay'] = isset($params['first_pay']) ? $params['first_pay'] : '';   //首付款
    $rs['first_pay_ratio'] = isset($params['first_pay_ratio']) ? $params['first_pay_ratio'] : '';   //首付比例
    $rs['loan_rate'] = isset($params['loan_rate']) ? $params['loan_rate'] : '';   //利率
    $rs['has_insurance'] = isset($params['has_insurance']) ? $params['has_insurance'] : '';   //有无保险
    $rs['loan_bank'] = isset($params['loan_bank']) ? $params['loan_bank'] : '';   //有无保险
    $rs['insurance_company'] = isset($params['insurance_company']) ? $params['insurance_company'] : '';   //保险公司
    $rs['commercial_insurance'] = isset($params['commercial_insurance']) ? $params['commercial_insurance'] : '';   //商业险
    $rs['compulsory_insurance'] = isset($params['compulsory_insurance']) ? $params['compulsory_insurance'] : '';   //交强险
    $rs['vehicle_vessel_tax'] = isset($params['vehicle_vessel_tax']) ? $params['vehicle_vessel_tax'] : '';   //车船税
    $rs['gross_premium'] = isset($params['gross_premium']) ? $params['gross_premium'] : '';   //保费总额
    $rs['total_expense'] = isset($params['total_expense']) ? $params['total_expense'] : '';   //费用合计
    $rs['inputrequest_description'] = isset($params['inputrequest_description']) ? $params['inputrequest_description'] : '';   //  备注信息
    $rs['fee'] = floatval($rs['loan_prize']) * floatval($rs['loan_rate']) /100;
    $rs['carshop_name'] = isset($params['carshop_name']) ? $params['carshop_name'] : '';   //车行名称
    $rs['carshop_address'] = isset($params['carshop_address']) ? $params['carshop_address'] : '';   //  车行地址
    $rs['idcard_valid_starttime'] = isset($params['idcard_valid_starttime']) ? $params['idcard_valid_starttime'] : '';   //  证件有效期的开始时间
    $rs['idcard_valid_endtime'] = isset($params['idcard_valid_endtime']) ? $params['idcard_valid_endtime'] : '';   //  证件有效期的结束时间
    $rs['idcard_authority'] = isset($params['idcard_authority']) ? $params['idcard_authority'] : '';   //  发证机关
    $rs['housing_situation'] = isset($params['housing_situation']) ? $params['housing_situation'] : '';   //  住宅状况
    $rs['housing_postcode'] = isset($params['housing_postcode']) ? $params['housing_postcode'] : '';   //  住宅邮编
    $rs['education_level'] = isset($params['education_level']) ? $params['education_level'] : '';   //  教育程度
    $rs['profession'] = isset($params['profession']) ? $params['profession'] : '';   //  职业
    $rs['business_nature'] = isset($params['business_nature']) ? $params['business_nature'] : '';   //  单位性质
    $rs['car_evaluation_price'] = isset($params['car_evaluation_price']) ? $params['car_evaluation_price'] : '';   //  车辆评估价格
    $rs['car_evaluation_authority'] = isset($params['car_evaluation_authority']) ? $params['car_evaluation_authority'] : '';   //  车辆评估机构
    $rs['car_use_years'] = isset($params['car_use_years']) ? $params['car_use_years'] : '';   //  车辆使用年限
    return $rs;
  }
  //验证格式是否正确
  public function checkformat($checkparams)
  {
    if ($checkparams['customer_name'] == '' || $checkparams['customer_certificate_number'] == ''|| $checkparams['customer_telephone'] == ''|| $checkparams['customer_address'] == ''|| $checkparams['hukou'] == '' || $checkparams['customer_marital_status'] == '' ||$checkparams['customer_has_bondsman'] == '' ||$checkparams['loan_prize'] == '' ||$checkparams['loan_date'] == '' ||$checkparams['first_pay_ratio'] == '' ||$checkparams['loan_rate'] == '' ||$checkparams['car_brand'] == '' ||$checkparams['car_type'] == '' || $checkparams['car_price'] == '' || $checkparams['has_insurance'] == '' || $checkparams['loan_bank'] == '' || $checkparams['customer_company_name'] == '') {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '信息填写不完整');         //信息填写不完整
    }
    $workinfo = $this->_anjie_work->getdetailByid($checkparams['work_id']);
    if ($workinfo['loan_bank'] == '04') {
      if ($checkparams['idcard_valid_starttime'] == '' || $checkparams['idcard_valid_endtime'] == '' || $checkparams['idcard_authority'] == '' || $checkparams['housing_situation'] == '' || $checkparams['housing_postcode'] == '' || $checkparams['education_level'] == '' || $checkparams['profession'] == '' || $checkparams['business_nature'] == '') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '信息填写不完整');         //信息填写不完整
      }
      if ($workinfo['product_id'] == '2') {
        if ($checkparams['car_evaluation_price'] == '' || $checkparams['car_evaluation_authority'] == '' || $checkparams['car_use_years'] == '') {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '信息填写不完整');         //信息填写不完整
        }
      }
    }
    
    $check_telephone_number = json_decode($this->_common->check_telephone_number($checkparams['customer_telephone']));       //验证是否手机号码
    if ($check_telephone_number->error_no !== 200) {
      return json_encode($check_telephone_number);
    }
    $isIdCard = json_decode($this->_common->isIdCard($checkparams['customer_certificate_number']));     //验证是否身份证号码
    if ($isIdCard->error_no !== 200) {
      return json_encode($isIdCard);
    }
    
    if ($checkparams['customer_marital_status'] == '1') {
      if ($checkparams['spouse_name'] == '' || $checkparams['spouse_certificate_number'] == '' ||$checkparams['spouse_telephone'] == '') {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '配偶信息填写不完整');         //配偶信息填写不完整
      }
      if (!$this->_common->checktelephone($checkparams['spouse_telephone'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '配偶手机号码格式不正确');         //配偶手机号码格式不正确
      }
      $isIdCard = json_decode($this->_common->isIdCard($checkparams['spouse_certificate_number']));     //验证是否身份证号码
      if ($isIdCard->error_no !== 200) {
        return json_encode($isIdCard);
      }
      // if (!$this->_common->checkcardid($checkparams['spouse_certificate_number'])) {
      //   return $this->_common->output(false, Constant::ERR_FAILED_NO, '配偶身份证号码格式不正确');         //配偶手机号码格式不正确
      // }
    }
    if ($checkparams['customer_marital_status'] == '2') {
      // if ($checkparams['contacts_man_name'] == '' || $checkparams['contacts_man_relationship'] == '' ||$checkparams['contacts_man_certificate_number'] == '' ||$checkparams['contacts_man_telephone'] == '') {
      //   return $this->_common->output(false, Constant::ERR_FAILED_NO, '联系人信息填写不完整');         //联系人信息填写不完整
      // }
      if ($checkparams['contacts_man_telephone']!=='' && !$this->_common->checktelephone($checkparams['contacts_man_telephone'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '联系人手机号码格式不正确');         //联系人手机号码格式不正确
      }

      if ($checkparams['contacts_man_certificate_number']!=='') {
        $isIdCard = json_decode($this->_common->isIdCard($checkparams['contacts_man_certificate_number']));     //验证是否身份证号码
        if ($isIdCard->error_no !== 200) {
          return json_encode($isIdCard);
        }
        // return $this->_common->output(false, Constant::ERR_FAILED_NO, '联系人身份证号码格式不正确');         //联系人身份证号码格式不正确
      }
    }
    if ($checkparams['customer_has_bondsman'] == '1') {
      // if ($checkparams['bondsman_name'] == '' || $checkparams['bondsman_certificate_number'] == '' ||$checkparams['bondsman_telephone'] == '') {
      //   return $this->_common->output(false, Constant::ERR_FAILED_NO, '担保人信息填写不完整');         //担保人信息填写不完整
      // }
      if ($checkparams['bondsman_telephone']!=='' && !$this->_common->checktelephone($checkparams['bondsman_telephone'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '担保人手机号码格式不正确');         //担保人手机号码格式不正确
      }
      if ($checkparams['bondsman_certificate_number']!=='') {
        $isIdCard = json_decode($this->_common->isIdCard($checkparams['bondsman_certificate_number']));     //验证是否身份证号码
        if ($isIdCard->error_no !== 200) {
          return json_encode($isIdCard);
        }
        // return $this->_common->output(false, Constant::ERR_FAILED_NO, '担保人身份证号码格式不正确');         //担保人手机号码格式不正确
      }
    }
    // if ($checkparams['has_insurance'] == '1') {
    //   if ($checkparams['insurance_company'] == '' || $checkparams['commercial_insurance'] == '' ||$checkparams['compulsory_insurance'] == '' ||$checkparams['vehicle_vessel_tax'] == '' ||$checkparams['gross_premium'] == '') {
    //     return $this->_common->output(false, Constant::ERR_FAILED_NO, '保险信息填写不完整');         //保险信息填写不完整
    //   }
    //   if (!$this->_common->checknumber($checkparams['commercial_insurance']) || !$this->_common->checknumber($checkparams['compulsory_insurance'])  || !$this->_common->checknumber($checkparams['vehicle_vessel_tax']) || !$this->_common->checknumber($checkparams['gross_premium'])) {
    //     return $this->_common->output(false, Constant::ERR_FAILED_NO, '数字类型不正确');         //数字类型不正确
    //   }
    // }
    if (!$this->_common->checkfloat($checkparams['car_price']) || !$this->_common->checkfloat($checkparams['loan_prize']) || !$this->_common->checkfloat($checkparams['first_pay']) || !$this->_common->checkfloat($checkparams['loan_rate']) ) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '数字类型不正确');         //数字类型不正确
    }
    if(!empty($checkparams['car_vehicle_identification_number']) && !preg_match("/^[a-zA-Z0-9]{17}$/",$checkparams['car_vehicle_identification_number'])){  
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '车架号必须为17位的数字和英文');    //车架号必须为17位的数字和英文
    }
    if (floatval($checkparams['first_pay']) + floatval($checkparams['loan_prize']) !== floatval($checkparams['car_price'])) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '车辆价格需要等于贷款额和首付金额的总和');         
    }
    return true;
  }
}

