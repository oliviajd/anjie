<?php

namespace App\Http\Controllers;
use App\Http\Models\business\Business;
use App\Http\Models\business\Auth;
use Request;
use DB;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
// use App\Http\Models\common\ProcessInstance;
// use App\Http\Models\common\TaskInstance;
// use App\Http\Models\common\UserTask;
date_default_timezone_set('PRC');

class BusinessController extends Controller
{   
    private $_auth = null;
    // protected $_process_instance = null;
    // protected $_task_instance = null;
    // protected $_user_task = null;
    /**
     * 定义业务对象
     */
    public function __construct()
    {
        parent::__construct();
        $this->_business = new Business();
        $this->_auth = new Auth();
        // $this->_process_instance = new ProcessInstance();
        // $this->_task_instance = new TaskInstance();
        // $this->_user_task = new UserTask();
    }
    /**
     * 业务申请列表展示页
     */
    public function index()
    {
        return view('admin.business.index')->with('title', '业务申请');
    }
    /**
     * 添加业务申请页
     */
    public function addbusiness()
    {
        return view('admin.business.addbusiness')->with('title', '添加业务申请');
    }
    /**
     * 编辑业务申请页
     * @param int $customer_id  客户ID
     * @return info  客户交易表信息.
     * @return path  域名.
     */
    public function editbusiness()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $customer_id = Request::input('customer_id', '');
        $info = $this->_business->getBusinessinfoById($customer_id);  //获取业务申请信息By客户id
        return view('admin.business.editbusiness', ['info'=>$info, 'path'=>$path])->with('title', '修改业务申请');
    }
    /**
     * 查看业务申请详情页
     * @param int $customer_id  客户ID
     * @return info  客户交易表信息.
     * @return path  域名.
     */
    public function detailbusiness()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $customer_id = Request::input('customer_id', '');
        $info = $this->_business->getBusinessinfoById($customer_id);  //获取业务申请信息By客户id
        return view('admin.business.detailbusiness', ['info'=>$info, 'path'=>$path])->with('title', '查看业务申请');
    }
    /**
     * 征信查询列表展示页
     */
    public function inquire()
    {
        return view('admin.inquire.index')->with('title', '征信工作区');
    }
    /**
     * 编辑征信查询，提交征信意见页面
     * @param int $customer_id  客户ID
     * @return info  客户交易表信息.
     * @return path  域名.
     * @return salesman  业务员用户表中信息.
     * 
     */
    public function editinquire()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $customer_id = Request::input('customer_id', '');
        $info = $this->_business->getBusinessinfoById($customer_id); //获取业务申请信息By客户id
        $salesman = $this->_business->getUserInfobyId($info['salesman_userid']); //获取业务员信息By业务员用户id
        return view('admin.inquire.editinquire', ['info'=>$info, 'path'=>$path, 'salesman'=>$salesman])->with('title', '修改征信查询意见');
    }
    /**
     * 征信查询详情页
     * @param int $customer_id  客户ID
     * @return info  客户交易表信息.
     * @return path  域名.
     * @return salesman  业务员用户表中信息.
     */
    public function detailinquire()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $customer_id = Request::input('customer_id', '');
        $info = $this->_business->getBusinessinfoById($customer_id); //获取业务申请信息By客户id
        $salesman = $this->_business->getUserInfobyId($info['salesman_userid']); //获取业务员信息By业务员用户id
        return view('admin.inquire.detailinquire', ['info'=>$info, 'path'=>$path, 'salesman'=>$salesman])->with('title', '显示征信查询意见');
    }
    /**
     * 家访签约列表展示页
     * @return visiter  家访员.
     */
    public function visit()
    {
        $visiter = $this->_business->getvisiter(); //获取家访员列表
        return view('admin.visit.index', ['visiter'=> $visiter])->with('title', '家访分派工作区');
    }
    /**
     * 家访员详情页
     * @return visiter  家访员.
     */
    public function detailvisit()
    {
        $customer_id = Request::input('customer_userid', '');
        $result['info'] = $this->_business->getBusinessinfoById($customer_id); //获取业务申请信息By客户id
        $result['salesman'] = $this->_business->getUserInfobyId($result['info']['salesman_userid']); //获取业务员信息By业务员用户id
        $result['home_visitman'] = $this->_business->getUserInfobyId($result['info']['home_visitman_userid']); //获取家访员信息
        return view('admin.visit.detailvisit',['visit'=>$result])->with('title', '家访详情页');
    }
    /**
     * 贷款档案展示页
     */
    public function loan()
    {
        return view('admin.loan.index')->with('title', '贷款档案');
    }
    /**
     * 补充地址
     */
    public function addaddress()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $customer_id = Request::input('customer_id', '');
        $info = $this->_business->getBusinessinfoById($customer_id);  //获取业务申请信息By客户id
        return view('admin.business.addaddress', ['info'=>$info, 'path'=>$path])->with('title', '补充地址');
    }
    /**
     * 提交审核
     */
    public function submitaudit()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $customer_id = Request::input('customer_id', '');
        $info = $this->_business->getBusinessinfoById($customer_id);  //获取业务申请信息By客户id
        return view('admin.business.submitaudit', ['info'=>$info, 'path'=>$path])->with('title', '提交审核');
    }
    /**
     * 已处理详情页
     */
    public function detailhandled()
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $customer_id = Request::input('customer_id', '');
        $info = $this->_business->getBusinessinfoById($customer_id);  //获取业务申请信息By客户id
        return view('admin.business.detailhandled', ['info'=>$info, 'path'=>$path])->with('title', '已处理');
    }
    /**
     * 补充地址
     */
    public function addaddresspost()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $user = Request::all();
        if ($user['customer_name'] == NULL || $user['customer_telephone'] == NULL|| $user['customer_address'] == NULL|| $user['customer_identity_card_number'] == NULL || !isset($user['identity_card_full_face'])|| !isset($user['identity_card_reverse_side']) || !isset($user['sign_authorized']) || !isset($user['authorized'])) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($user['customer_marital_status'] == '1' && (!isset($user['spouse_identity_card_full_face']) || !isset($user['spouse_identity_card_reverse_side'])|| !isset($user['spouse_sign_authorized']) || !isset($user['spouse_authorized']))) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        return $this->_business->addaddress($user, $user_id); //修改业务信息

    }
    /**
     * 提交审核
     */
    public function submitauditpost()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $user = Request::all();
        if ($user['customer_telephone'] == NULL || $user['customer_address'] == NULL ||$user['customer_company_name'] == NULL || $user['customer_company_address'] == NULL || $user['car_type'] == NULL || $user['loan_prize'] == NULL || $user['car_prize'] == NULL ||  $user['first_pay'] == NULL ||$user['pay_percentage'] == NULL ||$user['loan_rate'] == NULL) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($user['customer_marital_status'] == '1' && ($user['spouse_name'] == NULL ||  $user['spouse_identity_card_number'] == NULL ||$user['spouse_telephone'] == NULL ||$user['spouse_address'] == NULL||$user['spouse_company_name'] == NULL||$user['spouse_company_address'] == NULL)) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        return $this->_business->submitaudit($user, $user_id);  //提交审核
    }
    /**
     * 添加业务申请
     * @param user 新添加的客户个人信息
     * @return object errorcode为0的时候，添加成功.
     */
    public function addbusinesspost()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $user = Request::all();
        if ($user['customer_name'] == NULL || $user['customer_identity_card_number'] == NULL || !isset($user['identity_card_full_face'])|| !isset($user['identity_card_reverse_side']) || !isset($user['sign_authorized']) || !isset($user['authorized'])) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($user['customer_marital_status'] == '1' && (!isset($user['spouse_identity_card_full_face']) || !isset($user['spouse_identity_card_reverse_side'])|| !isset($user['spouse_sign_authorized']) || !isset($user['spouse_authorized']))) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        return $this->_business->setBusinessInfo($user, $user_id);  //新增业务申请
    }
    /**
     * 列出业务员对应的业务信息
     * @param page 页码
     * @param size 每一页的条数
     * @param keyword 搜索时的关键词
     * @param condition 查询的条件
     * @param status 查询业务的状态，默认为0也就是全部，1为已提交，2为已通过，3为拒单
     * @param sort 排序 0为按状态更新时间降序排列，1为升序
     * @return object error_no为200时请求成功
     */
    public function listsalesmanbusiness()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $page =  intval(Request::input('page', '1'));
        $size =  intval(Request::input('size', '20'));
        $keyword = Request::input('keyword', ''); //查询的关键词
        $status = Request::input('status', '0');  //查询业务的状态，默认为0也就是全部
        $sort = Request::input('sort', '0');  //排序
        $visit_status = Request::input('visit_status', '0');  //查询业务的状态，默认为0也就是全部
        $check = Request::input('check', '0');  //搜索时是否需要包含审核人
        $order = 'id desc';
        $lists = $this->_business->listsbusinessByUserid($page, $size, $order, $keyword, $status, $sort, $visit_status, $check, $user_id); //列出申请列表的数据
        foreach ($lists as $key => $value) {
            $lists[$key]['time'] = date('Y/m/d H:i:s', $value['modify_time']);
        }
        $r['rows'] = $lists;
        $r['total'] = $this->_business->count($keyword, $status, $visit_status, $check, $user_id);  //获得申请列表的条数
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 列出所有业务申请信息
     * @param page 页码
     * @param size 每一页的条数
     * @param keyword 搜索时的关键词
     * @param condition 查询的条件
     * @param status 查询业务的状态，默认为0也就是全部，1为已提交，2为已通过，3为拒单
     * @param sort 排序 0为按状态更新时间降序排列，1为升序
     * @return object error_no为200时请求成功.
     */
    public function listsallbusiness()
    {
        $page =  intval(Request::input('page', '1'));
        $size =  intval(Request::input('size', '20'));
        $keyword = Request::input('keyword', ''); //查询的关键词
        $status = Request::input('status', '0');  //查询业务的状态，默认为0也就是全部
        $loan_status = Request::input('loan_status', '0');  //查询业务的状态，默认为0也就是全部
        $sort = Request::input('sort', '0');  //排序
        $visit_status = Request::input('visit_status', '0');  //查询业务的状态，默认为0也就是全部
        $check = Request::input('check', '0');  //搜索时是否需要包含审核人
        $order = 'id desc';
        $lists = $this->_business->listsbusinessByUserid($page, $size, $order, $keyword, $status, $sort, $visit_status, $loan_status, $check);//列出申请列表的数据
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        foreach ($lists as $key => $value) {
            $lists[$key]['time'] = date('Y/m/d H:i:s', $value['modify_time']);
            $lists[$key]['download'] = pathinfo($path . $value['credit_report'],PATHINFO_BASENAME );
        }
        $r['rows'] = $lists;
        $r['total'] = $this->_business->count($keyword, $status, $visit_status, $loan_status, $check);//获得申请列表的条数
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 修改业务申请
     * @param user 修改业务申请的客户信息
     * @return object errorcode为0时修改成功.
     */
    public function editbusinesspost()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $user = Request::all();
        if ($user['customer_name'] == NULL || $user['customer_identity_card_number'] == NULL || !isset($user['identity_card_full_face'])|| !isset($user['identity_card_reverse_side']) || !isset($user['sign_authorized']) || !isset($user['authorized'])) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($user['customer_marital_status'] == '1' && (!isset($user['spouse_identity_card_full_face']) || !isset($user['spouse_identity_card_reverse_side'])|| !isset($user['spouse_sign_authorized']) || !isset($user['spouse_authorized']))) {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        return $this->_business->editBusinessInfo($user, $user_id); //修改业务信息
    }
    /**
     * 修改业务申请状态（也就是征信查询）
     * @param status  修改后的状态，1为已提交，2为已通过，3为拒单
     * @param comments 征信查询意见
     * @param customer_id 被修改的客户的业务id
     * @return object errorcode为0时请求成功.
     */
    public function setstatus()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $status = Request::input('status', '');
        $comments = Request::input('comments', '');
        $customer_id = Request::input('customer_id', '');
        $credit_report = Request::input('credit_report', '');
        if ($comments == '') {
            $result['errorcode'] = '-1';
            $result['errormsg'] = '征信查询意见不能为空';
            return json_encode($result);
        }
        $rs = $this->_business->setStatus($status, $comments, $customer_id, $credit_report, $user_id); //设置征信查询状态
        if ($rs == true) {
            $rs = array(
                'user_id' => $user_id,
                'status' => $status,
                'comments' => $comments,
                'customer_id' => $customer_id,
                'credit_report' => $credit_report,
            );
        }
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 设置家访员
     * @param home_visit_checked  选择后的家访员id
     * @param account  被设置的客户的账号
     * @return object errorcode为0时请求成功.
     */
    public function checkvisitmanweb()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $home_visit_userid = Request::input('visitor_userid', '');
        $account = Request::input('account', '');
        return $this->_business->setVisitmanByAccount($home_visit_userid, $account, $user_id);
    }
    /**
     * app端根据user_id设置家访员
     * @param visitor_userid  选择后的家访员id
     * @param customer_userid  被设置的客户的id
     * @return object errorcode为0时请求成功.
     */
    public function checkvisitman()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $home_visit_userid = Request::input('visitor_userid', '');
        $customer_userid = Request::input('customer_userid', '');
        return $this->_business->setVisitmanByUserid($home_visit_userid, $customer_userid, $user_id);
    }
    /**
     * 设置家访员位置
     * @param lat 纬度
     * @param lng 经度
     * @param userid 用户ID
     */
    public function setvisitlocation()
    {
        $param['lat'] =  Request::input('lat', '');  //纬度
        $param['lng'] =  Request::input('lng', '');  //经度
        $param['userid'] = Request::input('userid', '');  //用户ID
        return $this->_business->setPoint($param);
    }
    /**
     * 拒绝
     */
    public function refusevisit()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $inputall = Request::all();
        if ($inputall['refuse_reason'] == '') {
            return $this->_common->output('', Constant::ERR_FILED_NECESSARY_NO, Constant::ERR_FILED_NECESSARY_MSG);
        }
        return $this->_business->refusevisit($inputall);
    }

    public function test()
    {
        $has_completed =  Request::input('has_completed', '');
        return $this->_business->test($has_completed);
    }

    public function pickup()
    {
        $token =  Request::input('token', '');
        $task_instance_id =  Request::input('task_instance_id', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $inputall = Request::all();
        if (!isset($inputall['task_instance_id'])) {
            return $this->_common->output('', Constant::ERR_FILED_NECESSARY_NO, Constant::ERR_FILED_NECESSARY_MSG);
        }
        $pickup_rs = $this->_common->object_array(json_decode($this->_user_task->pickup($user_id, $task_instance_id)));
        // var_dump($pickup_rs);
        if ($pickup_rs['error_no'] !== '0') {
            return $this->_common->output('', Constant::ERR_PICK_UP_NO, $task_instance_id . Constant::ERR_PICK_UP_MSG);
        }
        $rs = $this->_business->pickup($user_id, $task_instance_id, $pickup_rs['result']);
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function giveup()
    {
        $token =  Request::input('token', '');
        $task_instance_id =  Request::input('task_instance_id', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        if ($task_instance_id == '') {
            return $this->_common->output('', Constant::ERR_FILED_NECESSARY_NO, Constant::ERR_FILED_NECESSARY_MSG);
        }
        $giveup_rs = $this->_common->object_array(json_decode($this->_user_task->giveup($user_id, $task_instance_id)));
        var_dump($giveup_rs);
        if ($giveup_rs['error_no'] !== '0') {
            return $this->_common->output(false, Constant::ERR_GIVE_UP_NO, Constant::ERR_GIVE_UP_MSG);
        }
        $rs = $this->_business->giveup($task_instance_id); 
        if ($rs) {
            return $this->_common->output('', Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output($rs, Constant::ERR_GIVE_UP_NO, Constant::ERR_GIVE_UP_MSG);
        }
    }

    public function completetask()
    {
        $token =  Request::input('token', '');
        $task_instance_id =  Request::input('task_instance_id', '');
        $data =  Request::input('data', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        if ($task_instance_id == '') {
            return $this->_common->output('', Constant::ERR_FILED_NECESSARY_NO, Constant::ERR_FILED_NECESSARY_MSG);
        }
        $complete_rs = $this->_common->object_array(json_decode($this->_user_task->complete($user_id, $task_instance_id, $data)));
        var_dump($complete_rs);
        if ($complete_rs['error_no'] !== '0') {
            return $this->_common->output($complete_rs['result'], Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_COMPLETE_NO, Constant::ERR_COMPLETE_MSG);
        }
    }

    /**
     *  该角色已完成  :  has_complete=1 and has_all=1 and has_locked = 0
        该用户已完成  ： has_complete=1 and has_all=0 and has_locked = 0
        该角色待处理  ： has_complete=0 and has_all=1 and has_locked = 2
        该角色已被认领： has_complete=0 and has_all=1 and has_locked = 1
        该用户已认领  ： has_complete=0 and has_all=0 and has_locked = 1
    */
    public function listsallbusinessnew()
    {
        $page =  intval(Request::input('page', '1'));
        $size =  intval(Request::input('size', '20'));
        $role_id =  intval(Request::input('role_id', '57'));
        $has_completed = intval(Request::input('has_completed', '0'));    //1为已完成，2为未完成
        $has_locked = intval(Request::input('has_locked', '0'));    //1为已锁定，2为未锁定  
        $has_all = intval(Request::input('has_all', '1'));    //1不加user_id条件，0为加user_id条件 
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $condition['role_id'] = '56'; 
        $condition['user_id'] = $user_id;
        if ($has_all) {
            $condition['user_id'] = '';
        }
        $condition['has_completed'] = $has_completed;
        $condition['has_locked'] = $has_locked;
        $list = json_decode($this->_task_instance->lists($condition, $page, $size));
        $listarr = $this->_common->object_array($list); 
        if ($listarr['error_no'] == '0') {
            $rs = $this->_business->getbusinessinfo($listarr['result']['rows']);
        } else {
            return $this->_common->output('', $listarr['error_no'], $listarr['error_msg']);
        }
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

}