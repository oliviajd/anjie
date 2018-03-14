<?php

namespace App\Http\Controllers;
use App\Http\Models\business\System;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Auth;

use Mail;

class SystemController extends Controller
{ 
    private $_system = null;
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_system = new System();
        $this->_auth = new Auth();
    }
    /**
     * 获取登录日志
     */
    public function loginlog()
    {
        return view('admin.system.loginlog')->with('title', '登录日志');
    }
    /**
     * 获取登录日志
     */
    public function actionlog()
    {
        return view('admin.system.actionlog')->with('title', '操作日志');
    }
    /**
     * 人工审核
     */
    public function manaudit()
    {
        return view('admin.system.manaudit')->with('title', '人工审核');
    }
    /**
     * 工行数据查询
     */
    public function bankdataquery()
    {
        return view('admin.system.bankdataquery')->with('title', '工行数据查询');
    }
    /**
     * 查询工行数据接口
     */
    public function listbankdataquery()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $param['name'] = Request::input('name', ''); //客户姓名
        $param['id_card'] = Request::input('id_card', '');  //身份证号码
        $rs = $this->_system->listbankdataquery($param);
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
//获取登录日志
    public function getloginlog()
    {
        $page =  intval(Request::input('page', '1'));
        $size =  intval(Request::input('size', '20'));
        $condition['keyword'] = Request::input('keyword', ''); //查询的关键词
        $condition['start_time'] = Request::input('start_time', '');  //开始时间
        $condition['end_time'] = Request::input('end_time', '');  //结束时间
        $order = 'id desc';
        $lists = $this->_system->listloginlog($page, $size, $condition);//列出申请列表的数据
        foreach ($lists as $key => $value) {
            $lists[$key]['time'] = date('Y/m/d H:i:s', $value['login_time']);
        }
        $r['rows'] = $lists;
        $r['total'] = $this->_system->countloginlog($condition);//获得申请列表的条数
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
//获取操作日志
    public function getactionlog()
    {
        $page =  intval(Request::input('page', '1'));
        $size =  intval(Request::input('size', '20'));
        $condition['merchant_no'] = Request::input('merchant_no', ''); //商户编号
        $condition['customer_certificate_number'] = Request::input('customer_certificate_number', ''); //身份证号码
        $condition['start_time'] = Request::input('start_time', '');  //开始时间
        $condition['end_time'] = Request::input('end_time', '');  //结束时间
        $order = 'id desc';
        $lists = $this->_system->listactionlog($page, $size, $condition);//列出申请列表的数据
        $r['rows'] = $lists;
        $r['total'] = $this->_system->countactionlog($condition);//获得申请列表的条数
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
//人工审核列表
    public function listmanaudit()
    {
        $page =  intval(Request::input('page', '1'));
        $size =  intval(Request::input('size', '20'));
        $condition['type'] = Request::input('audit_type', ''); //进件编号
        $lists = $this->_system->listmanaudit($page, $size, $condition);//列出申请列表的数据
        $r['rows'] = $lists;
        $r['total'] = $this->_system->countmanaudit($condition);//获得申请列表的条数
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
//修改人工审核
    public function updatemanaudit()
    {
        $params = Request::all();
        $update = $this->_system->updatemanaudit($params);
        if ($update) {
            return $this->_common->output($params, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    //获取省份
    public function getprovince()
    {
        $rs = $this->_system->getprovince();//获取省列表
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
        
    }
    //获取市
    public function getcity()
    {
        $pcode = Request::input('pcode', '');   //provincecode 省代码
        $rs = $this->_system->getcityBypcode($pcode);  //通过省代码获取市列表
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    //获取区
    public function gettown()
    {
        $pcode = Request::input('pcode', '');   //provincecode 省代码
        $rs = $this->_system->gettownBypcode($pcode); //通过市代码获取区列表
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    //获取市
    public function getcitybyname()
    {
        $name = Request::input('name', '');   //省名称
        $pcode = $this->_system->getprovincecodebyname($name);
        $rs = $this->_system->getcityBypcode($pcode);  //通过省代码获取市列表
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    //获取区
    public function gettownbyname()
    {
        $name = Request::input('name', '');   //市名称
        $pcode = $this->_system->getcitycodebyname($name);
        $rs = $this->_system->gettownBypcode($pcode); //通过市代码获取区列表
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }

    //获取申请件来源选择项
    public function getmerchantclass()
    {
        $merchant_class_no = Request::input('merchant_class_no', '');   //provincecode 省代码
        $merchant_class_no = $merchant_class_no ? $merchant_class_no : 'HB';
        $rs = $this->_system->getmerchantclass($merchant_class_no);
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    //获取申请件来源选择项
    public function getproductname()
    {
        $rs = $this->_system->getproductname();
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
    }
    //发邮件示范的代码，但是有一个问题，没法知道是否发送成功，不过如果发送失败，会退信给发送邮箱
    public function mail()
    {
        $image = storage_path('app/uploads/test.png');
        $name = '学院君';
        $flag = Mail::send('common.email',['name'=>$name,'imgPath'=>$image],function($message){
            $to = '737241700@qq.com';
            $message->to($to)->subject('主题是什么');
            $attachment = storage_path('app/uploads/test.docx');
            //在邮件中上传附件
            $message->attach($attachment,['as'=>"=?UTF-8?B?".base64_encode('测试文档')."?=.doc"]);
        });
    }
    //app端版本控制
    public function versioncontrol()
    {
        $params['current_version'] = Request::input('current_version', '');   //当前版本号
        $params['origin'] = Request::input('origin', 'ios');   //来源
        $rs =$this->_system->versioncontrol($params);
        return $rs;
    }

    public function anjieversion()
    {
        $params['current_version'] = Request::input('current_version', '');   //当前版本号
        $params['origin'] = Request::input('origin', 'ios');   //来源
        $rs =$this->_system->anjieversion($params);
        return $rs;
    }
}