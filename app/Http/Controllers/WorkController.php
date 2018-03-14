<?php

namespace App\Http\Controllers;
use App\Http\Models\business\Work;
use App\Http\Models\business\Workplatform;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Role;
//主要用于展示静态页面
class WorkController extends Controller
{ 
    private $_work = null;

    public function __construct()
    {
        parent::__construct();
        $this->_work = new Work();
        $this->_role = new Role();
    }
    /**
     * 角色编辑页面
     */
    public function creditrequest()
    {
        return view('admin.workplatform.credit_request')->with('title', '人行征信申请');
    }

    /**
     * 征信报告列表页
     */
    public function inquire()
    {
        return view('admin.workplatform.inquire.index')->with('title', '征信报告');
    }
    /**
     * 征信报告列表页
     */
    public function detailinquire()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        $edit = array(
            'inquire' => true
        );
        return view('admin.workplatform.inquire.detailinquire')->with('title', '征信报告')
                ->with('detail', $rs)->with('edit', $edit)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit',array());
    }
    /**
     * 人工审核一审
     */
    public function artificialone()
    {
        return view('admin.workplatform.artificialapproval.index_one')->with('title', '人工审核一审');
    }
    /**
     * 人工审核二审
     */
    public function artificialtwo()
    {
        return view('admin.workplatform.artificialapproval.index_two')->with('title', '人工审核二审');
    }
    /**
     * 人工审核一审
     */
    public function detailartificialone()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => false,
            'loan' => false,
            'goods' => false,
            'job' => false,
            'spouse' => false,
            'contact' => false,
            'receiver' => false,
            'artifical_phone' => true,
            'artifical_manual' => true,
            'cost'=>false,
            'bondsman'=>false,
            'artificialone_opinion'=>true,
            'ficodata'=>false,
        );
        if (empty($rs)) {
            return view('errors.404');
        }
        return view('admin.workplatform.artificialapproval.artificial_one')->with('title', '人工审核一审')
                        ->with('detail', $rs)->with('edit', $edit)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit', array(
                    '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
        ));
    }
    /**
     * 人工审核二审
     */
    public function detailartificialtwo()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => false,
            'loan' => false,
            'goods' => false,
            'job' => false,
            'spouse' => false,
            'contact' => false,
            'receiver' => false,
            'artifical_phone' => true,
            'artifical_manual' => true,
            'cost'=>false,
            'bondsman'=>false,
            'artificialone_opinion'=>false,
            'artificialtwo_opinion'=>true,
            'ficodata'=>false,
        );
        if (empty($rs)) {
            return view('errors.404');
        }
        return view('admin.workplatform.artificialapproval.artificial_two')->with('title', '人工审核二审')
                        ->with('detail', $rs)->with('edit', $edit)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit', array(
                    '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
        ));
    }
    /**
     * 申请打款
     */
    public function applyremittance()
    {
        return view('admin.workplatform.applyremittance.index')->with('title', '申请打款');
    }
    /**
     * 打款审核
     */
    public function moneyaudit()
    {
        return view('admin.workplatform.moneyaudit.index')->with('title', '打款审核');
    }
    /**
     * 财务打款
     */
    public function finance()
    {
        return view('admin.workplatform.finance.index')->with('title', '财务打款');
    }
    /**
     * 回款确认
     */
    public function returnmoney()
    {
        return view('admin.workplatform.returnmoney.index')->with('title', '回款确认');
    }
    /**
     * 申请录入
     */
    public function inputrequest()
    {
        return view('admin.workplatform.inputrequest.index')->with('title', '申请录入');
    }
    /**
     * 申请录入详情
     */
    public function detailinputrequest()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => true,
            'loan' => true,
            'goods' => true,
            'job' => true,
            'spouse' => true,
            'contact' => true,
            'receiver' => false,
            'cost'=>true,
            'bondsman'=>true,
        );
        if (empty($rs)) {
            return view('errors.404');
        }
        $address['customer_province_code'] = $this->_role->getprovincecode($rs['customer_province']);  //省代码
        $address['customer_city_code'] = $this->_role->getcitycode($rs['customer_city']);          //市代码
        $address['company_province_code'] = $this->_role->getprovincecode($rs['company_province']);  //省代码
        $address['company_city_code'] = $this->_role->getcitycode($rs['company_city']);          //市代码
        $address['spouse_company_province_code'] = $this->_role->getprovincecode($rs['spouse_company_province']);  //省代码
        $address['spouse_company_city_code'] = $this->_role->getcitycode($rs['spouse_company_city']);          //市代码
        $address['customer_province_code'] = empty($address['customer_province_code']) ? '' : $address['customer_province_code']['code'];
        $address['customer_city_code'] = empty($address['customer_city_code']) ? '' : $address['customer_city_code']['code'];
        $address['company_province_code'] = empty($address['company_province_code']) ? '' : $address['company_province_code']['code'];
        $address['company_city_code'] = empty($address['company_city_code']) ? '' : $address['company_city_code']['code'];
        $address['spouse_company_province_code'] = empty($address['spouse_company_province_code']) ? '' : $address['spouse_company_province_code']['code'];
        $address['spouse_company_city_code'] = empty($address['spouse_company_city_code']) ? '' : $address['spouse_company_city_code']['code'];
        return view('admin.workplatform.inputrequest.detailinputrequest')->with('title', '申请录入详情')
                        ->with('detail', $rs)->with('edit', $edit)->with('item_instance_id', Request::input('item_instance_id', ''))->with('address',$address)->with('source_edit', array(
                    '1' => 1, '2' => 1, '3' => 1, '4' => 1, '5' => 1, '6' => 1, '7' => 1, '8' => 1,
        ));
    }
    /**
     * 捞件审批
     */
    public function taskback()
    {
        return view('admin.workplatform.taskback.index')->with('title', '捞件审批');
    }
    /**
     * 捞件审批
     */
    public function detailtaskback()
    {
        return view('admin.workplatform.taskback.detailtaskback')->with('title', '捞件审批');
    }
    /**
     * 寄件登记
     */
    public function sendtask()
    {
        return view('admin.workplatform.sendtask.index')->with('title', '寄件登记');
    }
    /**
     * 抄单登记
     */
    public function copytask()
    {
        return view('admin.workplatform.copytask.index')->with('title', '抄单登记');
    }
    /**
     * GPS登记
     */
    public function gps()
    {
        return view('admin.workplatform.gps.index')->with('title', 'GPS登记');
    }
    /**
     * 抵押、登记
     */
    public function mortgage()
    {
        return view('admin.workplatform.mortgage.index')->with('title', '抵押登记');
    }
    /**
     * 捞件管理
     */
    public function taskbackmanage()
    {
        return view('admin.workplatform.taskbackmanage.index')->with('title', '捞件管理');
    }
    /**
     * 捞件管理详情页
     */
    public function detailtaskbackmanage()
    {
        return view('admin.workplatform.taskbackmanage.detailtaskbackmanage')->with('title', '捞件管理详情页');
    }
    /**
     * 捞件管理
     */
    public function taskget()
    {
        return view('admin.workplatform.taskget.index')->with('title', '案件认领管理');
    }
    /**
     * 申请件查询
     */
    public function taskquery()
    {
        return view('admin.workplatform.taskquery.index')->with('title', '申请件查询(管理员)');
    }
    /**
     * 分区申请件查询
     */
    public function taskquerypartition()
    {
        return view('admin.workplatform.taskquerypartition.index')->with('title', '申请件查询（分区）');
    }
    /**
     *老系统数据
     */
    public function beforedata()
    {
        return view('admin.workplatform.beforedata.index')->with('title', '老系统数据');
    }
    public function getshow()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('work_id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        if (empty($rs)) {
            return view('errors.404');
        }
        $show = $this->_work->getshow($rs['current_item_id']);
        if (empty($show)) {
            $show = $this->_work->getshow('0');
        }
        return $this->_common->output($show, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
    }
    /**
     *老系统数据详情页
     */
    public function detailbeforedata()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        if (empty($rs)) {
            return view('errors.404');
        }
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => false,
            'loan' => false,
            'goods' => false,
            'job' => false,
            'spouse' => false,
            'contact' => false,
            'receiver' => false,
            'artifical_phone' => true,
            'artifical_manual' => true,
            'bondsman'=>false,
            'cost' => false,
        );
        $show = $this->_work->getshow($rs['current_item_id']);
        if (empty($show)) {
            $show = $this->_work->getshow('0');
        }
        return view('admin.workplatform.beforedata.detailbeforedata')->with('title', '申请件查询')
                        ->with('detail', $rs)->with('edit', $edit)->with('show', $show)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit', array(
                    '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
        ));
    }
    /**
     * 申请件查询
     */
    public function taskqueryself()
    {
        return view('admin.workplatform.taskqueryself.index')->with('title', '申请件查询');
    }
    /**
     * 申请件查询
     */
    public function taskquerymore()
    {
        return view('admin.workplatform.taskquery.indexmore')->with('title', '申请件查询-进阶');
    }
    /**
     * 申请件查询(管理员)
     */
    public function detailtaskquery()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        if (empty($rs)) {
            return view('errors.404');
        }
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => false,
            'loan' => false,
            'goods' => false,
            'job' => false,
            'spouse' => false,
            'contact' => false,
            'receiver' => false,
            'artifical_phone' => true,
            'artifical_manual' => true,
            'bondsman'=>false,
            'cost' => false,
            'ficodata'=>false,
        );
        $show = $this->_work->getshow($rs['current_item_id']);
        if (empty($show)) {
            $show = $this->_work->getshow('0');
        }
        return view('admin.workplatform.taskquery.detailtaskquery')->with('title', '申请件查询')
                        ->with('detail', $rs)->with('edit', $edit)->with('show', $show)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit', array(
                    '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
        ));
    }
    /**
     * 申请件查询(分区)
     */
    public function detailtaskquerypartition()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        if (empty($rs)) {
            return view('errors.404');
        }
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => false,
            'loan' => false,
            'goods' => false,
            'job' => false,
            'spouse' => false,
            'contact' => false,
            'receiver' => false,
            'artifical_phone' => true,
            'artifical_manual' => true,
            'bondsman'=>false,
            'cost' => false,
            'ficodata'=>false,
        );
        $show = $this->_work->getshow($rs['current_item_id']);
        if (empty($show)) {
            $show = $this->_work->getshow('0');
        }
        return view('admin.workplatform.taskquerypartition.detailtaskquerypartition')->with('title', '申请件查询')
                        ->with('detail', $rs)->with('edit', $edit)->with('show', $show)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit', array(
                    '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
        ));
    }
    /**
     * 申请件查询
     */
    public function detailtaskqueryself()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        if (empty($rs)) {
            return view('errors.404');
        }
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => false,
            'loan' => false,
            'goods' => false,
            'job' => false,
            'spouse' => false,
            'contact' => false,
            'receiver' => false,
            'artifical_phone' => true,
            'artifical_manual' => true,
            'bondsman'=>false,
            'cost' => false,
            'ficodata'=>false,
        );
        $show = $this->_work->getshow($rs['current_item_id']);
        if (empty($show)) {
            $show = $this->_work->getshow('0');
        }
        return view('admin.workplatform.taskqueryself.detailtaskqueryself')->with('title', '申请件查询')
                        ->with('detail', $rs)->with('edit', $edit)->with('show', $show)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit', array(
                    '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
        ));
    }
    /**
     * 申请件查询
     */
    public function taskqueryprovince()
    {
        return view('admin.workplatform.taskqueryprovince.index')->with('title', '申请件查询(省级)');
    }
    
    /**
     * 申请件查询(分区)
     */
    public function detailtaskqueryprovince()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        if (empty($rs)) {
            return view('errors.404');
        }
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => false,
            'loan' => false,
            'goods' => false,
            'job' => false,
            'spouse' => false,
            'contact' => false,
            'receiver' => false,
            'artifical_phone' => true,
            'artifical_manual' => true,
            'bondsman'=>false,
            'cost' => false,
            'ficodata'=>false,
        );
        $show = $this->_work->getshow($rs['current_item_id']);
        if (empty($show)) {
            $show = $this->_work->getshow('0');
        }
        return view('admin.workplatform.taskquerypartition.detailtaskquerypartition')->with('title', '申请件查询')
                        ->with('detail', $rs)->with('edit', $edit)->with('show', $show)->with('item_instance_id', Request::input('item_instance_id', ''))->with('source_edit', array(
                    '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
        ));
    }
    /**
     * 销售补件列表
     */
    public function salessupplement()
    {
        return view('admin.workplatform.salessupplement.index')->with('title', '销售补件');
    }
    /**
     * 销售补件详情
     */
    public function detailsalessupplement()
    {
        $workplatform = new Workplatform();
        $params['work_id'] = Request::input('id', '');   //工作id
        $rs = $workplatform->getInfoById($params);
        $edit = array(
            'from' => false,
            'inquire' => false,
            'basic2' => true,
            'loan' => true,
            'goods' => true,
            'job' => true,
            'spouse' => true,
            'contact' => true,
            'receiver' => false,
            'cost'=>true,
            'bondsman'=>true,
        );
        if (empty($rs)) {
            return view('errors.404');
        }
        $address['customer_province_code'] = $this->_role->getprovincecode($rs['customer_province']);  //省代码
        $address['customer_city_code'] = $this->_role->getcitycode($rs['customer_city']);          //市代码
        $address['company_province_code'] = $this->_role->getprovincecode($rs['company_province']);  //省代码
        $address['company_city_code'] = $this->_role->getcitycode($rs['company_city']);          //市代码
        $address['spouse_company_province_code'] = $this->_role->getprovincecode($rs['spouse_company_province']);  //省代码
        $address['spouse_company_city_code'] = $this->_role->getcitycode($rs['spouse_company_city']);          //市代码
        $address['customer_province_code'] = empty($address['customer_province_code']) ? '' : $address['customer_province_code']['code'];
        $address['customer_city_code'] = empty($address['customer_city_code']) ? '' : $address['customer_city_code']['code'];
        $address['company_province_code'] = empty($address['company_province_code']) ? '' : $address['company_province_code']['code'];
        $address['company_city_code'] = empty($address['company_city_code']) ? '' : $address['company_city_code']['code'];
        $address['spouse_company_province_code'] = empty($address['spouse_company_province_code']) ? '' : $address['spouse_company_province_code']['code'];
        $address['spouse_company_city_code'] = empty($address['spouse_company_city_code']) ? '' : $address['spouse_company_city_code']['code'];
        return view('admin.workplatform.salessupplement.detailsalessupplement')->with('title', '销售补件详情')
                        ->with('detail', $rs)->with('edit', $edit)->with('item_instance_id', Request::input('item_instance_id', ''))->with('address',$address)->with('source_edit', array(
                    '1' => 1, '2' => 1, '3' => 1, '4' => 1, '5' => 1, '6' => 1, '7' => 1, '8' => 1,
        ));
    }
}