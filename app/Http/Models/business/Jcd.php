<?php

namespace App\Http\Models\business;

use App\Http\Models\table\Jcr_car_history;
use App\Http\Models\table\Jcr_csr_car;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Common;
use App\Http\Models\common\Jcrpost;
use App\Http\Models\table\Jcr_users;
use App\Http\Models\table\Jcr_verify;
use App\Http\Models\table\Jcr_file;
use App\Http\Models\table\Csr_file;
use App\Http\Models\table\Bill_file;
use App\Http\Models\table\Jcr_csr;
use App\Http\Models\table\Jcr_bill;
use App\Http\Models\table\Jcr_task;
use App\Http\Models\table\Anjie_file_class;
use Illuminate\Support\Facades\Log;
use App\Http\Models\business\File;
use App\Http\Models\business\Workflowcsr;
use Mail;

class Jcd extends Model
{
    protected $_jcr_users = null;

    public function __construct()
    {
        parent::__construct();
        $this->_common = new Common();
        $this->_jcr_users = new Jcr_users();
        $this->_jcr_verify = new Jcr_verify();
        $this->_jcr_file = new Jcr_file();
        $this->_csr_file = new Csr_file();
        $this->_bill_file = new Bill_file();
        $this->_jcr_csr = new Jcr_csr();
        $this->_jcr_bill = new Jcr_bill();
        $this->_jcr_task = new Jcr_task();
        $this->_jcrpost = new Jcrpost();
        $this->_file = new File();
        $this->_anjie_file_class = new Anjie_file_class();
        $this->_workflowcsr = new Workflowcsr();
    }

