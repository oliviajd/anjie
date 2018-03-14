<?php

namespace App\Http\Controllers;
use App\Http\Models\business\Workflow;
use App\Http\Models\table\Anjie_file;
use App\Http\Models\table\Anjie_push_log;
use App\Http\Models\table\Anjie_work;
use App\Http\Models\table\File_upload;
use League\Flysystem\Exception;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Auth;
use App\Http\Models\business\Role;
use Illuminate\Support\Facades\Log;
//主要用于展示静态页面
class WorkflowController extends Controller
{ 
    private $_auth = null;
    private $_work = null;

    public function __construct()
    {
        parent::__construct();
        $this->_workflow = new Workflow();
        $this->_auth = new Auth();
        $this->_role = new Role();
    }
//给引擎平台的家访和补件接口
    public function workflowvisit()
    {
        $this->_common->setlog();
        $params = Request::all();
        if ($params['work_id'] == NULL || $params['task_instance_id'] == NULL|| $params['type'] == NULL) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);  //传参不能为空
        }
        if ($params['type'] == '1') {                         
            $rs = $this->_workflow->workflowvisit($params);    //传参为1则是家访
        } elseif ($params['type'] == '2') {
            $rs = $this->_workflow->workflowsupplement($params);   //传参为2则为补件
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '提交的类型不对');    //传参目前type只能为1或2
        }
        return $rs;
    }
