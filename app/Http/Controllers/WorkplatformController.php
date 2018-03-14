<?php

namespace App\Http\Controllers;
use App\Http\Models\business\Workplatform;
use App\Http\Models\business\Auth;
use Request;
use Illuminate\Support\Facades\Log;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
//主要用于展示静态页面
class WorkplatformController extends Controller
{ 
    private $_work = null;
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_workplatform = new Workplatform();
        $this->_auth = new Auth();
        $this->_pdo = new Pdo();
    }
    public function fixcurrentid()
    {
        $rs = $this->_workplatform->fixcurrentid();
        var_dump($rs);
    }
    //fico数据提交
    public function ficopost()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $work_id =  Request::input('work_id', '');
        $rs = $this->_workplatform->ficopost($work_id);
        return $rs;
    }
    //百融
    public function bairongcredit()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $work_id =  Request::input('work_id', '');
        $rs = $this->_workplatform->bairongcreditpost($work_id);
        return $rs;
    }
    /**
     * 人行征信申请表单提交
     */
    public function creditrequestsubmit()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::input();
        $params['merchant_name'] =  Request::input('merchant_name', '商户A');
        $params['merchant_id'] =  Request::input('merchant_id', '1');
        $params['spouse_name'] =  Request::input('spouse_name', '');
        $params['spouse_certificate_number'] =  Request::input('spouse_certificate_number', '');
        // $params['bondsman_certificate_numberbondsman_spouse_name'] =  Request::input('bondsman_certificate_numberbondsman_spouse_name', '');
        $params['bondsman_name'] =  Request::input('bondsman_name', '');
        $params['bondsman_certificate_number'] =  Request::input('bondsman_certificate_number', '');
        $params['bondsman_spouse_name'] =  Request::input('bondsman_spouse_name', '');
        $params['bondsman_spouse_idcard'] =  Request::input('bondsman_spouse_idcard', '');
        $params['loan_bank'] =  Request::input('loan_bank', '');  //贷款银行
        if ($params['customer_name'] == NULL || $params['customer_certificate_number'] == NULL|| $params['customer_telephone'] == NULL || $params['merchant_id'] == NULL|| $params['product_id'] == NULL|| $params['merchant_name'] == NULL|| $params['product_name'] == NULL || !isset($params['imgs']) || $params['imgs'] == NULL) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        // try{
            $rs = $this->_workplatform->creditrequestsubmit($user_id, $params);
        // } catch(\Exception $e) { 
            // return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        // }
        return $rs;
    }
    /**
     * 写入合同编号
     */
    public function setconstractno()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['constract_no'] =  Request::input('constract_no', '');//合同编号
        $params['work_id'] =  Request::input('work_id', '');//工作ID
        try{
            $rs = $this->_workplatform->setconstractno($params);
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        if ($rs !== false) {
            $info = $this->_workplatform->getAppinfoById($params['work_id']);
            return $this->_common->output($info, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    /**
     * 写入拜访总结
     */
    public function setvisitdescrip()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['visit_description'] =  Request::input('visit_description', '');//拜访总结
        $params['work_id'] =  Request::input('work_id', '');//工作ID
        try{
            $rs = $this->_workplatform->setvisitdescrip($params);
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        if ($rs !== false) {
            $info = $this->_workplatform->getAppinfoById($params['work_id']);
            return $this->_common->output($info, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    /**
     * 写入客户地址
     */
    public function setcustomeraddress()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['customer_address'] =  Request::input('customer_address', '');//客户居住地址
        $params['work_id'] =  Request::input('work_id', '');//工作ID
        try{
            $rs = $this->_workplatform->setcustomeraddress($params);
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        if ($rs !== false) {
            $info = $this->_workplatform->getAppinfoById($params['work_id']);
            return $this->_common->output($info, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }

    /**
     * 设置家访员位置
     * @param lat 纬度
     * @param lng 经度
     * @param userid 用户ID
     */
    public function setvisitlocation()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['lat'] =  Request::input('lat', '');  //纬度
        $param['lng'] =  Request::input('lng', '');  //经度
        $param['userid'] = $user_id;  //用户ID
        try{
            $rs = $this->_workplatform->setPoint($param);
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        return $rs;
    }
    /**
     * 开始结束家访
     * @param type 1为开始，2为结束
     */
    public function beginendvisit()
    {
        //获得家访员userid
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['type'] =  Request::input('type', '');  //1为开始，2为结束
        $param['work_id'] =  Request::input('work_id', '');  //工作id
        $param['lat'] =  Request::input('lat', '');  //纬度
        $param['lng'] =  Request::input('lng', '');  //经度
        try{
            $rs = $this->_workplatform->beginendvisit($param, $user_id);  //开始结束家访的逻辑
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        return $rs;
    }
    /**
     * 工作台列出所有件
     * @param 
     */
    public function listworkplatform()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $roleid = Request::input('role_id', '');;   //征信报告的role_id=80
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $params['type'] =  Request::input('type', '1');   //1查待认领，2查待处理
        $params['credit_self'] =  Request::input('credit_self', '2');   //1查自己征信申请的件
        $params['roleid'] = $roleid;
        $params['user_id'] = $user_id;
        $inputs = Request::all();  
        try{
            $worklist = $this->_workplatform->getinquirelist($params, $inputs);
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        if ($worklist !== false) {
            return $this->_common->output($worklist, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    /**
     * 申请件查询
     * @param 
     */
    public function taskquery()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['user_id'] = $user_id;
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $inputs = Request::all();
        $rs['rows'] = $this->_workplatform->taskquery($params, $inputs);
        $count = $this->_workplatform->taskquerycount($inputs);
        $rs['total'] = $count['count'];
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    /**
     * 申请件查询(分区)
     * @param 
     */
    public function taskquerypartition()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['user_id'] = $user_id;
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $inputs = Request::all();
        $rs['rows'] = $this->_workplatform->taskquerypartition($params, $inputs);
        $count = $this->_workplatform->taskquerypartitioncount($params, $inputs);
        $rs['total'] = $count['count'];
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    /**
     * 申请件查询(省级)
     * @param 
     */
    public function taskqueryprovince()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['user_id'] = $user_id;
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $inputs = Request::all();
        $rs['rows'] = $this->_workplatform->taskqueryprovince($params, $inputs);
        $count = $this->_workplatform->taskqueryprovincecount($params, $inputs);
        $rs['total'] = $count['count'];
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    /**
     * 申请件查询
     * @param 
     */
    public function taskqueryself()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['user_id'] = $user_id;
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $inputs = Request::all();
        $rs['rows'] = $this->_workplatform->taskqueryself($params, $inputs);
        $count = $this->_workplatform->taskqueryselfcount($params, $inputs);
        $rs['total'] = $count['count'];
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    /**
     * 获取申请件信息
     * @param work_id 工作id
     */
    public function getWorkinfo()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['work_id'] =  Request::input('work_id', '');   //工作id
        try{
            $rs = $this->_workplatform->getInfoById($params);
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    /**
     * 征信认领操作
     * @param work_id               工作id
     */
    public function pickup()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        if ($params['work_id'] == '' || $params['item_instance_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->pickup($user_id, $params);   //认领征信的具体逻辑
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        return $rs;
    }
    /**
     * 工作流认领
     * @param work_id               工作id
     */
    public function workflowpickup()
    {
        $this->_common->setlog();
        $user_id = Request::input('user_id', '');   //客户id
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        if ($params['work_id'] == '' || $params['item_instance_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->pickup($user_id, $params);   //认领征信的具体逻辑
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    /**
     * 工行认领和推送
     * @param work_id               工作id
     */
    public function bankpickupandpush()
    {
        $this->_common->setlog();
        $user_id = Request::input('user_id', '');   //客户id
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        if ($params['work_id'] == '' || $params['item_instance_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        $rs = $this->_workplatform->bankpickupandpush($user_id, $params);   //认领征信的具体逻辑
        return $rs;
    }
    public function creditquerylog()
    {
        $rs = $this->_workplatform->creditquerylog();
        return $rs;
    }

    /**
     * 征信
     * @param type                  1为提交，2为暂存，3为退件 必传
     * @param work_id               工作id，必传
     * @param item_instance_id      任务实例id，必传
     * @param inquire_result        征信结果：正常、不正常，必传
     * @param inquire_description   征信备注  
     * @param imgs                  影像件资料 
     */
    public function inquire()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params = Request::all();   //传参
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        $params['type'] = Request::input('type', '');   //1为提交，2为暂存，3为退件
        // $params['inquire_result'] = Request::input('inquire_result', '');   //征信结果：1:通过、2：不通过
        $params['inquire_description'] = Request::input('inquire_description', '');   //征信备注
        $params['imgs'] = Request::input('imgs', array());   //影像
        if (!isset($params['imgs']) || $params['imgs'] == NULL) {
            $params['imgs'] = array(
                'add'=>array(),
                'delete'=>array(),
            );
        } else {
            $params['imgs'] = json_decode($params['imgs'], true);
        }
        if (!isset($params['work_id'])|| !isset($params['type']) || !isset($params['inquire_result']) || !isset($params['item_instance_id'])|| !isset($params['inquire_description'])) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->inquire($user_id, $params);   //征信的具体逻辑
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);   
        }
        return $rs;
    }
    /**
     * 申请录入
     * @param type                  1为提交，2为暂存，3为退件，必传
     * @param work_id               工作id，必传
     * @param item_instance_id      任务实例id，必传
     * @param inquire_result        征信结果：正常、不正常，必传
     * @param inquire_description   征信备注  
     * @param imgs                  影像件资料 
     */
    public function inputrequest()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   
        if (!isset($params['imgs']) || $params['imgs'] == NULL) {
            $params['imgs'] = array(
                'add'=>array(),
                'delete'=>array(),
            );
        } else {
            $params['imgs'] = json_decode($params['imgs'], true);
        }
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])|| !isset($params['type'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->inputrequest($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 销售的补件提交
     * @param type                  1为提交，2为暂存，3为退件，必传
     * @param work_id               工作id，必传
     * @param item_instance_id      任务实例id，必传
     * @param inquire_result        征信结果：正常、不正常，必传
     * @param inquire_description   征信备注  
     * @param imgs                  影像件资料 
     */
    public function salessupplement()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   
        if (!isset($params['imgs']) || $params['imgs'] == NULL) {
            $params['imgs'] = array(
                'add'=>array(),
                'delete'=>array(),
            );
        } else {
            $params['imgs'] = json_decode($params['imgs'], true);
        }
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])|| !isset($params['type'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->salessupplement($params, $user_id);  //销售补件的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 人工审批
     * @param type                  1为提交，2为暂存，4取消认领 必传
     * @param artificial            1为一审，2为二审
     * @param call_status           电核结果 1为通过，2为拒绝，3为补件
     * @param artificial_status     人工审批结果 1为通过，2为拒绝，3为补件
     * @param work_id               工作id，必传
     * @param item_instance_id      任务实例id，必传
     * @param visiter_supplement    家访补件（ (逗号分割) 1、身份证，2、收入证明,3、征信授权书
     * @param salesman_supplement   销售补件 (逗号分割) 1、身份证，2、收入证明,3、征信授权书
     * @param call_refuse_reason    电核拒绝原因 (逗号分割)1、客户否认申请，2、非本人签名，3、申请人主动取消申请，4、黑名单，5、人行征信有不良记录，6、申请人不配合调查，7、公安网信息有误，8、无法联系申请人，9、其他
     * @param call_description      电核备注
     * @param artificial_description人工审批备注
     */
    public function artificial()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        $params['artificial'] =  Request::input('artificial', '1');  //1为一审，2为二审 默认一审
        // $params['visiter_supplement'] =  Request::input('visiter_supplement', '');  //家访补件（ (逗号分割)）1、身份证，2、收入证明,3、征信授权书
        // $params['salesman_supplement'] =  Request::input('salesman_supplement', '');  //销售补件 (逗号分割)1、身份证，2、收入证明,3、征信授权书
        $params['artificial_refuse_reason'] =  Request::input('artificial_refuse_reason', '');  //人工审批1拒绝原因 (逗号分割)1、客户否认申请，2、非本人签名，3、申请人主动取消申请，4、黑名单，5、人行征信有不良记录，6、申请人不配合调查，7、公安网信息有误，8、无法联系申请人，9、其他
        // $params['call_description'] =  Request::input('call_description', '');  //电核备注
        $params['artificial_description'] =  Request::input('artificial_description', '');   //人工审批1备注
        $params['artificialtwo_refuse_reason'] =  Request::input('artificialtwo_refuse_reason', '');  //人工审批2拒绝原因 (逗号分割)1、客户否认申请，2、非本人签名，3、申请人主动取消申请，4、黑名单，5、人行征信有不良记录，6、申请人不配合调查，7、公安网信息有误，8、无法联系申请人，9、其他
        $params['artificialtwo_description'] =  Request::input('artificialtwo_description', '');   //人工审批2备注
        if (!isset($params['work_id']) || !isset($params['item_instance_id']) ) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->artificial($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 申请打款
     * @param work_id                   工作id，必传
     * @param item_instance_id          任务实例id，必传
     * @param constract_prize           合同金额
     * @param remittance_prize          打款金额
     * @param remittance_man            打款人
     * @param remittance_time           打款时间
     * @param remittance_card           收款账户
     * @param finance_description       财务打款备注
     */
    public function applyremittance()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        // $params['loan_principal'] =  Request::input('loan_principal', '');  //本金
        $params['finance_name'] =  Request::input('finance_name', '');  //打款户名
        $params['finance_driving'] =  Request::input('finance_driving', '');  //打给车行
        $params['finance_account'] =  Request::input('finance_account', '');  //账号
        $params['finance_deposit_bank'] =  Request::input('finance_deposit_bank', '');  //开户行
        // $params['finance_amount'] =  Request::input('finance_amount', '');   //打款金额
        $params['finance_date'] =  Request::input('finance_date', '');   //打款日期
        $params['finance_apply_description'] =  Request::input('finance_apply_description', '');   //申请打款备注
        $params['return_car_rate'] =  Request::input('return_car_rate', '');  //返车行利率
        $params['return_car_principal'] =  Request::input('return_car_principal', '');  //返车行本金
        $params['car_final_pay'] =  Request::input('car_final_pay', '');  //车商尾款
        $params['remittance_total_money'] =  Request::input('remittance_total_money', '');  //合计打款
        $params['gross_profit_rate'] =  Request::input('gross_profit_rate', '');  //毛利率
        $params['integration_principal'] =  Request::input('integration_principal', '');  //融入本金
        $params['bank_lending'] =  Request::input('bank_lending', '');  //银行放款额
        $params['car_name'] =  Request::input('car_name', '');  //车行全称
        $params['business_place'] =  Request::input('business_place', '');  //业务发生地
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['finance_name']=='' || $params['finance_driving']=='' || $params['finance_account']=='' || $params['finance_deposit_bank']=='' || $params['finance_date']=='' || $params['return_car_rate'] =='' || $params['return_car_principal']=='' || $params['remittance_total_money']=='' || $params['gross_profit_rate']=='' || $params['bank_lending']=='' || $params['car_name']=='' || $params['business_place']=='' || $params['integration_principal']=='') {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->applyremittance($params, $user_id);  //申请打款的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 打款审核
     * @param work_id                   工作id，必传
     * @param item_instance_id          任务实例id，必传
     * @param moneyaudit_status         打款审核状态

     */
    public function moneyaudit()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        if (!isset($params['work_id']) || !isset($params['item_instance_id']) || !isset($params['moneyaudit_status'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->moneyaudit($params, $user_id);  //打款审核
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 财务打款
     * @param work_id                   工作id，必传
     * @param item_instance_id          任务实例id，必传
     * @param constract_prize           合同金额
     * @param remittance_prize          打款金额
     * @param remittance_man            打款人
     * @param remittance_time           打款时间
     * @param remittance_card           收款账户
     * @param finance_description       财务打款备注
     */
    public function finance()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        $params['constract_prize'] =  Request::input('constract_prize', '');  //合同金额
        $params['remittance_prize'] =  Request::input('remittance_prize', '');  //打款金额
        $params['remittance_man'] =  Request::input('remittance_man', '');  //打款人
        $params['remittance_time'] =  Request::input('remittance_time', '');  //打款时间
        $params['remittance_card'] =  Request::input('remittance_card', '');  //收款账户
        $params['finance_description'] =  Request::input('finance_description', '');   //财务打款备注
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['remittance_time'] == '' || $params['remittance_prize'] =='') {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->finance($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 回款确认
     * @param work_id                   工作id，必传
     * @param item_instance_id          任务实例id，必传
     * @param return_prize              回款金额
     * @param return_time               回款时间
     * @param return_card               回款账户
     * @param return_confirm_time       回款确认时间
     * @param return_description        回款备注
     */
    public function returnmoney()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        $params['return_prize'] =  Request::input('return_prize', '');  //回款金额
        $params['return_time'] =  Request::input('return_time', '');  //回款时间
        $params['return_card'] =  Request::input('return_card', '');  //回款账户
        $params['return_confirm_time'] =  Request::input('return_confirm_time', '');  //回款确认时间
        $params['return_description'] =  Request::input('return_description', '');  //回款备注
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['return_prize'] == '' || $params['return_time'] =='' || $params['return_card'] =='') {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->returnmoney($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 寄件登记
     * @param work_id                    工作id，必传
     * @param item_instance_id           任务实例id，必传
     * @param courier_man                寄件人
     * @param courier_business           快递商
     * @param courier_number             快递单号
     * @param courier_time               寄件时间
     * @param courier_description        寄件备注
     */
    public function courier()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        $params['courier_man'] =  Request::input('courier_man', '');  //寄件人
        $params['courier_business'] =  Request::input('courier_business', '');  //快递商
        $params['courier_number'] =  Request::input('courier_number', '');  //快递单号
        $params['courier_time'] =  Request::input('courier_time', '');  //寄件时间
        $params['courier_description'] =  Request::input('courier_description', '');  //寄件备注
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['courier_man'] == '' && $params['courier_business'] =='' && $params['courier_number'] =='' && $params['courier_time'] =='' && $params['courier_description'] == '') {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->courier($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 抄单登记
     * @param work_id                             工作id，必传
     * @param item_instance_id                    任务实例id，必传
     * @param copytask_courier_man                抄单寄件人
     * @param copytask_courier_business           抄单快递商
     * @param copytask_courier_number             抄单快递单号
     * @param copytask_courier_time               抄单寄件时间
     * @param copytask_courier_description        抄单寄件备注
     */
    public function copytask()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        $params['copytask_courier_man'] =  Request::input('copytask_courier_man', '');  //抄单寄件人
        $params['copytask_courier_business'] =  Request::input('copytask_courier_business', '');  //抄单快递商
        $params['copytask_courier_number'] =  Request::input('copytask_courier_number', '');  //抄单快递单号
        $params['copytask_courier_time'] =  Request::input('copytask_courier_time', '');  //抄单寄件时间
        $params['copytask_courier_description'] =  Request::input('copytask_courier_description', '');  //抄单寄件备注
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['copytask_courier_man'] == '' || $params['copytask_courier_time'] =='') {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->copytask($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 车辆gps登记
     * @param work_id                            工作id，必传
     * @param item_instance_id                   任务实例id，必传
     * @param license_number                     车牌号
     * @param gps_number                         GPS编号
     * @param install_man                        安装人
     * @param install_time                       安装时间
     * @param gps_description                    gps登记备注
     */
    public function gps()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        $params['license_number'] =  Request::input('license_number', '');  //车牌号
        $params['gps_number'] =  Request::input('gps_number', '');  //GPS编号
        $params['gps_number2'] =  Request::input('gps_number2', '');  //GPS编号2
        $params['install_man'] =  Request::input('install_man', '');  //安装人
        $params['install_time'] =  Request::input('install_time', '');  //安装时间
        $params['gps_description'] =  Request::input('gps_description', '');  //gps登记备注
        $params['install_position'] =  Request::input('install_position', '');  //安装位置
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['install_time'] == '' || $params['install_position'] =='' || ($params['gps_number'] ==''&& $params['gps_number2'] =='')){
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try{
            $rs = $this->_workplatform->gps($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 抵押登记
     * @param work_id                            工作id，必传
     * @param item_instance_id                   任务实例id，必传
     * @param is_mortgage                        是否办理抵押
     * @param mandate_number                     委托书编号
     * @param transactor                         办理人
     * @param transact_time                      办理时间
     * @param mortgage_description               抵押备注
     */
    public function mortgage()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        $params['is_mortgage'] =  Request::input('is_mortgage', '');  //是否办理抵押
        $params['mandate_number'] =  Request::input('mandate_number', '');  //委托书编号
        $params['AmortNum'] =  Request::input('AmortNum', '');  //抵押权证号
        $params['transactor'] =  Request::input('transactor', '');  //办理人
        $params['transact_time'] =  Request::input('transact_time', '');  //办理时间
        $params['mortgage_description'] =  Request::input('mortgage_description', '');  //抵押备注
        $params['car_vehicle_identification_number'] = Request::input('car_vehicle_identification_number', '');  //车架号
        $params['car_number'] = Request::input('car_number', '');  //车牌号
        if (!isset($params['work_id']) || !isset($params['item_instance_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['mandate_number'] == '' || $params['transact_time'] =='' || $params['car_vehicle_identification_number'] ==''){
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if(!preg_match("/^[a-zA-Z0-9]{17}$/",$params['car_vehicle_identification_number'])){  
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '车架号必须为17位的数字和英文');    //车架号必须为17位的数字和英文
        }
        try{
            $rs = $this->_workplatform->mortgage($params, $user_id);  //申请录入的请求
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    /**
     * 列举任务流
     * @param work_id    工作id，必传
     */
    public function listtasks()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        if (!isset($params['work_id'])) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        $params['process_id'] = '1';
        $rs = $this->_workplatform->listtasks($params);  //申请录入的请求
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }


//添加需要向银行上传资料的队列
    public function transtobank()
    {
        if (env('IFCAR_BANK') !== 'ON') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '测试中，暂不上传数据');    //如果返回的是false
        }
        Log::info('接受到请求');
        $this->_common->setlog();
        $params['work_id'] = Request::input('work_id', '');   //工作id
        // file_put_contents(FCPATH . 'test.txt', $params['work_id'] . date('Ymd H:i:s'), FILE_APPEND);exit;
        if ($params['work_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //上传至银行服务器
        $rs = $this->_workplatform->transtobank($params);   //认领征信的具体逻辑
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    } 
    public function transtozhaohuibank()
    {
        if (env('IFCAR_ZHAOHUI_BANK') !== 'ON') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '测试中，暂不上传数据');    //如果返回的是false
        }
        $this->_common->setlog();
        $params['work_id'] = Request::input('work_id', '');   //工作id
        if ($params['work_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //上传至银行服务器
        $rs = $this->_workplatform->transtozhaohuibank($params);   //认领征信的具体逻辑
        return $rs;
    }
    //补录接口
    public function suppletobank()
    {
        if (env('IFCAR_BANK') !== 'ON') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '测试中，暂不上传数据');    //如果返回的是false
        }
        Log::info('接受到请求');
//        return $this->_common->output(false, Constant::ERR_FAILED_NO, '测试中，暂不上传数据');    //如果返回的是false
        $this->_common->setlog();
        $params['work_id'] = Request::input('work_id', '');   //工作id
        // file_put_contents(FCPATH . 'test.txt', $params['work_id'] . date('Ymd H:i:s'), FILE_APPEND);exit;
        if ($params['work_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //上传至银行服务器
        $rs = $this->_workplatform->suppletobank($params);   //认领征信的具体逻辑
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    public function suppletozhaohuibank()
    {
        if (env('IFCAR_ZHAOHUI_BANK') !== 'ON') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '测试中，暂不上传数据');    //如果返回的是false
        }
        $this->_common->setlog();
        $params['work_id'] = Request::input('work_id', '');   //工作id
        if ($params['work_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //上传至银行服务器
        $rs = $this->_workplatform->suppletozhaohuibank($params);   //认领征信的具体逻辑
        return $rs;
    }
    //添加需要向银行上传资料的队列
    public function retranstobank()
    {
        Log::info('接受到请求');
//        return $this->_common->output(false, Constant::ERR_FAILED_NO, '测试中，暂不上传数据');    //如果返回的是false
        $this->_common->setlog();
        $params['work_id'] = Request::input('work_id', '');   //工作id
        // file_put_contents(FCPATH . 'test.txt', $params['work_id'] . date('Ymd H:i:s'), FILE_APPEND);exit;
        if ($params['work_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //上传至银行服务器
        $rs = $this->_workplatform->retranstobank($params);   //认领征信的具体逻辑
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    } 
    public function listsubordinatetask()
    {
        $this->_common->setlog();
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['subordinate_userid'] =  Request::input('subordinate_userid', '');  //下属用户id
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        try{
            $rs = $this->_workplatform->listsubordinatetask($user_id, $params);   //认领征信的具体逻辑
        } catch(\Exception $e) { 
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG); 
        }
        return $rs;
    }
    public function getservice()
    {
        $params['type'] =  Request::input('type', '0'); //1:业务员APP，2:家访APP，0：两个都要 
        $rs = $this->_workplatform->getservice($params);
        return $rs;
    }
    //删除申请件
    public function endwork()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $rs = $this->_workplatform->endwork($user_id, $params);
        return $rs;
    }
    //修改申请件
    public function editwork()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params =  Request::all();   //传参
        if (!isset($params['imgs']) || $params['imgs'] == NULL) {
            $params['imgs'] = array(
                'add'=>array(),
                'delete'=>array(),
            );
        } else {
            $params['imgs'] = json_decode($params['imgs'], true);
        }
        $params['work_id'] = Request::input('work_id', '');   //工作id
        if ($params['work_id'] == '') {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        $rs = $this->_workplatform->editwork($user_id, $params);
        return $rs;
    }
    public function beforedata()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['user_id'] = $user_id;
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $inputs = Request::all();
        $rs['rows'] = $this->_workplatform->beforedata($params, $inputs);
        $count = $this->_workplatform->beforedatacount($params, $inputs);
        $rs['total'] = $count['count'];
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
//获取逾期列表
    public function getyuqi()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $params['request_no'] = Request::input('request_no', '');  //申请编号
        $params['customer_name'] = Request::input('customer_name', '');  //客户姓名
        $params['product_name'] =  Request::input('product_name', '');
        $params['customer_certificate_number'] =  Request::input('customer_certificate_number', '');
        $rs['rows'] = $this->_workplatform->getyuqi($user_id, $params);
        $count = $this->_workplatform->getyuqicount($user_id, $params);
        $rs['total'] = $count['count'];
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }
    //获取还款列表
    public function gethuankuan()
    {
        //获取用户id
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $params['request_no'] = Request::input('request_no', '');  //申请编号
        $params['customer_name'] = Request::input('customer_name', '');  //客户姓名
        $params['product_name'] =  Request::input('product_name', '');
        $params['customer_certificate_number'] =  Request::input('customer_certificate_number', '');
        $rs['rows'] = $this->_workplatform->gethuankuan($user_id, $params);
        $count = $this->_workplatform->gethuankuancount($user_id, $params);
        $rs['total'] = $count['count'];
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }
}