    //聚车金融标的状态推送
    public function billnotify($params)
    {
        $billinfo = $this->_jcr_bill->getdetailbyfinancebillid($params['finance_bill_id']);

        if (empty($billinfo)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '标的信息不存在');
        }
        if ($params['status'] == '') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '更新的状态不能为空');
        }
        $sql = "update jcr_bill set status = '" . $params['status'] . "', borrow_title = '" . $params['borrow_title'] . "', finance_amount = '" . $params['finance_amount'] . "', finance_amount = '" . $params['finance_amount'] . "', total_finance = '" . $params['total_finance'] . "', repay_last_time = '" . $params['repay_last_time'] . "', repay_end_time = '" . $params['repay_end_time'] . "', repay_account = '" . $params['repay_account'] . "', repay_account_yes = '" . $params['repay_account_yes'] . "', repay_time = '" . $params['repay_time'] . "', overdue_status = '" . $params['overdue_status'] . "', overdue_days = '" . $params['overdue_days'] . "', prepayment_status = '" . $params['prepayment_status'] . "', prepayment_time = '" . $params['prepayment_time'] . "'";
        if ($params['status'] == 'REPAY_SUCCESS') {    //还款成功
            // $repay_end_day = date('Ymd', intval($params['repay_end_time']));    //最后实际还款的时间
            // $repay_day = date('Ymd', intval($params['repay_time']));            //最后还款日
            // $reapy_end_hour = date('H', intval($params['repay_end_time']));     //最后实际还款的小时数
            // if (($repay_end_day > $repay_day)|| ($repay_end_day == $repay_day && $reapy_end_hour >=12) ) {  //逾期
            //   $repay_type = 3;
            // } elseif ($repay_end_day == $repay_day && $reapy_end_hour < 12) {
            //   $repay_type = 2;
            // } else {
            //   $repay_type = 1;
            // }
            $repay_type = 2;
            $sql = $sql . ", has_repay =1, repay_type = '" . $repay_type . "'";
            $csrinfo = $this->_jcr_csr->getinfobycsrid($billinfo['csr_id']);

            $this->_jcr_verify->decrementField(['user_id'=>$csrinfo['user_id']],'use_quota',($billinfo['money'] / 10000));
        }

        if ($params['status'] == 'VERIFY_SUCCESS'){
            $sql = $sql . ", service_fee =  '". $params['total_finance'] * 0.03 . "'";
        }

        if ($params['status'] == 'VERIFY_FAILED'){
            $csrinfo = $this->_jcr_csr->getinfobycsrid($billinfo['csr_id']);

            $this->_jcr_verify->decrementField(['user_id'=>$csrinfo['user_id']],'use_quota',($billinfo['money'] / 10000));
        }

        if ($params['status'] == 'PAY_SUCCESS') {
            $sql = $sql . ", has_loan = 1 ";
            $sql = $sql . ", loan_time = '" . time() . "'" ;
        }
        if ($params['status'] == 'OVERDUE' && $params['overdue_status'] == '1') {
            $sql = $sql . ", repay_type = 3 ";
        }
        if ($params['status'] == 'PREPAYMENT_SUCCESS' && $params['prepayment_status'] == '1') {
            $sql = $sql . ", repay_type = 1 ";
        }
        $sql = $sql . " where finance_bill_id = '" . $params['finance_bill_id'] . "'";
        $updatebillnotify = $this->_jcr_bill->updatebillnotify($sql);  //更新标的的状态推送
        if ($updatebillnotify == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '更新推送的状态失败');
        }
        $billinfo = $this->_jcr_bill->getdetailbyfinancebillid($params['finance_bill_id']);
        $csrarr = $this->_jcr_bill->getdetailbycsrid($billinfo['csr_id']);
        $loanarr = array_column($csrarr, 'has_loan');
        if (!in_array('1', $loanarr)) {  //没有已放款的
            $has_loan = 3;
        } elseif (!in_array('2', $loanarr)) { //全部已放款
            $has_loan = 1;
        } else {      //部分
            $has_loan = 2;
        }
        $updateloan = $this->_jcr_csr->updateloan($has_loan, $billinfo['csr_id']);
        if ($updateloan == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '更新已放款状态失败');
        }
        return $this->_common->output(null, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    public function listsverify($params)
    {
        $limitout = '';
        if ($params['page'] && $params['size']) {
            $limitout = " limit " . intval(($params['page'] - 1) * $params['size']) . ', ' . intval($params['size']);
        }
        $where = $this->condition_verify($params);  //获得where条件
        $order = ' order by create_time desc';
        $rs['rows'] = $this->_jcr_verify->getdetail($where, $order, $limitout);
        $rs['total'] = $this->_jcr_verify->getcount($where);
        return $rs;
    }

    //查询的条件
    public function condition_verify($inputs,$verify_status = '3')
    {
        $where = ' where is_delete = 2 and verify_status=' . $verify_status;
        if (isset($inputs['name']) && $inputs['name'] !== '') {
            $where = $where . " and name like '%" . $inputs['name'] . "%'";   //申请编号
        }
        if (isset($inputs['shopname']) && $inputs['shopname'] !== '') {
            $where = $where . "  and shopname like '%" . $inputs['shopname'] . "%'";   //商户编号
        }
        if (isset($inputs['verify_type']) && $inputs['verify_type'] !== '') {
            $where = $where . "  and verify_type ='" . $inputs['verify_type'] . "'";   //客户姓名
        }
        if (isset($inputs['user_id']) && $inputs['user_id'] !== '') {
            $where = $where . "  and user_id ='" . $inputs['user_id'] . "'";   //客户姓名
        }
        if (isset($inputs['start_time']) && $inputs['start_time'] !== '' && isset($inputs['end_time']) && $inputs['end_time'] !== '') {
            $where = $where . "  and create_time >='" . $inputs['start_time'] . "' && create_time <= '" . $inputs['end_time'] . "'";   //进件时间
        }
        return $where;
    }

    //处理认证申请
    public function handleverify($params)
    {
        $check = $this->_jcr_verify->getinfobyidandstatus($params['verify_id'], '3');
        if (empty($check)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证信息不存在');
        }
        $check2 = $this->_jcr_verify->getginfobyuseridandstatus($check['user_id'], '5');
        if (!empty($check2)) {
            $params['reverify'] = '1';
            $params['pre_id'] = $check2['id'];
            if ($params['verify_result'] == '1') {
                $delete['verify_id'] = $check2['id'];
                $deleteverify = $this->_jcr_verify->deleteverify($delete);
                if ($deleteverify == false) {
                    return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除暂存认证信息失败');
                }
                $deleteverifyfile = $this->_jcr_file->deleteverifyfile($delete);
                if ($deleteverifyfile == false) {
                    return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除暂存认证信息失败！');
                }
            }
        }

        $params['quota'] = 50;
        $handleverify = $this->_jcr_verify->handleverify($params);
        if ($handleverify) {
            $handleverifyusers = $this->_jcr_users->verfiyuser($check['user_id'], $params['verify_result']);
            $updatename = $this->_jcr_users->updatename($check['name'], $check['user_id']);
            if (isset($params['reverify']) && $params['reverify'] == '1' && $params['verify_result'] == '4') {
                $recover['verify_status'] = '1';
                $recover['verify_id'] = $params['pre_id'];
                $recoververfiy = $this->_jcr_verify->storageverify($recover);
                $recoververfiyfile = $this->_jcr_file->recoververifyfile($recover);
                $recoververfiyuser = $this->_jcr_users->verfiyuser($check['user_id'], '1');
            }
            if ($params['verify_result'] == '1') {   //通过
                //修改融资单
                $detail = $this->_jcr_csr->updateFinancingSingle($check['user_id']);

                if ($detail) {
                    foreach ($detail as $k => $v) {
                        $jcrrs = $v;
                        $jcrrs['name'] = $check['name'];
                        $jcrrs['shopname'] = $check['shopname'];
                        //创建工作流
                        $createprocess = $this->_workflowcsr->createprocess($check['user_id'], $jcrrs);
                    }
                    if ($createprocess === false) {
                        return $this->_common->output(null, Constant::ERR_FAILED_NO, '用户认证失败');
                    }
                }
            }

            if ($params['verify_result'] == '4') {
                $delete['verify_id'] = $check['id'];
                $delete['status'] = '2';
                $handleverifyfile = $this->_jcr_file->storageverifyfile($delete);
            }
            if ($handleverifyusers !== false) {
                $verifyinfo = $this->_jcr_verify->getinfobyid($params['verify_id']);
                return $this->_common->output($verifyinfo, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
            } else {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证处理失败！');
            }
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证处理失败');
        }
    }

    //获取认证信息
    public function getverifyinfo($user_id, $params)
    {
        $rs = $this->_jcr_verify->getinfobyid2($params['verify_id']);

        if (empty($rs)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证信息不存在');
        }
        $rs['imgs'] = $this->_file->listjcrimages($params);  //列出图像、视频
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    //获取车商信息
    public function getdealerinfo($user_id)
    {
        $rs = $this->_jcr_verify->getginfobyuserid($user_id);

        if ($rs['validity_time']){
            $rs['validity_status'] = $rs['validity_time'] < time() ? 1 : 2;
        }

        $userinfo = $this->_jcr_users->getinfobyuserid($rs['user_id']);
        $rs['loan_info'] = $this->_common->object_array(json_decode($this->_jcrpost->user_borrower_stats_admin(env('IFCAR_TOKEN', 'test'),  $userinfo['user_id'])));
        $ifcarinfo = $this->_common->object_array(json_decode($this->_jcrpost->user_get($userinfo['token'])));
        $rs['bank_account'] = $ifcarinfo['result']['user']['bank_account'];

        if (empty($rs)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证信息不存在');
        }

        $param['verify_id'] = $rs['id'];
        $rs['imgs'] = $this->_file->listjcrimages($param);  //列出图像、视频
        $rs['dealerimgs'] = $this->_file->listdealerimages(['verify_id'=>$rs['id']]);  //列出车商资料
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    //获取车商融认证信息
    public function getcsrinfo($user_id, $params)
    {
        $csrinfo = $this->_jcr_csr->getinfobycsrid($params['csr_id']);
        if (empty($csrinfo)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证信息不存在');
        }
        $verifyinfo = $this->_jcr_verify->getginfobyuseridandstatus($csrinfo['user_id'], '1');

        $verifyinfo['validity_status'] = 1;
        if ($verifyinfo['validity_time']){
            $verifyinfo['validity_status'] = $verifyinfo['validity_time'] < time() ? 1 : 2;
        }

        $csr = Jcr_csr_car::getDetailById($csrinfo['history_id']);
        $rs = array(
            'csr' => $csrinfo,
            'verifyinfo' => $verifyinfo,
            'car' => $csr,
        );
        if (empty($rs)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '认证信息不存在');
        }
        $params['verify_id'] = $verifyinfo['id'];
        $rs['verifyinfo']['imgs'] = $this->_file->listjcrimages($params);  //列出图像、视频
        $param['file_class_id'] = '10';
        $param['csr_id'] = $params['csr_id'];
        $rs['dealerimgs'] = $this->_file->listdealerimages(['verify_id'=>$verifyinfo['id']]);  //列出车商资料
        $rs['bills'] = $this->_jcr_bill->getdetailbycsrid($params['csr_id']);
        $rs['cars'] = Jcr_car_history::getDetailById($params['csr_id']);
        $where = " where csr_id = " . $params['csr_id'];
        $rs['csrimages'] = $this->_bill_file->getDetail($where);
        foreach ($rs['bills'] as $key => $value) {
            $where = " where bill_id = " . $value['id'];
            $rs['bills'][$key]['imgs'] = $this->_bill_file->getDetail($where);
            if (!empty($rs['bills'][$key]['imgs'])) {
                $rs['bills'][$key]['imgs'] = $rs['bills'][$key]['imgs'][0];
            }
        }
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    //获取列表
    public function getjcrlist($params, $inputs)
    {
        $user_id = $params['user_id'];
        //1查待认领，2查待处理
        if ($params['type'] == '1') {
            $params['user_id'] = '';
            $params['role_id'] = $params['roleid'];
            $params['has_locked'] = 2;
            $params['has_completed'] = 2;
            $params['has_stoped'] = 2;
        } elseif ($params['type'] == '2') {
            $params['user_id'] = $params['user_id'];
            $params['role_id'] = $params['roleid'];
            $params['has_locked'] = 1;
            $params['has_completed'] = 2;
            $params['has_stoped'] = 2;
        }
        $condition = array();
        if (isset($inputs['name']) && $inputs['name'] !== '') {
            $condition['name'] = $inputs['name'];
        }
        if (isset($inputs['shopname']) && $inputs['shopname'] !== '') {
            $condition['shopname'] = $inputs['shopname'];
        }
        if (isset($inputs['deadline_request']) && $inputs['deadline_request'] !== '') {
            $condition['deadline_request'] = $inputs['deadline_request'];
        }
        if (isset($inputs['rate_request']) && $inputs['rate_request'] !== '') {
            $condition['rate_request'] = $inputs['rate_request'];
        }
        if (isset($inputs['deadline']) && $inputs['deadline'] !== '') {
            $condition['deadline'] = $inputs['deadline'];
        }
        if (isset($inputs['rate']) && $inputs['rate'] !== '') {
            $condition['rate'] = $inputs['rate'];
        }
        if (isset($inputs['start_time']) && $inputs['start_time'] !== '') {
            $condition['start_time'] = $inputs['start_time'];
        }
        if (isset($inputs['end_time']) && $inputs['end_time'] !== '') {
            $condition['end_time'] = $inputs['end_time'];
        }
        $workflowlist = $this->_workflowcsr->getworkflowlist($params, $condition);
        if (empty($workflowlist)) {
            return $workflowlist;
        }
        $rs = array();
        $rs['rows'] = array();
        foreach ($workflowlist['rows'] as $key => $value) {
            $rs['rows'][$key] = $this->_jcr_csr->getinfobycsrid($value['fields']['csr_id']);
            $rs['rows'][$key]['verify_info'] = $this->_jcr_verify->getginfobyuseridandstatus($rs['rows'][$key]['user_id'], '1');
            $rs['rows'][$key]['item_instance_id'] = $value['item_instance_id'];
            $rs['rows'][$key]['type'] = $params['type'];
            $rs['rows'][$key]['receive_time'] = $value['receive_time'];
        }
        $rs['total'] = $workflowlist['total'];
        return $rs;
    }

    /**
     * 认领征信件
     * @param work_id 工作id
     */
    public function pickup($user_id, $params)
    {
        $rs = $this->_workflowcsr->pickup($user_id, $params);   //拾取任务
        return $rs;
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
    public function csrrequestverify($user_id, $params)
    {
        $has_completed = $this->_jcr_task->issettask($params['item_instance_id']);
        if (empty($has_completed)) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
        } elseif ($has_completed['status'] == '2') {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
        } elseif($has_completed['user_id'] !== $user_id) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
        }
        Log::info("ups接收到数据：".json_encode($params));
        $csrinfo = $this->_common->object_array(json_decode($this->getcsrinfo($user_id, $params)));  //引擎中的认领任务
        Log::info("ups获取到引擎中的任务：".json_encode($csrinfo));
        if ($csrinfo['result']['verifyinfo']['has_score'] == '2' && $params['csrrequest_result'] == '1') {
            if ($csrinfo['result']['verifyinfo']['foundtime'] == '' || $csrinfo['result']['verifyinfo']['main_business'] == '' || $csrinfo['result']['verifyinfo']['credit_score'] == '' || $csrinfo['result']['verifyinfo']['manage_score'] == '' || $csrinfo['result']['verifyinfo']['assets_score'] == '' || $csrinfo['result']['verifyinfo']['debt_score'] == '' || $csrinfo['result']['verifyinfo']['conducive_score'] == '') {
                return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO,'增信评分不能为空');
            }
        }
        Log::info("ups验证参数完成！");
        $userinfo = $this->_jcr_users->getinfobyuserid($csrinfo['result']['csr']['user_id']);

        Log::info("ups获取用户信息：".json_encode($userinfo));
        if ($params['csrrequest_result'] == '2') {   //退件，complete这个任务  status=2
            $data = array(
                'csr_id' => $params['csr_id'],
                'csrrequest_user_id' => $user_id,
                'crsrequest_status' => '2',   //拒件
                'task_status' => '2',
            );
            $this->_jcr_verify->decrementField(['user_id'=>$userinfo['user_id']],'use_quota',$csrinfo['result']['csr']['money_request'] );
            $refusework = $this->_jcr_csr->refusework($params);
            $completers = $this->_workflowcsr->completeworkflow($user_id, $params['item_instance_id'], $data);

            if ($completers !== false){
                return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
            }else{
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '融资申请审核完成失败');                //征信报告完成失败
            }

        }
        Log::info("ups111======");
        if ($userinfo['bankaccount_status'] != 1){
            return $this->_common->output('', Constant::ERR_ITEM_NOT_EXISTS_NO,'请先开通融资账户');
        }
        Log::info("ups22222======");
        $param = $params;
        $has_completed = $this->_jcr_task->issettask($params['item_instance_id']);

        if (isset($has_completed['status']) && $has_completed['status'] == '2') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
        }
        Log::info("ups333======");

        Log::info("ups444======");
        if ($params['csrrequest_result'] == '1') {
            //判斷額度
            if (isset($csrinfo['result']['verifyinfo']['use_quota'])){
                if ($csrinfo['result']['verifyinfo']['use_quota'] - $csrinfo['result']['csr']['money_request'] + $params['money'] > $csrinfo['result']['verifyinfo']['quota']) {
                    return $this->_common->output(false, Constant::ERR_FAILED_NO, '额度超限');
                }
            }else{
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '请先填写车商文件');
            }

            //去掉冻结金额
            $this->_jcr_verify->decrementField(['user_id'=>$userinfo['user_id']],'use_quota',$csrinfo['result']['csr']['money_request'] );
        }

        $csrrequestverify = $this->_jcr_csr->csrrequestverify($params, $user_id);   //第一次提交融资申请单的时候

        if ($csrrequestverify == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '融资申请提交有误');                      //征信提交有误
        }


        Log::info("ups555======");
        //车商文件
        $csr_file = $this->_file->listdealerimages(['verify_id'=>$csrinfo['result']['verifyinfo']['id']]);

        if (!$csr_file){
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '请先填写车商文件');                      //征信提交有误
        }
        Log::info("ups666======");
        $res = current($csr_file);
        unset($res['id'],$res['remark']);
        $res['csr_id'] = $params['csr_id'];
        $csr_file = $this->_csr_file->addimage($res);
        Log::info("ups======addimage");
        //车资料
        $addbillimage = $this->addbillimage($params['imgs'], 0, $csrinfo['result']['csr']['user_id'], $params['csr_id']);
        Log::info("ups======addbillimage");
        if ($addbillimage == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加标的资料失败');
        }
        Log::info("ups======77777");
        if ($params['csrrequest_result'] == '1') {
            if ($csrinfo['result']['verifyinfo']['verify_type'] == '1') {
                $params['verify_type'] = '2';
            } elseif ($csrinfo['result']['verifyinfo']['verify_type'] == '2') {
                $params['verify_type'] = '1';
            }
            Log::info("ups======888");
            $csrinfo = $this->_common->object_array(json_decode($this->getcsrinfo($user_id, $params)));  //引擎中的认领任务
            Log::info("ups======999");
            $finance_account_sub_update = $this->_common->object_array(json_decode($this->_jcrpost->finance_account_sub_update($userinfo['token'], $params['verify_type'], $csrinfo['result']['verifyinfo']['main_business'], $csrinfo['result']['verifyinfo']['foundtime'], $csrinfo['result']['verifyinfo']['company_name'], $csrinfo['result']['verifyinfo']['name'], '江西银行', $userinfo['accountId'], $csrinfo['result']['verifyinfo']['certificate_number'], $csrinfo['result']['verifyinfo']['loginname'], $userinfo['finance_account_sub_id'])));

            if ($finance_account_sub_update['error_no'] !== 200) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '融资子账号信息更新失败');
            }
        }
        Log::info("ups======0000");
        $completers = 'true';
        if ($params['csrrequest_result'] == '1') {    //提交，且通过，complete这个任务  status=1
            $data = array(
                'csr_id' => $params['csr_id'],
                'crsrequest_status' => '1',   //通过
                'task_status' => '1',
                'csrrequest_user_id' => $user_id,
            );
            Log::info("ups======aaaa");
            $completers = $this->_workflowcsr->completeworkflow($user_id, $params['item_instance_id'], $data);
            Log::info("ups======ccc");
            $this->_jcr_verify->incrementField(['user_id'=>$userinfo['user_id']],'use_quota',$params['money'] );
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '提交类型不对');                  //提交类型不对
        }
        if ($completers !== false) {
            Log::info("ups======dddd");
            $rs = $this->_jcr_csr->getinfobycsrid($params['csr_id']);
            Log::info("ups======eeee");
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '融资申请审核完成失败');                //征信报告完成失败
        }
    }

    //标的申请
    public function billrequestverify($user_id, $params)
    {
        $has_completed = $this->_jcr_task->issettask($params['item_instance_id']);
        if (empty($has_completed)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务不存在');         //该任务已完成
        } elseif ($has_completed['status'] == '2') {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '该任务已完成');         //该任务已完成
        } elseif($has_completed['user_id'] !== $user_id) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '您没有该件的权限');         //该任务已完成
        }
        $billdetails = $this->_jcr_csr->getinfobycsrid($params['csr_id']);
        //添加标的信息

        $value['csr_id'] = $params['csr_id'];
        $addbill = $this->_jcr_bill->addbill($billdetails);
        if ($addbill == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加标的信息失败');
        }

        $param['bill_id'] = $addbill['id'];
        $param['csr_id'] = $params['csr_id'];
        $imagers = $this->_bill_file->updateimage($param);  //添加图像逻辑
        if ($imagers == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加标的资料失败');
        }

        $csrinfo = $this->_common->object_array(json_decode($this->getcsrinfo($user_id, $params)));

        $userinfo = $this->_jcr_users->getinfobyuserid($csrinfo['result']['csr']['user_id']);

        if (isset($csrinfo['result']['bills']) && !empty($csrinfo['result']['bills'])) {
            foreach ($csrinfo['result']['bills'] as $key => $value) {
                if ($value['finance_bill_id'] == null) {
                    $billparams = $this->format_billdata($userinfo, $value, $csrinfo['result']['csr'], $csrinfo['result']['verifyinfo'], $csrinfo['result']['dealerimgs'][0],$csrinfo['result']['csrimages'][0]);
                    $finance_bill_add = $this->_common->object_array(json_decode($this->_jcrpost->finance_bill_add($billparams)));
                    if ($finance_bill_add['error_no'] !== 200) {
                        $deletenotbill = $this->_jcr_bill->deletenotbill($params['csr_id']);
                        return $this->_common->output($finance_bill_add['result'], $finance_bill_add['error_no'], $finance_bill_add['error_msg']);
                    } else {
                        $updatehasbill = $this->_jcr_csr->updatehasbill($params['csr_id'], '1');
                        $addfinancebillid = $this->_jcr_bill->addfinancebillid($finance_bill_add['result']['finance_bill_id'], $value['id']);
                        if ($addfinancebillid == false || $updatehasbill == false) {
                            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加标的id失败');
                        }
                    }
                }
            }
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加标的资料失败');
        }
        $data = array(
            'csr_id' => $params['csr_id'],
            'billrequest_status' => '1',   //通过
            'task_status' => '1',
        );
        $completers = $this->_workflowcsr->completeworkflow($user_id, $params['item_instance_id'], $data);
        if ($completers !== false) {
            $rs = $this->_jcr_csr->getinfobycsrid($params['csr_id']);
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '标的申请完成失败');                //征信报告完成失败
        }
    }

    //格式化标的的格式
    public function format_billdata($userinfo, $billinfo, $csrinfo, $verifyinfo, $dealerimgs,$csrimgs)
    {
        $rs['token'] = env('IFCAR_TOKEN', 'test');
        $rs['money'] = intval($billinfo['money'] * 10000) * 100;                   //融资金额，单位分
        $rs['car'] = $billinfo['carbrand'];                 // 车辆品牌型号
        $rs['car_type'] = $billinfo['cartype'];             //1新车，2二手车
        $rs['advance'] = 0;                                 // 垫付按揭款，单位分
        $rs['payment_certificate'] = 'no_photo';            //电子银行回款单，图片地址
        $rs['paid_time'] = 0;                               //打款时间
        $rs['attach'] = $csrimgs['ifcar99_path'];   // 用户材料压缩包
        $rs['name'] = $verifyinfo['name'];   // 融资对象姓名
        $rs['id_card'] = $verifyinfo['certificate_number'];          // 融资对象身份证号
        $rs['borrow_type'] = '8';                                   //标的种类：6聚车贷，7新车汇，8车商贷
        $rs['credit_score'] = $verifyinfo['credit_score'];            //信用评分
        $rs['manage_score'] = $verifyinfo['manage_score'];           //经营评分
        $rs['assets_score'] = $verifyinfo['assets_score'];           //资产评分
        $rs['debt_score'] = $verifyinfo['debt_score'];              // 负债评分
        $rs['conducive_score'] = $verifyinfo['conducive_score'];    //增信评分
        $rs['borrower_attach'] = $dealerimgs['ifcar99_path'];  // 借款人材料，压缩包
        $rs['finance_account_sub_id'] = $userinfo['finance_account_sub_id'];
        return $rs;
    }

    //传入imgs和csr_id插入图片
    public function addcsrimage($imgs, $csr_id, $user_id, $verfiy_id)
    {
        $file_type = $this->_anjie_file_class->listjcrimagetype();  //文件类型
        $file_types = array();
        foreach ($file_type as $key => $value) {
            $file_types[$value['id']] = $value;
        }
        if (!is_array($imgs)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加图片失败');
        }
        foreach ($imgs as $key => $value) {
            if (!isset($file_types[$key])) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '该文件类型不存在');                //该文件类型不存在
            }
            $param['file_class_id'] = $key;
            $param['csr_id'] = $csr_id;
            $param['verfiy_id'] = $verfiy_id;
            $curren_length = $this->_csr_file->countByCsridAndFileclassid($param);
            $max_length = intval($file_types[$key]['max_length']);  //该文件类型的最大上传数量
            $min_length = intval($file_types[$key]['min_length']);  //该文件类型的最大上传数量
            $count = count($imgs[$key]['source_lists']) + intval($curren_length['count(*)']);    //该文件类型的上传数量
            if ($count > $max_length) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');                //文件数量超过最大上传张数
            }
            if ($count < $min_length) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');                  //文件数量小于最小上传张数
            }
        }
        $imagers = true;
        foreach ($imgs as $key => $value) {
            foreach ($value['source_lists'] as $k => $v) {
                $param['add_userid'] = $user_id; //添加者的userid
                $param['file_type_name'] = $value['source_type'];
                $param['file_type'] = ($param['file_type_name'] == 'image') ? '1' : '2';
                $param['file_class_id'] = $key;
                $param['file_path'] = $v['org'];
                $param['file_id'] = $v['alt'];
                $param['csr_id'] = $csr_id;
                $param['filename'] = $v['filename'];
                $imagers = $this->_csr_file->addimage($param);  //添加图像逻辑
            }
        }
        if ($imagers == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加图片失败!');
        }
        return true;
    }

    //传入imgs和verify_id插入图片
    public function addjcrimage($imgs, $verfiy_id, $user_id)
    {
        $file_type = $this->_anjie_file_class->listjcrimagetype();  //文件类型
        $file_types = array();
        foreach ($file_type as $key => $value) {
            $file_types[$value['id']] = $value;
        }
        if (!is_array($imgs)) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '添加图片失败');
        }
        foreach ($imgs as $key => $value) {
            if (!isset($file_types[$key])) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '该文件类型不存在');                //该文件类型不存在
            }
            $param['file_class_id'] = $key;
            $param['verify_id'] = $verfiy_id;
            $curren_length = $this->_jcr_file->countByVerifyidAndFileclassid($param);
            $max_length = intval($file_types[$key]['max_length']);  //该文件类型的最大上传数量
            $min_length = intval($file_types[$key]['min_length']);  //该文件类型的最大上传数量
            $count = count($imgs[$key]['source_lists']) + intval($curren_length['count(*)']);    //该文件类型的上传数量
            if ($count > $max_length) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');                //文件数量超过最大上传张数
            }
            if ($count < $min_length) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');                  //文件数量小于最小上传张数
            }
        }
        $imagers = true;
        foreach ($imgs as $key => $value) {
            foreach ($value['source_lists'] as $k => $v) {
                $param['add_userid'] = $user_id; //添加者的userid
                $param['file_type_name'] = $value['source_type'];
                $param['file_type'] = ($param['file_type_name'] == 'image') ? '1' : '2';
                $param['file_class_id'] = $key;
                $param['file_path'] = $v['org'];
                $param['file_id'] = $v['alt'];
                $param['verfiy_id'] = $verfiy_id;
                $param['filename'] = $v['filename'];
                $imagers = $this->_jcr_file->addimage($param);  //添加图像逻辑
            }
        }
        if ($imagers == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '添加图片失败!');
        }
        return true;
    }

    //传入imgs和verify_id修改资料
    public function updatejcrimage($imgs, $verfiy_id, $user_id)
    {
        $file_type = $this->_anjie_file_class->listjcrimagetype();  //文件类型
        $file_types = array();
        foreach ($file_type as $key => $value) {
            $file_types[$value['id']] = $value;
        }
        if (!is_array($imgs)) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '添加图片失败');
        }
        foreach ($imgs as $key => $value) {
            if (!isset($file_types[$key])) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '该文件类型不存在');                //该文件类型不存在
            }
        }
        $imagers = true;
        foreach ($imgs as $key => $value) {
            foreach ($value['source_lists'] as $k => $v) {
                $param['add_userid'] = $user_id; //修改者的userid
                $param['file_type_name'] = $value['source_type'];
                $param['file_type'] = ($param['file_type_name'] == 'image') ? '1' : '2';
                $param['file_class_id'] = $key;
                $param['file_path'] = $v['org'];
                $param['file_id'] = $v['alt'];
                $param['verify_id'] = $verfiy_id;
                $param['filename'] = $v['filename'];
                $imagers = $this->_jcr_file->updateimage($param);  //修改图像逻辑
            }
        }
        if ($imagers == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '添加图片失败!');
        }
        return true;
    }

    //传入imgs和bill_id插入图片
    public function addbillimage($imgs, $bill_id, $user_id, $csr_id)
    {
        $file_type = $this->_anjie_file_class->listjcrimagetype();  //文件类型
        $file_types = array();
        foreach ($file_type as $key => $value) {
            $file_types[$value['id']] = $value;
        }
        if (!is_array($imgs)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加图片失败');
        }
        foreach ($imgs as $key => $value) {
            if (!isset($file_types[$key])) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '该文件类型不存在');                //该文件类型不存在
            }
            $param['file_class_id'] = $key;
            $param['bill_id'] = $bill_id;
            $curren_length = $this->_bill_file->countByBillidAndFileclassid($param);
            $max_length = intval($file_types[$key]['max_length']);  //该文件类型的最大上传数量
            $min_length = intval($file_types[$key]['min_length']);  //该文件类型的最大上传数量
            $count = count($imgs[$key]['source_lists']) + intval($curren_length['count(*)']);    //该文件类型的上传数量
            if ($count > $max_length) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');                //文件数量超过最大上传张数
            }
            if ($count < $min_length) {
                return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');                  //文件数量小于最小上传张数
            }
        }
        $imagers = true;
        foreach ($imgs as $key => $value) {
            foreach ($value['source_lists'] as $k => $v) {
                $param['add_userid'] = $user_id; //添加者的userid
                $param['file_type_name'] = $value['source_type'];
                $param['file_type'] = ($param['file_type_name'] == 'image') ? '1' : '2';
                $param['file_class_id'] = $key;
                $param['file_path'] = $v['org'];
                $param['file_id'] = $v['alt'];
                $param['bill_id'] = $bill_id;
                $param['filename'] = $v['filename'];
                $param['csr_id'] = $csr_id;
                $imagers = $this->_bill_file->addimage($param);  //添加图像逻辑
            }
        }
        if ($imagers == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加图片失败!');
        }
        return true;
    }

    public function listsverifyrecord($params)
    {
        $limitout = '';
        if ($params['page'] && $params['size']) {
            $limitout = " limit " . intval(($params['page'] - 1) * $params['size']) . ', ' . intval($params['size']);
        }
        $where = $this->condition_verify_record($params);  //获得where条件
        $order = ' order by verify_time desc';
        $rs['rows'] = $this->_jcr_verify->getdetail($where, $order, $limitout);
        $rs['total'] = $this->_jcr_verify->getcount($where);
        return $rs;
    }

    //查询的条件
    public function condition_verify_record($inputs)
    {
        $where = " where verify_status in ('1', '4')";
        if (isset($inputs['name']) && $inputs['name'] !== '') {
            $where = $where . " and name like '%" . $inputs['name'] . "%'";   //客户姓名
        }
        if (isset($inputs['shopname']) && $inputs['shopname'] !== '') {
            $where = $where . "  and shopname like '%" . $inputs['shopname'] . "%'";   //店铺名称
        }
        if (isset($inputs['verify_type']) && $inputs['verify_type'] !== '') {
            $where = $where . "  and verify_type ='" . $inputs['verify_type'] . "'";   //认证类型
        }
        if (isset($inputs['verify_status']) && $inputs['verify_status'] !== '') {
            $where = $where . "  and verify_status ='" . $inputs['verify_status'] . "'";   //认证类型
        }
        if (isset($inputs['start_time']) && $inputs['start_time'] !== '' && isset($inputs['end_time']) && $inputs['end_time'] !== '') {
            $where = $where . "  and verify_time >='" . $inputs['start_time'] . "' && verify_time <= '" . $inputs['end_time'] . "'";   //进件时间
        }
        return $where;
    }

    public function listscsrrequestrecord($params)
    {
        $limitout = '';
        if ($params['page'] && $params['size']) {
            $limitout = " limit " . intval(($params['page'] - 1) * $params['size']) . ', ' . intval($params['size']);
        }
        $where = $this->condition_csrrequest_record($params);  //获得where条件
        $order = ' order by b.csrrequestverfiy_time desc';
        $rs['rows'] = $this->_jcr_csr->getdetail2($where, $order, $limitout);
        $rs['total'] = $this->_jcr_csr->getcount2($where);
        return $rs;
    }

    //查询的条件
    public function condition_csrrequest_record($inputs)
    {
        $where = " where  a.user_id = b.user_id  and b.crsrequest_status in ('1', '2') and item_status < 4 and a.verify_status = 1";
        if (isset($inputs['name']) && $inputs['name'] !== '') {
            $where = $where . " and a.name like '%" . $inputs['name'] . "%'";   //客户姓名
        }
        if (isset($inputs['shopname']) && $inputs['shopname'] !== '') {
            $where = $where . "  and a.shopname like '%" . $inputs['shopname'] . "%'";   //店铺名称
        }
        if (isset($inputs['deadline']) && $inputs['deadline'] !== '') {
            $where = $where . "  and b.deadline ='" . $inputs['deadline'] . "'";   //融资期限
        }
        if (isset($inputs['rate']) && $inputs['rate'] !== '') {
            $where = $where . "  and b.rate ='" . $inputs['rate'] . "'";   //融资利率
        }
        if (isset($inputs['crsrequest_status']) && $inputs['crsrequest_status'] !== '') {
            $where = $where . "  and b.crsrequest_status ='" . $inputs['crsrequest_status'] . "'";   //申请审核结果，1：通过2：拒绝
        }
        if (isset($inputs['start_time']) && $inputs['start_time'] !== '' && isset($inputs['end_time']) && $inputs['end_time'] !== '') {
            $where = $where . "  and b.csrrequestverfiy_time >='" . $inputs['start_time'] . "' && b.csrrequestverfiy_time <= '" . $inputs['end_time'] . "'";   //进件时间
        }
        return $where;
    }

    //上标记录
    public function listsbillrecord($params)
    {
        $limitout = '';
        if ($params['page'] && $params['size']) {
            $limitout = " limit " . intval(($params['page'] - 1) * $params['size']) . ', ' . intval($params['size']);
        }
        $where = $this->condition_bill_record($params);  //获得where条件
        $order = ' order by b.billrequest_time desc';
        $rs['rows'] = $this->_jcr_csr->getdetail2($where, $order, $limitout);
        $rs['total'] = $this->_jcr_csr->getcount2($where);
        return $rs;
    }

    //查询的条件
    public function condition_bill_record($inputs)
    {
        $where = " where a.user_id = b.user_id  and b.has_bill=1 and a.verify_status=1 ";
        if (isset($inputs['name']) && $inputs['name'] !== '') {
            $where = $where . " and a.name like '%" . $inputs['name'] . "%'";   //客户姓名
        }
        if (isset($inputs['shopname']) && $inputs['shopname'] !== '') {
            $where = $where . "  and a.shopname like '%" . $inputs['shopname'] . "%'";   //店铺名称
        }
        if (isset($inputs['deadline']) && $inputs['deadline'] !== '') {
            $where = $where . "  and b.deadline ='" . $inputs['deadline'] . "'";   //融资期限
        }
        if (isset($inputs['rate']) && $inputs['rate'] !== '') {
            $where = $where . "  and b.rate ='" . $inputs['rate'] . "'";   //融资利率
        }
        if (isset($inputs['start_time']) && $inputs['start_time'] !== '' && isset($inputs['end_time']) && $inputs['end_time'] !== '') {
            $where = $where . "  and b.csrrequestverfiy_time >='" . $inputs['start_time'] . "' && b.csrrequestverfiy_time <= '" . $inputs['end_time'] . "'";   //进件时间
        }
        return $where;
    }

    //还款列表
    public function borrowplan($params)
    {
        if ($params['type'] == '1') {
            $end_time = strtotime(date('Y-m-d'));
            $start_time = '';
        } elseif ($params['type'] == '2') {
            $start_time = time();
            $end_time = '';
        } else {
            $start_time = '';
            $end_time = '';
        }
        if ((isset($params['name']) && $params['name'] !== '') || (isset($params['shopname']) && $params['shopname'] !== '')|| (isset($params['user_id']) && $params['user_id'] !== '')) {
            $where = " where verify_status = 1 and is_delete = 2";
            if (isset($params['name']) && $params['name'] !== '') {
                $where = $where . " and name like '%" . $params['name'] . "%'";   //客户姓名
            }
            if (isset($params['shopname']) && $params['shopname'] !== '') {
                $where = $where . "  and shopname like '%" . $params['shopname'] . "%'";   //店铺名称
            }
            if (isset($params['user_id']) && $params['user_id'] !== '') {
                $where = $where . "  and user_id like '%" . $params['user_id'] . "%'";   //店铺名称
            }
            $verifyinfo = $this->_jcr_verify->getdetail($where);
            $user_ids = array_column($verifyinfo, 'user_id');
            $user_id = implode(',', $user_ids);
        } else {
            $user_id = '';
        }
        $rs = $this->_common->object_array(json_decode($this->_jcrpost->borrow_repay_lists_admin(env('IFCAR_TOKEN', 'test'), $user_id, '', $params['status'], $start_time, $end_time, $params['page'], $params['size'], $params['type'])));
        if ($rs){
            foreach ($rs['result']['rows'] as $key => $value) {
                $finance_bill_id = $value['finance_bill_id'];
                $rs['result']['rows'][$key]['billinfo'] = $this->_jcr_bill->getdetailbyfinancebillid($finance_bill_id);
                if (!empty($rs['result']['rows'][$key]['billinfo'])) {
                    $rs['result']['rows'][$key]['csrinfo'] = $this->_jcr_csr->getinfobycsrid($rs['result']['rows'][$key]['billinfo']['csr_id']);
                    $rs['result']['rows'][$key]['verifyinfo'] = $this->_jcr_verify->getginfobyuserid($rs['result']['rows'][$key]['csrinfo']['user_id']);
                } else {
                    $rs['result']['rows'][$key]['billinfo'] = array('carbrand' => '');
                    $rs['result']['rows'][$key]['csrinfo'] = array();
                    $rs['result']['rows'][$key]['verifyinfo'] = array('name' => '', 'loginname' => '', 'shopname' => '');
                }
            }
            if (!empty($rs['result']['rows'])) {
                foreach ($rs['result']['rows'] as $key => $value) {
                    if (isset($value['billinfo'])) {
                        $hour = date('H');
                        if (isset($value['billinfo']['repay_time'])){
                            $repay_time = $value['billinfo']['repay_time'];
                            $repay_days = (time() - $repay_time) / 86400;
                            if ($hour >= 12) {
                                $repay_days = $repay_days + 1;
                            }
                            $rs['result']['rows'][$key]['billinfo']['repay_days'] = (int)$repay_days;
                        }else{
                            $rs['result']['rows'][$key]['billinfo']['repay_days'] = 0;
                        }
                    } else {
                        $rs['result']['rows'][$key]['billinfo']['repay_days'] = 0;
                    }

                }
            }
        }
        return $rs;
    }

    //过户列表
    public function transferlist($params)
    {
        $limitout = '';
        if ($params['page'] && $params['size']) {
            $limitout = " limit " . intval(($params['page'] - 1) * $params['size']) . ', ' . intval($params['size']);
        }
        $where = $this->condition_transferlist($params);  //获得where条件
        $order = ' order by jcr_bill.repay_time desc';
        $rs['rows'] = $this->_jcr_bill->getdetailtransfer($where, $order, $limitout);
        foreach ($rs['rows'] as $key => $value) {
            $hour = date('H', intval($value['repay_end_time']));
            $repay_days = (intval($value['repay_end_time']) - intval($value['repay_time'])) / 86400;
            if ($hour >= 12) {
                $repay_days = $repay_days + 1;
            }
            $rs['rows'][$key]['repay_days'] = (int)$repay_days;
        }
        $rs['total'] = $this->_jcr_bill->getdecounttransfer($where);
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function condition_transferlist($inputs)
    {
        $where = " LEFT JOIN jcr_csr on jcr_csr.id=jcr_bill.csr_id  LEFT JOIN jcr_verify on jcr_verify.user_id = jcr_csr.user_id where jcr_bill.has_repay =1 and jcr_bill.has_transfer =" . $inputs['has_transfer'];
        if (isset($inputs['name']) && $inputs['name'] !== '') {
            $where = $where . " and jcr_verify.name like '%" . $inputs['name'] . "%'";   //客户姓名
        }
        if (isset($inputs['user_id']) && $inputs['user_id'] !== '') {
            $where = $where . " and jcr_verify.user_id =" . $inputs['user_id'] . "'";   //客户id
        }
        if (isset($inputs['shopname']) && $inputs['shopname'] !== '') {
            $where = $where . "  and jcr_verify.shopname like '%" . $inputs['shopname'] . "%'";   //店铺名称
        }
        if (isset($inputs['repay_type']) && $inputs['repay_type'] !== '' && $inputs['repay_type'] !== '0') {
            $where = $where . "  and jcr_bill.repay_type ='" . $inputs['repay_type'] . "'";   //店铺名称
        }
        if (isset($inputs['start_time']) && $inputs['start_time'] !== '' && isset($inputs['end_time']) && $inputs['end_time'] !== '') {
            $where = $where . "  and jcr_bill.repay_time >='" . $inputs['start_time'] . "' && jcr_bill.repay_time <= '" . $inputs['end_time'] . "'";   //进件时间
        }
        return $where;
    }

    public function transferhandle($params)
    {
        $billinfo = $this->_jcr_bill->getinfobyid($params['bill_id']);
        if (empty($billinfo)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '标的不存在!');
        }
        if ($billinfo['has_repay'] !== '1') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '未还款的标不能过户!');
        }
        if ($billinfo['has_transfer'] !== '2') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '已过户的标无需过户!');
        }
        $sql = "update jcr_bill set has_transfer=1 where id=" . $params['bill_id'];
        $update = $this->_jcr_bill->updatebillnotify($sql);
        if ($update == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '过户失败!');
        } else {
            return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        }
    }

    //资金明细
    public function financial_details($params)
    {
        $rs = $this->_common->object_array(json_decode($this->_jcrpost->account_log_lists_admin(env('IFCAR_TOKEN', 'test'), $params['user_id'], $params['start_time'], $params['end_time'], $params['ba_type'])));

        if ($rs['error_no'] != 200) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_MSG);
        }

        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function update_credit($id, $params, $user_id)
    {
        if (!$id){
            $verifyInfo = $this->_jcr_verify->getginfobyuserid($user_id);
            $id = $verifyInfo['id'];
        }

        $dealer = $this->_file->listdealerimages(['verify_id'=>$id,'user_id'=>$user_id]);

        if ($dealer){
            $addcsrimage = $this->updatejcrimage($params['csrimgs'], $id, $user_id);
        }else{
            $addcsrimage = $this->addjcrimage($params['csrimgs'], $id, $user_id);
        }

        if (!$params['quota']){
            unset($params['quota']);
        }
        unset($params['csrimgs'],$params['csr_id']);

        if ($addcsrimage == false) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加车商资料失败');
        }

        $params['validity_time'] = strtotime("+6 Monday");
        $result = Jcr_verify::updateByUserId($user_id, $params);

        if ($result) {
            return $this->_common->output($result, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        }
    }

    public function update_image($params, $user_id)
    {

        $verifyInfo = $this->_jcr_verify->getginfobyuserid($user_id);
        $id = $verifyInfo['id'];

        $dealer = $this->_file->listdealerimages(['verify_id'=>$id,'user_id'=>$user_id]);

        if ($dealer){
            $csrimage = $this->updatejcrimage($params['csrimgs'], $id, $user_id);
        }else{
            $csrimage = $this->addjcrimage($params['csrimgs'], $id, $user_id);
        }

        if ($csrimage){
            return $this->_common->output($csrimage, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        }

        return $csrimage;

    }

    public function getCarCsrList($p, $n, $params)
    {
        $result = Jcr_csr::getCarCsrList($p, $n, $params);

        if ($result) {
            return $this->_common->output($result, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        }
    }

    public function listcarprice($p, $n, $params)
    {
        $result = Jcr_car_history::getList($p, $n, $params);

        if ($result) {
            return $this->_common->output($result, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        }
        return $this->_common->output([], Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function getcarprice($id)
    {
        $info = Jcr_car_history::getDetailById($id);

        $rs = $this->_common->object_array(json_decode($this->_che300post->getUsedCarPrice($info['model_id'], $info['zone'], $info['reg_date'], $info['mile'])));

        if ($rs['status'] == '1') {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功

        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

}

