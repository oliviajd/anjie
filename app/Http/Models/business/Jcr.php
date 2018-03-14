<?php

namespace App\Http\Models\business;

use App\Http\Models\table\Jcr_car_history;
use App\Http\Models\table\Jcr_common_value;
use App\Http\Models\table\Jcr_csr_car;
use function GuzzleHttp\Psr7\str;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Common;
use App\Http\Models\common\Jcrpost;
use App\Http\Models\common\Che300post;
use App\Http\Models\table\Jcr_users;
use App\Http\Models\table\Jcr_verify;
use App\Http\Models\table\Jcr_file;
use App\Http\Models\table\Jcr_suggestions;
use App\Http\Models\table\Suggestion_file;
use App\Http\Models\table\Jcr_csr;
use App\Http\Models\table\Anjie_file_class;
use App\Http\Models\business\File;
use App\Http\Models\business\Message;
use App\Http\Models\business\Workflowcsr;
use App\Http\Models\table\Jcr_bill;
use Mail;

class Jcr extends Model
{
    protected $_jcr_users = null;

    public function __construct()
    {
        parent::__construct();
        $this->_common = new Common();
        $this->_jcr_users = new Jcr_users();
        $this->_jcr_verify = new Jcr_verify();
        $this->_jcr_file = new Jcr_file();
        $this->_jcr_csr = new Jcr_csr();
        $this->_anjie_file_class = new Anjie_file_class();
        $this->_jcrpost = new Jcrpost();
        $this->_che300post = new Che300post();
        $this->_file = new File();
        $this->_jcr_suggestions = new Jcr_suggestions();
        $this->_suggestion_file = new Suggestion_file();
        $this->_message = new Message();
        $this->_workflowcsr = new Workflowcsr();
        $this->_jcr_bill = new Jcr_bill();
    }

    //聚车融登录接口
    public function login($params)
    {
        $r = $this->_common->object_array(json_decode($this->_jcrpost->login($params['loginname'], $params['password_md5'], $params['from'], $params['device'])));
        if ($r['error_no'] == 200) {
            $issetuser = $this->_jcr_users->getjcruserinfo($params['loginname']);
            if (empty($issetuser)) {
                $params['token'] = $r['result']['token']['token'];
                $params['user_id'] = $r['result']['user']['user_id'];
                $params['loginname'] = $r['result']['user']['loginname'];
                $params['realname'] = $r['result']['user']['realname'];
                $insertuser = $this->_jcr_users->addjcrusers($params);
                if ($insertuser == false) {
                    return $this->_common->output(null, Constant::ERR_FAILED_NO, '登录信息插入失败');
                }
            } else {
                $params['token'] = $r['result']['token']['token'];
                $params['user_id'] = $r['result']['user']['user_id'];
                $params['loginname'] = $r['result']['user']['loginname'];
                $params['realname'] = $r['result']['user']['realname'];
                $updatejcrusers = $this->_jcr_users->updatejcrusers($params);
                if ($updatejcrusers == false) {
                    return $this->_common->output(null, Constant::ERR_FAILED_NO, '登录信息插入失败');
                }
            }
        }
        return $r;
    }

    //聚车融注册发送验证码
    public function regmobilecode($params)
    {
        $r = $this->_common->object_array(json_decode($this->_jcrpost->reg_mobile_code($params['mobile'])));
        return $r;
    }

    //给已有用户发送手机验证码
    public function mobilecode($params)
    {
        $r = $this->_common->object_array(json_decode($this->_jcrpost->mobile_code($params['mobile'])));
        return $r;
    }

