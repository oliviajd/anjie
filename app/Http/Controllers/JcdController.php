<?php

namespace App\Http\Controllers;

use App\Http\Models\business\Jcd;
use App\Http\Models\business\Auth;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;

//主要用于展示静态页面
class JcdController extends Controller
{
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_jcd = new Jcd();
        $this->_auth = new Auth();
    }

    /**
     * 聚车金融标的状态推送
     * @param string finance_bill_id   标的id
     * @return object   errorcode为0时修改成功
     */
    public function billnotify()
    {
        $params['finance_bill_id'] = Request::input('finance_bill_id', '0');   //标的id
        $params['status'] = Request::input('status', '');   //审核成功 VERIFY_SUCCESS;    审核失败 VERIFY_FAILED ;    上标成功 ONLINE_SUCCESS ;   用户投资 USER_TENDER ;放款成功  PAY_SUCCESS;      还款提醒 REPAY_NOTICE;     还款成功 REPAY_SUCCESS   提前还款状态  PREPAYMENT_SUCCESS
        $params['finance_amount'] = Request::input('finance_amount', '');   //已融资金额  用户投资USER_TENDER的时候更新
        $params['total_finance'] = Request::input('total_finance', '');   //融资总金额    审核成功VERIFY_SUCCESS的时候更新
        $params['repay_time'] = Request::input('repay_time', '');   //还款时间    用户投资USER_TENDER的时候更新
        $params['repay_last_time'] = Request::input('repay_last_time', '');   //实际的上一次还款时间   审核成功VERIFY_SUCCESS的时候更新
        $params['repay_end_time'] = Request::input('repay_end_time', '');   //实际的最后一次还款时间   审核成功VERIFY_SUCCESS的时候更新
        $params['repay_account_yes'] = Request::input('repay_account_yes', '');   //已还款金额  还款成功的时候REPAY_SUCCESS更新
        $params['repay_account'] = Request::input('repay_account', '');   //应还款金额   审核成功VERIFY_SUCCESS的时候更新
        $params['borrow_title'] = Request::input('borrow_title', '');   //标的编号    每次都会返回
        $params['overdue_status'] = Request::input('overdue_status', '');   //逾期状态
        $params['overdue_days'] = Request::input('overdue_days', '');   //逾期天数
        $params['prepayment_status'] = Request::input('prepayment_status', '');   //提前还款的状态
        $params['prepayment_time'] = Request::input('prepayment_time', '');   //提前还款的时间
        $params['service_fee'] = Request::input('service_fee', '');   //服务费
        $params['loan_time'] = Request::input('loan_time', '');   //放款时间
        $rs = $this->_jcd->billnotify($params);
        return $rs;
    }

    /**
     * 认证申请列表
     * @param string token   登录的token
     * @return object   errorcode为0时修改成功
     */
    public function listsverify()
    {
        //获取用户id
        $token = Request::input('token', '');
        $params['name'] = Request::input('name', '');   //客户姓名
        $params['shopname'] = Request::input('shopname', '');   //店铺名称
        $params['verify_type'] = Request::input('verify_type', ''); //认证类型
        $params['start_time'] = Request::input('start_time', '');   //开始时间
        $params['end_time'] = Request::input('end_time', '');       //结束时间
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $rs = $this->_jcd->listsverify($params);
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    /**
     * 认证申请处理
     * @param string token   登录的token
     * @return object   errorcode为0时修改成功
     */
    public function handleverify()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['verify_id'] = Request::input('verify_id', '');  //认证id
        $param['verify_type'] = Request::input('verify_type', '');  //认证类型  1：个体认证，1：商户认证
        $param['shopname'] = Request::input('shopname', '');  //店铺名称
        $param['area'] = Request::input('area', '');  //所在地区
        $param['address'] = Request::input('address', '');  //详细地址
        $param['business_license_number'] = Request::input('business_license_number', '');  //营业执照号码
        $param['company_name'] = Request::input('company_name', '');  //公司名称
        $param['verify_result'] = Request::input('verify_result', '');  //认证结果，1：通过，4：拒绝
        if ($param['area'] == '' || $param['address'] == '' || $param['verify_type'] == '' || $param['verify_id'] == '' || $param['verify_result'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($param['verify_type'] == '2') {
            if ($param['business_license_number'] == '' || $param['company_name'] == '') {
                return $this->_common->output(null, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
            }
        } elseif ($param['verify_type'] == '1') {
            $param['business_license_number'] = '';
            $param['company_name'] = '';
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证类型不存在');
        }
        if ($param['verify_result'] !== '1' && $param['verify_result'] !== '4') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证结果不存在');
        }
        $rs = $this->_jcd->handleverify($param);
        return $rs;
    }

    //获取单个的认证信息
    public function getverifyinfo()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['verify_id'] = Request::input('verify_id', '');  //认证id
        $rs = $this->_jcd->getverifyinfo($user_id, $param);
        return $rs;
    }

    //获取车商信息
    public function getcsrinfo()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['csr_id'] = Request::input('csr_id', '');  //认证id
        $rs = $this->_jcd->getcsrinfo($user_id, $param);
        return $rs;
    }

    /**
     * 工作台列出所有件
     * @param
     */
    public function getjcrlist()
    {
        //获取用户id
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $roleid = Request::input('role_id', '');;   //
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $params['type'] = Request::input('type', '1');   //1查待认领，2查待处理
        $params['name'] = intval(Request::input('name', ''));   //客户姓名
        $params['shopname'] = intval(Request::input('shopname', ''));   //店铺名称
        $params['deadline_request'] = intval(Request::input('deadline_request', ''));   //融资期限的申请值
        $params['rate_request'] = intval(Request::input('rate_request', ''));   //融资利率的申请值
        $params['deadline'] = intval(Request::input('deadline', ''));   //融资期限
        $params['rate'] = intval(Request::input('rate', ''));   //融资利率
        $params['start_time'] = intval(Request::input('start_time', ''));   //开始时间
        $params['end_time'] = intval(Request::input('end_time', ''));   //结束时间
        $params['roleid'] = $roleid;
        $params['user_id'] = $user_id;
        $inputs = Request::all();
        try {
            $worklist = $this->_jcd->getjcrlist($params, $inputs);
        } catch (\Exception $e) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);
        }
        if ($worklist !== false) {
            return $this->_common->output($worklist, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    /**
     * 认领操作
     * @param work_id               工作id
     */
    public function pickup()
    {
        $this->_common->setlog();
        //获取用户id
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['csr_id'] = Request::input('csr_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        if ($params['csr_id'] == '' || $params['item_instance_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try {
            $rs = $this->_jcd->pickup($user_id, $params);   //认领征信的具体逻辑
        } catch (\Exception $e) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);
        }
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    /**
     * 工作流认领
     * @param work_id               工作id
     */
    public function workflowpickup()
    {
        $this->_common->setlog();
        $user_id = Request::input('user_id', '');   //客户id
        $params['csr_id'] = Request::input('csr_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        if ($params['csr_id'] == '' || $params['item_instance_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        try {
            $rs = $this->_jcd->pickup($user_id, $params);   //认领征信的具体逻辑
        } catch (\Exception $e) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);
        }
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    /**
     * 融资申请
     * @param csr_id                车商融id，必传
     * @param item_instance_id      任务实例id，必传
     * @param fcsrrequest_result    融资申请结果：1:通过、2：拒绝
     * @param money                 审批融资金额
     * @param car_stock             审批库存车辆
     * @param deadline              审批融资期限
     * @param rate                  审批融资利率
     */
    public function csrrequestverify()
    {
        $this->_common->setlog();
        //获取用户id
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['csr_id'] = Request::input('csr_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        $params['csrrequest_result'] = Request::input('csrrequest_result', '');   //融资申请结果：1:通过、2：拒绝
        $params['money'] = Request::input('money', '');  //审批融资金额
        $params['deadline'] = Request::input('deadline', '');  //审批融资期限
        $params['rate'] = Request::input('rate', '');  //审批融资利率
        $params['imgs'] = Request::input('imgs', '');  //车资料
        $params['remark'] = Request::input('remark', '');  //备注
        if ($params['csr_id'] == '' || $params['item_instance_id'] == '' || $params['csrrequest_result'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($params['csrrequest_result'] == '1') {
            if ($params['money'] == '' || $params['imgs'] == '' || $params['deadline'] == '' || $params['rate'] == '') {
                return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
            }
        } else {
            if ($params['remark'] == '') {
                return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
            }
        }
        try {
            $rs = $this->_jcd->csrrequestverify($user_id, $params);   //征信的具体逻辑
        } catch (\Exception $e) {
            var_dump($e);
        }
        return $rs;
    }

    /**
     * 上标申请
     * @param csr_id                车商融id，必传
     * @param item_instance_id      任务实例id，必传
     * @param csrimgs               车商的资料
     * @param billdetails           标的详情
     */
    public function billrequestverify()
    {
        $this->_common->setlog();
        //获取用户id
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }

        $params['transfer'] = Request::input('transfer', '');   //过户
        $params['csr_id'] = Request::input('csr_id', '');   //工作id
        $params['item_instance_id'] = Request::input('item_instance_id', '');   //任务实例id
        if ($params['csr_id'] == '' || $params['item_instance_id'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }

        $rs = $this->_jcd->billrequestverify($user_id, $params);   //征信的具体逻辑
        return $rs;
    }

    /**
     * 认证申请记录列表
     * @param string token   登录的token
     * @return object   errorcode为0时修改成功
     */
    public function listsverifyrecord()
    {
        //获取用户id
        $token = Request::input('token', '');
        $params['name'] = Request::input('name', '');   //客户姓名
        $params['shopname'] = Request::input('shopname', '');   //店铺名称
        $params['verify_type'] = Request::input('verify_type', ''); //认证类型
        $params['start_time'] = Request::input('start_time', '');   //开始时间
        $params['end_time'] = Request::input('end_time', '');       //结束时间
        $params['verify_status'] = Request::input('verify_status', '');       //认证结果，1：通过4：拒绝
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $rs = $this->_jcd->listsverifyrecord($params);
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    /**
     * 融资申请记录列表
     * @param string token   登录的token
     * @return object   errorcode为0时修改成功
     */
    public function listscsrrequestrecord()
    {
        //获取用户id
        $token = Request::input('token', '');
        $params['name'] = Request::input('name', '');   //客户姓名
        $params['shopname'] = Request::input('shopname', '');   //店铺名称
        $params['deadline'] = Request::input('deadline', ''); //融资期限
        $params['rate'] = Request::input('rate', ''); //融资利率
        $params['start_time'] = Request::input('start_time', '');   //开始时间
        $params['end_time'] = Request::input('end_time', '');       //结束时间
        $params['crsrequest_status'] = Request::input('crsrequest_status', '');       //申请审核结果，1：通过4：拒绝
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $rs = $this->_jcd->listscsrrequestrecord($params);
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    /**
     * 标的申请记录列表
     * @param string token   登录的token
     * @return object   errorcode为0时修改成功
     */
    public function listsbillrecord()
    {
        //获取用户id
        $token = Request::input('token', '');
        $params['name'] = Request::input('name', '');   //客户姓名
        $params['shopname'] = Request::input('shopname', '');   //店铺名称
        $params['deadline'] = Request::input('deadline', ''); //融资期限
        $params['rate'] = Request::input('rate', ''); //融资利率
        $params['start_time'] = Request::input('start_time', '');   //开始时间
        $params['end_time'] = Request::input('end_time', '');       //结束时间
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $rs = $this->_jcd->listsbillrecord($params);
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    //还款列表
    public function borrowplan()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['name'] = Request::input('name', '');   //客户姓名
        $params['shopname'] = Request::input('shopname', '');   //店铺名称
        $params['type'] = Request::input('type', '');   //类型，1：已逾期，2：未逾期
        $params['user_id'] = Request::input('user_id', ''); //  借款人编号
        $params['ba_account_id'] = Request::input('ba_account_id', '');   // 借款人电子账号
        $params['status'] = Request::input('status', '');  //1已还款，2未还款
        $params['start_time'] = Request::input('start_time', '');  //筛选开始时间
        $params['end_time'] = Request::input('end_time', '');  //筛选结束时间
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $params['csr_no'] = intval(Request::input('csr_no', ''));  //融资单号
        $params['car_model'] = intval(Request::input('car_model', ''));  //车辆型号
        $params['transfer_status'] = intval(Request::input('transfer_status', ''));  //过户状态
        $params['repay_type'] = intval(Request::input('repay_type', ''));  //还款类别
        $rs = $this->_jcd->borrowplan($params);
        return $rs;
    }

    //待过户列表
    public function transferlist()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['name'] = Request::input('name', '');
        $params['shopname'] = Request::input('shopname', '');
        $params['repay_type'] = Request::input('repay_type', '');//1：提前还款，2：到期还款，3：逾期还款
        $params['start_time'] = Request::input('start_time', '');  //筛选开始时间
        $params['end_time'] = Request::input('end_time', '');  //筛选结束时间
        $params['page'] = Request::input('page', '1');   //第几页
        $params['size'] = Request::input('size', '20');  //每一页的数量
        $params['user_id'] = Request::input('user_id');  //每一页的数量
        $params['has_transfer'] = Request::input('has_transfer', '2');  //1:已过户，2：待过户
        if ($params['has_transfer'] == null) {
            $params['has_transfer'] = 2;
        }
        $rs = $this->_jcd->transferlist($params);
        return $rs;
    }

    //处理过户
    public function transferhandle()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['bill_id'] = Request::input('bill_id', ''); //标的id 
        $rs = $this->_jcd->transferhandle($params);
        return $rs;
    }

    //资金明细
    public function listfunddetail()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $params['page'] = Request::input('page', ''); //页码
        $params['size'] = Request::input('size', ''); //每页的数量
        $params['user_id'] = Request::input('user_id', ''); //user_id
        $params['start_time'] = Request::input('start_time', ''); //开始时间
        $params['end_time'] = Request::input('end_time', ''); //结束时间
        $params['ba_type'] = Request::input('ba_type', ''); //交易状态
        $rs = $this->_jcd->financial_details($params);

        return $rs;
    }

    //修改增信
    public function updateCredit()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }

        $params['main_business'] = Request::input('main_business', ''); //主营业务
        $params['foundtime'] = Request::input('foundtime', '');  //成立时间
        $params['credit_score'] = Request::input('credit_score', ''); //信用评分
        $params['manage_score'] = Request::input('manage_score', ''); //经营评分
        $params['assets_score'] = Request::input('assets_score', ''); //资产评分
        $params['debt_score'] = Request::input('debt_score', ''); //负债评分
        $params['conducive_score'] = Request::input('conducive_score', ''); //增信评分
        $params['csr_id'] = Request::input('csr_id', ''); //融资单id
        $params['csrimgs'] = Request::input('csrimgs', ''); //车商资料
        $params['quota'] = Request::input('quota', ''); //车商资料
        $id = Request::input('verify_id', ''); //车商资料
        $user_id = Request::input('user_id', ''); //车商id

        $rs = $this->_jcd->update_credit($id, $params, $user_id);

        return $rs;

    }

    //修改增信
    public function updateImage()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }

        $params['csrimgs'] = Request::input('csrimgs', ''); //车商资料
        $user_id = Request::input('user_id', ''); //车商id

        $rs = $this->_jcd->update_image($params, $user_id);

        return $rs;

    }

    //车商融资单
    public function cardealerrequestrecord()
    {
        $p = intval(Request::input('page', '1'));   //第几页
        $n = intval(Request::input('size', '20'));  //每一页的数量
        $params['status'] = Request::input('crsrequest_status', '');   //1通过，4拒绝
        $params['csr_no'] = Request::input('csr_no', '');;   //融资单号
        $params['car_model'] = Request::input('car_type', '');   //车辆型号
        $params['start_time'] = Request::input('start_time', ''); //开始时间
        $params['end_time'] = Request::input('end_time', ''); //结束时间
        $params['user_id'] = Request::input('user_id', ''); //用户id

        $rs = $this->_jcd->getCarCsrList($p, $n, $params);

        return $rs;
    }

//车商的估价详情 （废弃）
    public function detailcarevaluate()
    {
        $token = Request::input('token', '');
        $user_info = $this->_auth->getInfoBytoken($token);
        if (!$user_info) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }

        $id = Request::input('id', ''); //id
        $user_id = Request::input('user_id', ''); //id

        $rs = $this->_jcd->getcarprice($id, $user_id);

        return $rs;
    }

//车商的估价列表
    public function carevaluaterecord()
    {
        $token = Request::input('token', '');
        $user_info = $this->_auth->getInfoBytoken($token);
        if (!$user_info) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }

        $p = Request::input('page', ''); //页码
        $n = Request::input('size', ''); //每页的数量
        $params['user_id'] = Request::input('user_id', ''); //user_id
        $params['start_time'] = Request::input('start_time', ''); //开始时间
        $params['end_time'] = Request::input('end_time', ''); //结束时间
        $params['model_name'] = Request::input('model_name', ''); //车型

        $rs = $this->_jcd->listcarprice($p, $n, $params);

        return $rs;
    }

    //获取单个车商的详情
    public function getdealerinfo()
    {
        $token = Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $user_id = Request::input('user_id', '');  //认证id
        $rs = $this->_jcd->getdealerinfo($user_id);
        return $rs;
    }

}