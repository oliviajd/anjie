<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\table\Fico_log;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * fico的接口
 *
 * @author win7
 */
class Ficopost
{
    public $reason_code = array(
        '1001'=>'贷记借记账户不活跃',
        '1002'=>'借记账户账龄过短或未知',
        '1003'=>'贷记账户账龄过短或未知',
        '1004'=>'入网时长过短或未知',
        '1005'=>'借记账户余额',
        '1006'=>'借记账户入账金额',
        '1007'=>'借记账户出账金额',
        '1008'=>'借记账户出账频率',
        '1009'=>'借记账户取现金额',
        '1010'=>'借记账户取现频率',
        '1011'=>'贷记账户余额',
        '1012'=>'贷记账户取现频率',
        '1013'=>'贷记账户额度使用',
        '1014'=>'贷记账户线上消费频率',
        '1015'=>'贷记账户分期消费',
        '1016'=>'借记账户使用情况',
        '1017'=>'近期活跃过的P2P平台数',
        '1018'=>'近期在P2P平台上的申请次数',
        '1019'=>'最近一次P2P平台贷款申请距今时间短',
        '1020'=>'最近一次P2P平台注册距今时间短',
        '1021'=>'近期在P2P平台上的注册次数',
        '1022'=>'近期在P2P平台有过逾期',
        '1023'=>'高风险投资交易次数',
        '1024'=>'P2P投资交易次数',
        '1025'=>'近期电商消费情况',
        '1026'=>'近期商旅情况',
        '1027'=>'近期休闲娱乐情况',
        '1028'=>'近期贷记账户取现情况',
        '1029'=>'贷记借记账户交易频率',
        '1030'=>'夜间消费情况',
        '1031'=>'近期消费情况',
        '1032'=>'贷记账户还款情况',
        '1033'=>'近期付费电视使用情况',
        '1034'=>'近期借记贷记账户活跃度',
        '1035'=>'理财账户账龄短或未知',
        '1036'=>'理财账户夜间交易次数',
        '1037'=>'理财账户出账交易次数',
        '1038'=>'理财账户余额',
        '1039'=>'理财账户不活跃',
        '1040'=>'最新注册的理财账户距今时间短或未知',
        '1041'=>'贷记卡帐户线上消费金额',
        '1042'=>'借记卡帐户线上消费次数',
        '1043'=>'移动应用行为',
    );

    /**
     * fico数据查询
     * @param 
     * </p>
     * @return 
     * 
     */
    public function ficoquery($workinfo) 
    {
        $this->_fico_log = new Fico_log();
        $this->_common = new Common();
        $param = array(
            'clientID' => env('FICO_clientID'),
            'password' => env('FICO_password'),
            'serviceCode' => env('FICO_serviceCode'),
            'txnID' => $workinfo['id'],
            'pboc'=> '2',
            'mobile' => md5($workinfo['customer_telephone']),
            'mobHeader' =>substr($workinfo['customer_telephone'], 0, 3),
            'idCard'=> md5($workinfo['customer_certificate_number']),
        );
        //xml拼接
        $xmldata = "<?xml version='1.0' encoding='UTF-8'?><Request ";
        foreach ($param as $key => $value) {
            $xmldata = $xmldata . $key . "='" . $value . "' ";
        }
        $xmldata = $xmldata . "/>";
        $url = env('FICO_URL') . '/BigDataScore/GetScore';
        $r = curl_post($url, $xmldata);
        $rs = $this->_common->object_array(simplexml_load_string($r));
        $retCode = $rs['@attributes']['retCode'];
        $addlog = $this->_fico_log->addlog($url, json_encode($param), $r, $retCode);       
        return simplexml_load_string($r);
    }
}