    //注册
    public function reg($params)
    {
        $r = $this->_common->object_array(json_decode($this->_jcrpost->reg($params['channel_code'], $params['mobile'], $params['password'], $params['confirm_password'], $params['invite_userid'], $params['mobile_code'], $params['mobile_auth'], $params['channel'])));
        if ($r['error_no'] == 200) {
            $params['token'] = '';
            $params['user_id'] = '';
            $params['loginname'] = $params['mobile'];
            $params['realname'] = '';
            $params['from'] = '';
            $params['device'] = '';
            $insertuser = $this->_jcr_users->addjcrusers($params);
            if ($insertuser == false) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '注册信息插入失败');
            }
        }
        return $r;
    }

    //注册
    public function reglogin($params)
    {
        $params['from'] = 'juchedai';  // 登录渠道
        $r = $this->_common->object_array(json_decode($this->_jcrpost->reglogin( $params['mobile'],$params['mobile_code'], $params['mobile_auth'], $params['channel'], $params['device'], $params['is_borrower'])));

        if ($r['error_no'] == 200) {
            $issetuser = $this->_jcr_users->getjcruserinfo($r['result']['user']['loginname']);
            if (empty($issetuser)) {
                $params['token'] = $r['result']['token']['token'];
                $params['user_id'] = $r['result']['user']['user_id'];
                $params['loginname'] = $r['result']['user']['loginname'];
                $params['realname'] = $r['result']['user']['realname'];
                $result = $this->_jcr_users->addjcrusers($params);
            } else {
                $params['token'] = $r['result']['token']['token'];
                $params['user_id'] = $r['result']['user']['user_id'];
                $params['loginname'] = $r['result']['user']['loginname'];
                $params['realname'] = $r['result']['user']['realname'];
                $result = $this->_jcr_users->updatejcrusers($params);
            }
            if ($result == false) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '登录信息插入失败');
            }

        }
        return $r;
    }

    //开通银行存管
    public function baAccountOpen($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        if ($userinfo['verify_status'] !== '1') {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '请先进行车商认证');
        }
        $check = $this->_jcr_verify->getginfobyuseridandstatus($userinfo['user_id'], '1');
        if (empty($check)) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '请先进行车商认证');
        }
        $params['realname'] = $check['name'];               //姓名
        $params['id_No'] = $check['certificate_number'];    //身份证号码
        $r = $this->_common->object_array(json_decode($this->_jcrpost->ba_account_open($params['token'], $params['realname'], $params['id_No'], $params['card_No'], $params['sms_code'], $params['channel'], $params['sms_auth'])));
        if ($r['error_no'] == 200) {
            $accountId = $r['result']['accountId'];  //电子账户
            $params['accountId'] = $r['result']['accountId'];  //电子账户
            $params['acqRes'] = $r['result']['acqRes'];
            $params['user_id'] = $userinfo['user_id'];
            $baAccountOpen = $this->_jcr_users->baAccountOpen($params);
            $addcardno = $this->_jcr_verify->addcardno($params);
            if ($baAccountOpen == false || $addcardno == false) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '开通银行存管失败');
            }
            $finance_account_add_admin = $this->_common->object_array(json_decode($this->_jcrpost->finance_account_add_admin($userinfo['user_id'], $params['realname'], $params['id_No'], $userinfo['loginname'])));  //添加主融资账号
            if ($finance_account_add_admin['error_no'] !== 200) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '开通融资账号失败');
            }
            if ($check['verify_type'] == '1') {
                $params['verify_type'] = '2';
            } elseif ($check['verify_type'] == '2') {
                $params['verify_type'] = '1';
            }
            $check = $this->_jcr_verify->getginfobyuseridandstatus($userinfo['user_id'], '1');
            $finance_account_sub_add = $this->_common->object_array(json_decode($this->_jcrpost->finance_account_sub_add($params['token'], $params['verify_type'], '', '', $check['shopname'], $check['name'], '江西银行', $accountId, $params['id_No'], $userinfo['loginname'])));  //开通融资子账号
            if ($finance_account_sub_add['error_no'] !== 200) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '开通融资子账号失败');
            }
            $finance_account_sub_id = $finance_account_sub_add['result']['finance_account_sub_id'];
            $addfinanceid = $this->_jcr_users->addfinanceid($finance_account_sub_id, $userinfo['user_id']);
            if ($addfinanceid == false) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '融资子账号入库失败');
            }
        }
        return $this->_common->output($r['result'], $r['error_no'], $r['error_msg']);
    }

    //银行存管发送验证码
    public function basmscodeapply($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $r = $this->_common->object_array(json_decode($this->_jcrpost->ba_sms_code_apply($params['token'], $params['srv_code'], $params['mobile'])));
        return $r;
    }

    public function getuserinfo($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $verfiyinfo = $this->_jcr_verify->getginfobyuserid($userinfo['user_id']);
        $r = $this->_common->object_array(json_decode($this->_jcrpost->user_get($params['token'])));
        if ($r['error_no'] == 200) {
            $jcrinfo = $this->_jcr_users->getjcruserinfo($r['result']['user']['loginname']);
            if (!empty($jcrinfo)) {
                if ($jcrinfo['head_portrait'] !== '' && $jcrinfo['head_portrait'] !== null) {
                    $jcrinfo['head_portrait'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $jcrinfo['head_portrait'];
                }
                $r['result']['user']['head_portrait'] = $jcrinfo['head_portrait'];
                $r['result']['user']['verify_status'] = $jcrinfo['verify_status'];
                $r['result']['user']['bankaccount_status'] = $jcrinfo['bankaccount_status'];
                $r['result']['user']['name'] = $jcrinfo['name'];
            } else {
                $r['result']['user']['head_portrait'] = '';
                $r['result']['user']['verify_status'] = '2';
                $r['result']['user']['bankaccount_status'] = '2';
                $r['result']['user']['name'] = 'jcd000';
            }
        }

        if (!empty($verfiyinfo)) {
            $r['result']['user']['creditline'] = $verfiyinfo['quota'];
            $r['result']['user']['use_creditline'] = $verfiyinfo['use_quota'];
            $r['result']['user']['certificate_number'] = $verfiyinfo['certificate_number'];
        } else {
            $r['result']['user']['certificate_number'] = '';
            $r['result']['user']['creditline'] = '';
            $r['result']['user']['use_creditline'] = '';
        }
        return $this->_common->output($r['result'], $r['error_no'], $r['error_msg']);
    }

    public function setheadportrait($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $rs = $this->_jcr_users->setheadportrait($params);
        if ($rs !== false) {
            $userinfo = $this->getuserinfo($params);
            return $userinfo;
        }
        return $this->_common->output(null, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
    }

    //认证信息
    public function jcrverify($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        if ($params['reverify'] == '1') {
            $params['account'] = $userinfo['loginname'];
            $params['type'] = '3';
            $checkmessage = $this->_message->checkmessage($params);
            if ($checkmessage == false) {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '请输入正确的验证码');
            }
        }
        $isIdCard = json_decode($this->_common->isIdCard($params['certificate_number']));     //验证是否身份证号码
        if ($isIdCard->error_no !== 200) {
            return json_encode($isIdCard);
        }
        $params['sex'] = $this->_common->get_xingbie($params['certificate_number']);  //根据身份证号码获取客户性别
        $params['age'] = $this->_common->getAgeByID($params['certificate_number']);  //根据身份证号码获取客户年龄
        $params['user_id'] = $userinfo['user_id'];
        $params['loginname'] = $userinfo['loginname'];
        //写入业务信息
        $check = $this->_jcr_verify->getginfobyuseridandstatus($params['user_id'], '1');
        $check2 = $this->_jcr_verify->getginfobyuseridandstatus($params['user_id'], '3');
        if ($params['reverify'] == '1') {
            if (empty($check) || !empty($check2)) {    //未认证或者认证中不可以再认证
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '认证中或未认证不能重新认证！');
            }
            if ($userinfo['bankaccount_status'] == '1') {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '已开通银行存管，不能重新认证');
            }
            $params['verify_id'] = $check['id'];
            $rs = $this->reverify($userinfo, $params);
        } else {
            if (!empty($check2) || !empty($check)) {    //已通过或者认证中不可以再认证
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '认证信息已存在！');
            }
            $rs = $this->_verify($userinfo, $params);
        }
        return $rs;
    }

    //初次认证
    public function _verify($userinfo, $params)
    {
        $jcrverfiy = $this->_jcr_verify->jcrverify($params);    //写入业务申请，添加业务
        if ($jcrverfiy == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '认证信息提交失败');
        }
        //插入图片信息
        $imagers = $this->addjcrimage($params['imgs'], $jcrverfiy['id'], $userinfo['user_id']);  //添加图片
        if ($imagers !== true) {
            return $imagers;
        }
        $verfiyuser = $this->_jcr_users->verfiyuser($userinfo['user_id'], '3');
        if ($verfiyuser == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '用户认证失败');
        }
        return $this->_common->output($jcrverfiy, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功

    }

    //重新认证
    public function reverify($userinfo, $params)
    {
        $params['verify_status'] = '5';
        $storageverify = $this->_jcr_verify->storageverify($params);
        if ($storageverify == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '暂存认证信息失败');
        }
        $params['status'] = '3';
        $storageverifyfile = $this->_jcr_file->storageverifyfile($params);
        if ($storageverifyfile == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '暂存认证信息失败！');
        }
        $_verify = $this->_verify($userinfo, $params);
        return $_verify;
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
                $param['filename'] = isset($v['filename']) ? $v['filename']:'';
                $imagers = $this->_jcr_file->addimage($param);  //添加图像逻辑
            }
        }
        if ($imagers == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '添加图片失败!');
        }
        return true;
    }

    //验证手机号是否正确
    public function checkaccount($param)
    {
        $userinfo = $this->_jcr_users->getuserinfobytoken($param['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        if ($userinfo['loginname'] !== $param['account']) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '验证的手机号必须与当然登录的手机号相同!');
        }
        return true;
    }

    public function getverifyinfo($param)
    {
        $userinfo = $this->_jcr_users->getuserinfobytoken($param['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $rs = $this->_jcr_verify->getginfobyuseridandstatus($userinfo['user_id'], '1');
        if (empty($rs)) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '获取认证信息不存在');
        }
        $param['verify_id'] = $rs['id'];
        $rs['imgs'] = $this->_file->listjcrimages($param);  //列出图像、视频
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '获取认证信息失败');
        }
    }

    //车商融的申请  先判断有没有开通银行存管账号，再判断有没有认证
    public function csrrequest($param)
    {
        $userinfo = $this->_jcr_users->getuserinfobytoken($param['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
//        if ($userinfo['verify_status'] !== '1' || $userinfo['bankaccount_status'] !== '1') {
//            return $this->_common->output(null, Constant::ERR_FAILED_NO, '未认证或未开通融资账号不能提交融资申请');
//        }
        $creditline = 50;  //授信额度暂时是50万
        $rs = $this->_jcr_verify->getginfobyuseridandstatus($userinfo['user_id'], '1');
        if (!empty($rs)) {
            $creditline = $rs['quota'] > 0 ?$rs['quota']:200;
        }
        $where = " where user_id = " . $userinfo['user_id'] . " and crsrequest_status = 1";
        $credit_processing = $this->_jcr_csr->getdetail($where);

        if (isset($rs['use_quota'])){
            $total = $rs['use_quota'] > 0 ?$rs['use_quota']:0;
        }else{
            $total = 0;
            foreach ($credit_processing as $key => $value) {
                if ($value['money'] == '' || $value['money'] == null) {
                    $total = $total + floatval($value['money_request']);
                } else {
                    $total = $total + floatval($value['money']);
                }
            }
        }

        if ($total + floatval($param['money']) > $creditline) {
            return $this->_common->output(null, Constant::ERR_MORE_THAN_LIMIT_NO,  Constant::ERR_MORE_THAN_LIMIT_MSG);
        }
        $where = " where 1=1";
        $count = $this->_jcr_csr->getcount($where);
        $param['csr_no'] = 'CS' . sprintf("%09d", $count['count']);      //商机编号
        $verifyrs = $this->_jcr_verify->getginfobyuseridandstatus($userinfo['user_id'], '1');
        if ($verifyrs) {
            $jcrrs = $this->_jcr_csr->csrrequest($param, $userinfo['user_id']);
            $this->_jcr_verify->incrementField(['user_id'=>$userinfo['user_id']],'use_quota',$param['money'] );
            $jcrrs['name'] = $verifyrs['name'];
            $jcrrs['shopname'] = $verifyrs['shopname'];
            $createprocess = $this->_workflowcsr->createprocess($userinfo['user_id'], $jcrrs);
            if ($createprocess !== false) {
                return $this->_common->output($jcrrs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
            } else {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '融资申请提交失败');
            }
        } else {
            $param['item_status'] = 4;
            $param['user_id'] = $userinfo['user_id'];
            $param['money_request'] = $param['money'];
            $param['deadline_request'] = $param['deadline'];
            $param['rate_request'] = $param['rate'];
            unset($param['money'],$param['deadline'],$param['rate'],$param['token']);
            $jcrrs = $this->_jcr_csr->createCsr($param);

            if ($jcrrs !== false) {
                return $this->_common->output($jcrrs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
            } else {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, '融资申请提交失败');
            }
        }

    }

    //意见反馈提交
    public function suggestionssubmit($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $params['user_id'] = $userinfo['user_id'];
        $suggestionssubmit = $this->_jcr_suggestions->suggestionssubmit($params);    //写入业务申请，添加业务
        if ($suggestionssubmit == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '意见反馈提交失败');
        }
        //插入图片信息
        if (isset($params['imgs']) && is_array($params['imgs'])) {
            $imagers = $this->addsuggestionimage($params['imgs'], $suggestionssubmit['id'], $userinfo['user_id']);  //添加图片
            if ($imagers !== true) {
                return $imagers;
            }
        }
        return $this->_common->output($suggestionssubmit, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    //传入imgs和suggestion_id插入图片
    public function addsuggestionimage($imgs, $suggestion_id, $user_id)
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
            $param['suggestion_id'] = $suggestion_id;
            $curren_length = $this->_suggestion_file->countBySuggestionidAndFileclassid($param);
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
                $param['suggestion_id'] = $suggestion_id;
                $imagers = $this->_suggestion_file->addimage($param);  //添加图像逻辑
            }
        }
        if ($imagers == false) {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '添加图片失败!');
        }
        return true;
    }

    //融资申请记录
    public function csrrequestrecord($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $order = " order by create_time desc";
        $params['user_id'] = $userinfo['user_id'];
        $limitout = '';
        if ($params['page'] && $params['size']) {
            $limitout = " limit " . intval(($params['page'] - 1) * $params['size']) . ', ' . intval($params['size']);
        }
        if ($params['type'] == '1') {   //上标申请之前,未征信加征信已通过但是还没上标的   (has_bill=2 and crsrequest_status=1) || crsrequest_status=0
            $where = " where ((a.has_bill = '2' and a.crsrequest_status='1') or a.crsrequest_status ='0' or (b.status = '0')) and a.user_id = " . $params['user_id'];
        } elseif ($params['type'] == '2') { //上标申请后   需要查询jcr_bill   has_bill=1   has_loan = 2
            $where = " where (a.has_bill =1 and a.has_loan in ('2', '3') and (b.status = 'ONLINE_SUCCESS' or b.status = 'USER_TENDER')) and a.user_id =  " . $params['user_id'];
        } elseif ($params['type'] == '3') { //USER_TENDER 已放款   需要查询jcr_bill   has_bill==1  has_loan=1
            $where = " where (a.has_bill =1 and a.has_loan in ('1', '2')) and a.user_id =  " . $params['user_id'];
        } elseif ($params['type'] == '4') { //crsrequest_status==2的    has_bill=2 and crsrequest_status==2
            $where = " where ((a.has_bill=2 and a.crsrequest_status=2) or b.status = 'VERIFY_FAILED') and a.user_id =  " . $params['user_id'];
        }

        $rs['rows'] = $this->_jcr_csr->getdetail3($where, $order, $limitout);
        $rs['total'] = $this->_jcr_csr->getcount3($where);
        if ($params['type'] == '2') {  //取未放款的标
            foreach ($rs['rows'] as $key => $value) {
                $rs['rows'][$key]['bills'] = $this->_jcr_bill->getloanbycsrid($value['id'], '2');
            }
        }
        if ($params['type'] == '3') {  //取已放款的标
            $sql = "select *,jcr_bill.id as bill_id  from jcr_bill, jcr_csr where jcr_bill.has_loan = 1 and jcr_csr.id = jcr_bill.csr_id and jcr_csr.user_id = " . $userinfo['user_id'];
            $rs['rows'] = $this->_jcr_csr->getbysql($sql);
            $sql = "select count(1) as count from jcr_bill, jcr_csr where jcr_bill.has_loan = 1 and jcr_csr.id = jcr_bill.csr_id and jcr_csr.user_id = " . $userinfo['user_id'];
            $rs['total'] = $this->_jcr_csr->getonebysql($sql);
            $prepayment_status = config('common.prepayment_status');
             foreach ($rs['rows'] as $key => $value) {
                 $detail = Jcr_csr_car::getDetailById($value['history_id']);
                 if($detail){
                     $rs['rows'][$key]['reg_date'] = $detail['reg_date'];
                     $rs['rows'][$key]['mile'] = $detail['mile'];
                     $rs['rows'][$key]['series_name'] = $detail['series_name'];
                     $rs['rows'][$key]['zone_name'] = $detail['zone_name'];
                 }

                 if ($value['prepayment_status']){
                     $rs['rows'][$key]['prepayment_status'] = ['id'=>$value['prepayment_status'],'text'=> $prepayment_status[$value['prepayment_status']]];
                 }else{
                     $rs['rows'][$key]['prepayment_status'] = ['id'=>0,'text'=> current($prepayment_status)];
                 }
             }
        }
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    //获取还款计划接口
    public function borrowplan($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $rs = $this->_common->object_array(json_decode($this->_jcrpost->borrow_repay_lists($params['token'], $params['start_time'], $params['end_time'], $params['status'], $params['page'], $params['size'])));
        if ($rs['error_no'] == 200) {
            $need_principal = 0;
            foreach ($rs['result']['rows'] as $key => $value) {
                $finance_bill_id = $value['finance_bill_id'];
                $rs['result']['rows'][$key]['billinfo'] = $this->_jcr_bill->getdetailbyfinancebillid($finance_bill_id);
                $rs['result']['rows'][$key]['csrinfo'] = $this->_jcr_csr->getinfobycsrid($rs['result']['rows'][$key]['billinfo']['csr_id']);
                $accountmoney = $value['repay_capital'] - $value['repay_capital_yes'];
                $need_principal = $need_principal + $accountmoney;
            }
            $rs['result']['need_principal'] = $need_principal;
            $where = " where a.has_repay = 2 and a.has_loan =1 and a.csr_id = b.id and b.user_id = c.user_id and b.user_id=" . $userinfo['user_id'];
            $rs['result']['need_loan'] = $this->_jcr_bill->getcount($where);
        }
        return $this->_common->output($rs['result'], Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    //获取所有的城市
    public function getallcity()
    {
        $rs = $this->_common->object_array(json_decode($this->_che300post->getAllCity()));
        if ($rs['status'] == '1') {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

    //获取所有的品牌列表
    public function getcarbrandlist()
    {
        $rs = $this->_common->object_array(json_decode($this->_che300post->getCarBrandList()));

        if ($rs['status'] == '1') {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

    //获取车系列表
    public function getcarserieslist($params)
    {
        $rs = $this->_common->object_array(json_decode($this->_che300post->getCarSeriesList($params['brandId'])));
        if ($rs['status'] == '1') {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

    //获取车型列表
    public function getcarmodellist($params)
    {
        $rs = $this->_common->object_array(json_decode($this->_che300post->getCarModelList($params['seriesId'])));
        if ($rs['status'] == '1') {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

    //获取车型列表
    public function identifymodelbyvin($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $rs = $this->_common->object_array(json_decode($this->_che300post->identifyModelByVIN($params['vin'])));
        if ($rs['status'] == '1') {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

    //车辆估值接口
    public function getusedcarprice($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        unset($params['token']);

        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $rs = $this->_common->object_array(json_decode($this->_che300post->getUsedCarPrice($params['model_id'], $params['zone'], $params['reg_date'], $params['mile'])));
        if ($rs['status'] == '1') {
            $params['user_id'] = $userinfo['user_id'];
            $params['result'] = json_encode($rs);
            $params['created_at'] = date('Y-m-d H:i:s');

            if ($params['type']  == 1){
                unset($params['type']);
                $id = Jcr_csr_car::addCarCsr($params);
            }else{
                unset($params['type']);
                $id = Jcr_car_history::addCarHistory($params);
            }

            if ($id) {
                $rs['history_id'] = $id;
                $rs['reg_date'] = $params['reg_date']; //车辆上牌日期，如2012-01
                $rs['mile'] = $params['mile'];//车辆行驶里程，单位是万公里//车型id
                $rs['zone_name'] = $params['zone_name']; //城市名称
                return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
            } else {
                return $this->_common->output(null, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //失败
            }

        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

    //首页总数
    public function gettotalfinancingnumber()
    {
        $rs = Jcr_common_value::getDetail(['method_name' => 'gettotalfinancingnumber', 'key' => 'START_TIME']);
        $today = date('Y-m-d');
        //判断是否是今日
        if ($rs['value'] == $today) {
            $res = Jcr_common_value::getDetail(['method_name' => 'gettotalfinancingnumber', 'key' => 'START_NUM']);
            return $this->_common->output(intval($res['value']), Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            $res = Jcr_common_value::getDetail(['method_name' => 'gettotalfinancingnumber', 'key' => 'START_NUM']);
            $new = $res['value'] + rand(5, 20);
            Jcr_common_value::updateById($res['id'], ['value' => $new]);
            Jcr_common_value::updateById($rs['id'], ['value' => $today]);
            return $this->_common->output($new, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        }
    }

    //估价历史列表
    public function gethistorylist($p,$n,$params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        unset($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $params['user_id'] = $userinfo['user_id'];

        $re = Jcr_car_history::getList($p,$n,$params);

        foreach ($re['rows'] as $k=>$v){
            $result = $this->_common->object_array(json_decode($v['result']));
            $re['rows'][$k]['url'] = $result['url'];
            $re['rows'][$k]['dealer_price'] = $result['dealer_price'];
            $re['rows'][$k]['dealer_buy_price'] = $result['dealer_buy_price'];
            unset($re['rows']['result']);
        }

        return $this->_common->output($re, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }

    //估价历史
    public function gethistorydetail($params)
    {
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($params['token']);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }

        $re = Jcr_car_history::getDetailByParams(['id'=>$params['history_id'],'user_id'=>$userinfo['user_id']]);

        if (!$re){
            return $this->_common->output(null, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        }

        $rs = $this->_common->object_array(json_decode($this->_che300post->getUsedCarPrice($re['model_id'], $re['zone'], $re['reg_date'], $re['mile'])));
        $rs['reg_date'] = $re['reg_date'];//上牌时间
        $rs['mile'] = $re['mile'];//公里数
        $rs['zone_name'] = $re['zone_name']; //城市名称
        if ($rs['status'] == '1') {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(null, $rs['error_code'], $rs['error_msg']);    //失败
        }
    }

}