//引擎平台修改某个件的任务状态
    public function workflowitem()
    {
        $this->_common->setlog();
        $params['work_id'] =  Request::input('work_id', ''); //工作id
        $params['current_item_id'] =  Request::input('current_item_id', '');  //当前的任务
        $params['process_id'] =  Request::input('process_id', '1');  //当前的任务
        $params['process_instance_id'] =  Request::input('process_instance_id', '');  //当前的任务
        $params['item_instance_id'] =  Request::input('item_instance_id', '');  //当前的任务
        if ($params['work_id'] == '' || $params['current_item_id'] == '') {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);  //传参不能为空
        }
        $rs = $this->_workflow->workflowitem($params);  //引擎平台修改某个件的任务状态逻辑
        if ($rs !== false) {
            return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
//查询家访任务列表
    public function lists()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $params['haschild'] = $this->_auth->haschild($user_id);   //判断是否有下属 , 1为有下属，2为无下属
        $params['type'] = Request::input('type', '0');   //0查全部，1为查待认领，2为查已认领，3为查待家访，4为查待补件，5为查历史件，6查待分派， 7为已分派，默认查全部    //待分派等于待认领，所以1==6
        $params['date'] = Request::input('date', '');   //需要查询的日期，不传则默认查全部
        $params['product_class_number'] = Request::input('carclass', '0');   //车辆类型 产品类别编号,XC:新车，ES:二手车，不传为查全部
        $params['keyword'] = Request::input('keyword', '');   //关键词
        $params['order'] = Request::input('order', 'create_time');   //排序依据
        $worklist = $this->_workflow->lists($params, $user_id);  //查询家访任务列表
        return $worklist;
    }
    //查询工作详情
    public function getworkinfo()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $workid = Request::input('work_id', '');   //工作id
        $worklist = $this->_workflow->getworkinfobyid($workid);  //根据id查工作信息
        if ($worklist !== false) {
            return $this->_common->output($worklist, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    //查询工作对应日期数量
    public function getaccountlist()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $params['startdate'] =  Request::input('strartdate', '');   //开始日期，包含本身，日期格式，如20170608
        $params['enddate'] =  Request::input('enddate', '');   //结束日期，包含本身，日期格式，如20170608
        $accountlist = $this->_workflow->getaccountlist($user_id, $params);
        if ($accountlist !== false) {
            return $this->_common->output($accountlist, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
  //家访中的认领任务逻辑
    public function pickupvisit()
    {
        $this->_common->setlog();
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $params['work_id'] = Request::input('work_id', ''); //工作id
        $params['visit_date'] = Request::input('visit_date', ''); //家访日期
        $rs = $this->_workflow->pickupvisit($user_id, $params);   //认领任务的逻辑
        return $rs;
    }
    //家访中的退回任务逻辑，退回任务实际是将所有该work_id的件has_pickup=2,has_assgin=2,然后删掉当前这个用户的记录
    public function backvisit()
    {
        $this->_common->setlog();
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $param['work_id'] = Request::input('work_id', ''); //工作id
        $param['description'] = Request::input('visit_back_description', ''); //家访退回意见
        $rs = $this->_workflow->backvisit($user_id, $param);   //认领任务的逻辑
        return $rs;
    }
    //家访中的分派逻辑
    public function assignvisit()
    {
        $this->_common->setlog();
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $param['work_id'] = Request::input('work_id', ''); //工作id
        $param['subordinate_userid'] = Request::input('subordinate_userid', ''); //下属的userid
        $rs = $this->_workflow->assignvisit($user_id, $param);  //分派家访的逻辑
        return $rs;
    }
    //家访中的拒件逻辑
    public function refusevisit()
    {
        $this->_common->setlog();
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $param['work_id'] = Request::input('work_id', ''); //工作id
        $param['description'] = Request::input('refuse_description', ''); //拒件意见
        $rs = $this->_workflow->refusevisit($user_id, $param);   //拒件的逻辑
        return $rs;
    }
    //完成补件任务
    public function completesupplement()
    {
        $this->_common->setlog();
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $param['work_id'] = Request::input('work_id', ''); //工作id
        $rs = $this->_workflow->completesupplement($user_id, $param);   //完成补件任务
        return $rs;
    }
    //根据user_id获取家访的消息列表
    public function listmessage()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '100'));  //每一页的数量
        $rs = $this->_workflow->listmessage($user_id, $params);   //列出所有的消息，并将所有未读消息设置为已读
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    //APP里面的销售的征信列表查询接口
    public function listinquire()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);  //用户id
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $params['page'] =  intval(Request::input('page', '1'));   //第几页
        $params['size'] =  intval(Request::input('size', '20'));  //每一页的数量
        $params['type'] =  Request::input('type', '1');   //0查全部，1查待征信，2查已征信
        $params['keyword'] = Request::input('keyword', '');   //关键词
        $params['role_id'] = '80';
        $params['user_id'] = $user_id;
        $rs = $this->_workflow->listinquire($user_id, $params);   //列出所有的消息，并将所有未读消息设置为已读
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }
    public function giveup()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);  //用户id
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        $rs = $this->_workflow->giveup($user_id, $params);
        return $rs;
    }

    public function visitgiveup()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);  //用户id
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $rs = $this->_workflow->visitgiveup($user_id, $params);
        return $rs;
    }

    /**
     * 面签视频处理接口
     * @return string
     */
    public function facevideo(){
        $identityNumber = Request::input('identity');
        $path = Request::input('path');
        $file = Request::input('file');
        Log::Info('收到面签视频通知接口数据，identity='.$identityNumber.',path='.$path.'file='.$file);
        if($identityNumber == '' || $path == '' || $file == ''){
            Log::info('视频面签通知中，身份证号或路径或文件id为空，处理失败');
            return $this->_common->output('',Constant::ERR_FILED_NECESSARY_NO,Constant::ERR_FILED_NECESSARY_MSG);
        }
        $workModel = new Anjie_work();
        $workInfo = $workModel->where(['customer_certificate_number'=>$identityNumber])->orderBy('id','desc')->first();
        if(!$workInfo){
            Log::info('视频面签通知时，没有查询到身份证号码='.$identityNumber.'相关的数据，处理失败');
            return $this->_common->output('',Constant::ERR_ITEM_NOT_EXISTS_NO,Constant::ERR_ITEM_NOT_EXISTS_MSG);
        }
        $fileId = explode('.',$file);
        //anjie_file表
        $work_id = $workInfo->id;
        $fileInsert['work_id'] = $work_id;
        $fileInsert['file_class_id'] = 17;
        $fileInsert['file_type'] = 2;
        $fileInsert['file_type_name'] = 'video';
        $fileInsert['file_path'] = $path.$file;
        $fileInsert['status'] = 1;
        $fileInsert['create_time'] = time();
        $fileInsert['add_userid'] = 0;
        $fileInsert['file_id'] = $fileId[0];
        $fileInsert['upload_status'] = 0;
        $fileInsert['type'] = 1;
        $fileInsert['need_thumb'] = 1;
        //file_upload表
        $fileUpload['path'] = $path;
        $fileUpload['file_token'] = md5($path) . microtime() . rand(10000, 99999);
        $fileUpload['token_expired_in'] = time() + 3600 * 4 * 7;
        $fileUpload['sync_to_upyun_status'] = 0;
        $fileUpload['upyun_uploads_offset'] = 0;
        $fileUpload['sync_to_upyun'] = 1;
        $fileUpload['user_id'] = 0;
        $fileUpload['create_time'] = time();
        $fileUpload['size'] = 0;
        $fileUpload['suffix'] = $fileId[1];
        $fileUpload['file_id'] = $fileId[0];
        $fileUpload['type'] = 'video';
        $fileUpload['sync_to_bank'] = 2;
        $fileUpload['sync_to_bank_status'] = 0;
        $fileUploadModel = new File_upload();
        $anjieFileModel = new Anjie_file();
        $this->_pdo->beginTransaction();
        try{
            $fileUploadModel->insertGetId($fileUpload);
            $anjieFileModel->insertGetId($fileInsert);
            $this->_pdo->commit();
            return $this->_common->output(true,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
        }catch (Exception $e){
            Log::info('视频面签通知插入数据库失败:'.$e->getMessage());
            return $this->_common->output('',Constant::ERR_FAILED_NO,Constant::ERR_FAILED_DATA_MSG);
        }
    }

    /**
     * 获取销售员推送列表
     */
    public function sellermessage(){
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $page = Request::input('page',1);
        $pageSize = Request::input('pagesize',10);
        $offset = ($page-1)*$page;
        if(!is_numeric($user_id) || $user_id <= 0){
           return $this->_common->output('',Constant::ERR_FAILED_NO,Constant::ERR_FAILED_MSG);
        }
        $pushLogModel = new Anjie_push_log();
        $where = ['user_id'=>$user_id];
        $whereNotIn = ['submit','allot'];
        $list = $pushLogModel->where($where)->whereNotIn('push_action',$whereNotIn)->select(['title','result','add_time'])->orderBy('add_time','desc')->offset($offset)->limit($pageSize)->get();
        $count = $pushLogModel->where($where)->whereNotIn('push_action',$whereNotIn)->count();
        $data['data'] = [];
        $data['count'] = 0;
        if($list){
            $list = $list->toArray();
            foreach($list as &$v){
                $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            }
            $data['data'] = $list;
            $data['count'] = $count;
        }
        return $this->_common->output($data, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }


    /**
     * 获取家访推送列表
     */
    public function homemessage(){
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $page = Request::input('page',1);
        $pageSize = Request::input('pagesize',10);
        $offset = ($page-1)*$page;
        if(!is_numeric($user_id) || $user_id <= 0){
            return $this->_common->output('',Constant::ERR_FAILED_NO,Constant::ERR_FAILED_MSG);
        }
        $pushLogModel = new Anjie_push_log();
        $where = ['user_id'=>$user_id];
        $whereIn = ['submit','allot'];
        $list = $pushLogModel->where($where)->whereIn('push_action',$whereIn)->select(['title','result','add_time'])->orderBy('add_time','desc')->offset($offset)->limit($pageSize)->get();
        $count = $pushLogModel->where($where)->whereIn('push_action',$whereIn)->count();
        $data['data'] = [];
        $data['count'] = 0;
        if($list){
            $list = $list->toArray();
            foreach($list as &$v){
                $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            }
            $data['data'] = $list;
            $data['count'] = $count;
        }
        return $this->_common->output($data, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function getrolelist()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $rolelist = $this->_workflow->getrolelist();
        return $this->_common->output($rolelist, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }


}