<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;

// define('WORK_API_URL', 'http://work_admin.car.com/work_api.php/');
if (getenv('APP_ENV') == 'local') {
    define('JCR_API_URL', env('IFCAR_PATH','http://test-jcjr.apitest-feature.ifcar99.com'));
}
if (getenv('APP_ENV') == 'testing') {
    define('JCR_API_URL', env('IFCAR_PATH','http://test-jcjr.apitest-feature.ifcar99.com'));
}
if (getenv('APP_ENV') == 'production') {
    define('JCR_API_URL', env('IFCAR_PATH','https://www.ifcar99.com/api_v20/'));
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 流程实例
 *
 * @author win7
 */
class Jcrpost
{

    /**
     * 登录
     * @param 
     * </p>
     * @return 
     * 
     */
    public function login($loginname, $password_md5, $from, $device) 
    {
        $param = array(
            'loginname' => $loginname,
            'password_md5' => $password_md5,
            'from' => 'juchedai',
            'device' => $device,
        );
        $r = curl_post_jcr(JCR_API_URL . '/user/login', $param);
        return $r;
    }
    /**
     * 注册时的发送验证码
     * @param 
     * </p>
     * @return 
     * 
     */
    public function reg_mobile_code($mobile)
    {
        $param = array(
            'mobile' => $mobile,
            'srv_code' => 'register',
            'from' => 'juchedai',
        );
        $r = curl_post_jcr(JCR_API_URL . '/user/mobile/code/send', $param);
        return $r;
    }
    /**
     * 给已有用户发送手机验证码
     * @param 
     * </p>
     * @return 
     * 
     */
    public function mobile_code($mobile)
    {
        $param = array(
            'mobile' => $mobile,
            'from' => 'juchedai',
        );
        $r = curl_post_jcr(JCR_API_URL . '/user/mobile_code', $param);
        return $r;
    }
    /**
     * 注册
     * @param 
     * </p>
     * @return 
     * 
     */
    public function reg($channel_code, $mobile, $password, $confirm_password, $invite_userid, $mobile_code,$mobile_auth, $channel)
    {
        $param = array(
            'channel_code' => $channel_code,
            'mobile' => $mobile,
            'password' => $password,
            'confirm_password' => $confirm_password,
            'invite_userid' => $invite_userid,
            'mobile_code' => $mobile_code,
            'mobile_auth' => $mobile_auth,
            'channel' => $channel,
            'is_borrower' => '1',
        );
        $r = curl_post_jcr(JCR_API_URL . '/user/reg/v2', $param);
        return $r;
    }
    /**
     * 注册登录
     * @param
     * </p>
     * @return
     *
     */
    public function reglogin($mobile, $mobile_code,$mobile_auth, $channel,$device,$is_borrower)
    {
        $param = array(
            'mobile' => $mobile,
            'mobile_code' => $mobile_code,
            'mobile_auth' => $mobile_auth,
            'channel' => $channel,
            'device' => $device,
            'is_borrower' => $is_borrower,
            'from' => 'juchedai',
        );
        $r = curl_post_jcr(JCR_API_URL . '/user/code/login', $param);
        return $r;
    }
    /**
     * 存管开户
     * @param 
     * </p>
     * @return 
     * 
     */
    public function ba_account_open($token, $realname, $id_No, $card_No, $sms_code, $channel, $sms_auth)
    {
        $param = array(
            'token' => $token,
            'realname' => $realname,
            'id_No' => $id_No,
            'card_No' => $card_No,
            'sms_code' => $sms_code,
            'channel' => $channel,
            'sms_auth' => $sms_auth,
        );
        $r = curl_post_jcr(JCR_API_URL . '/ba/account/open', $param);
        return $r;
    }
     /**
     * 银行存管发送验证码
     * @param 
     * </p>
     * @return 
     * 
     */
    public function ba_sms_code_apply($token, $srv_code, $mobile)
    {
        $param = array(
            'token' => $token,
            'srv_code' => $srv_code,
            'mobile' => $mobile,
        );
        $r = curl_post_jcr(JCR_API_URL . '/ba/sms/code/apply', $param);
        return $r;
    }
    /**
     * 获取用户信息
     * @param 
     * </p>
     * @return 
     * 
     */
    public function user_get($token)
    {
        $param = array(
            'token' => $token
        );
        $r = curl_post_jcr(JCR_API_URL . '/user/get', $param);
        return $r;
    }
    /**
     * 开通融资主账号F:\wamp64\www\anjie\app\Http\Models\common\Jcrpost.php
     * @param 
     * </p>
     * @return 
     * 
     */
    public function finance_account_add_admin($user_id, $name, $id_card, $mobile)
    {
        $param = array(
            'token' => env('IFCAR_TOKEN', 'test'),
            'user_id' => $user_id,
            'name' => $name,
            'id_card' => $id_card,
            'mobile' => $mobile,
        );
        $r = curl_post_jcr(JCR_API_URL . '/finance/account/add/admin', $param);
        return $r;
    }
    /**
     * 开通融资子账号
     * @param 
     * </p>
     * @return 
     * 
     */
    public function finance_account_sub_add($token, $user_type, $main_business, $establish_time, $company, $name, $bank, $bank_card, $id_card, $mobile)
    {
        $param = array(
            'token' => $token,
            'user_type' => $user_type,
            'main_business'=>$main_business,
            'establish_time'=>$establish_time,
            'company' => $company,
            'name' => $name,
            'bank' => $bank,
            'bank_card' => $bank_card,
            'id_card' => $id_card,
            'mobile' => $mobile,
        );
        $r = curl_post_jcr(JCR_API_URL . '/finance/account/sub/add', $param);
        return $r;
    }
    /**
     * 开通融资子账号
     * @param 
     * </p>
     * @return 
     * 
     */
    public function finance_account_sub_update($token, $user_type, $main_business, $establish_time, $company, $name, $bank, $bank_card, $id_card, $mobile, $finance_account_sub_id)
    {
        $param = array(
            'token' => $token,
            'user_type' => $user_type,
            'main_business'=>$main_business,
            'establish_time'=>$establish_time,
            'company' => $company,
            'name' => $name,
            'bank' => $bank,
            'bank_card' => $bank_card,
            'id_card' => $id_card,
            'mobile' => $mobile,
            'finance_account_sub_id' => $finance_account_sub_id,
        );
        $r = curl_post_jcr(JCR_API_URL . '/finance/account/sub/update', $param);
        return $r;
    }
    /**
     * 添加融资申请
     * @param 
     * </p>
     * @return 
     * 
     */
    public function finance_bill_add($params)
    {
        $param = array(
            'token' => $params['token'],
            'money' => $params['money'],
            'car'=>$params['car'],
            'car_type'=>$params['car_type'],
            'advance' => $params['advance'],
            'payment_certificate' => $params['payment_certificate'],
            'paid_time' => $params['paid_time'],
            'attach' => $params['attach'],
            'name' => $params['name'],
            'id_card' => $params['id_card'],
            'borrow_type' => $params['borrow_type'],
            'credit_score' => intval($params['credit_score']),
            'manage_score' => intval($params['manage_score']),
            'assets_score' => intval($params['assets_score']),
            'debt_score' => intval($params['debt_score']),
            'conducive_score' => intval($params['conducive_score']),
            'borrower_attach' => $params['borrower_attach'],
            'finance_account_sub_id' => $params['finance_account_sub_id'],
        );
        $r = curl_post_jcr(JCR_API_URL . '/finance/bill/add', $param);
        return $r;
    }
    /**
     * 个人回款记录列表
     * @param 
     * </p>
     * @return 
     * 
     */
    public function borrow_repay_lists($token, $start_time, $end_time, $status, $page, $size)
    {
        $param = array(
            'token' => $token,
            'start_time' => $start_time,
            'end_time'=>$end_time,
            'status'=>$status,
            'page' => $page,
            'size' => $size,
        );
        $r = curl_post_jcr(JCR_API_URL . '/borrow/repay/lists', $param);
        return $r;
    }
    /**
     * admin回款记录列表
     * @param 
     * </p>
     * @return 
     * 
     */
    public function borrow_repay_lists_admin($token, $user_id, $ba_account_id, $status, $start_time, $end_time, $page, $size, $overdue)
    {
        $param = array(
            'token' => $token,
            'user_id' => $user_id,
            'ba_account_id'=> $ba_account_id,
            'status'=> $status,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'page' => $page,
            'size' => $size,
            'borrow_type' =>'8',
            'overdue' => $overdue,
        );
        $r = curl_post_jcr(JCR_API_URL . '/borrow/repay/lists/admin', $param);
        return $r;
    }

    public function account_log_lists_admin($token, $user_id, $starttime, $endtime, $ba_type)
    {
        $param = array(
            'token' => $token,
            'user_id' => $user_id,
            'starttime'=> $starttime,
            'endtime'=> $endtime,
            'ba_type' => $ba_type,
        );
        $r = curl_post_jcr(JCR_API_URL . '/account_log/lists/admin', $param);
        return $r;
    }

    public function user_borrower_stats_admin($token, $user_id)
    {
        $param = array(
            'token' => $token,
            'user_id' => $user_id,
        );
        $r = curl_post_jcr(JCR_API_URL . '/user/borrower/stats/admin', $param);

        if (!$r){
            return [];
        }
        return $r;
    }
}
function curl_post_jcr($post_url, $post_data) 
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $post_url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    return $error ? $error : $result;
}
