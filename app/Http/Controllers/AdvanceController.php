<?php

/**
 * 用户申请垫资接口控制器
 * @author cxj
 */

namespace App\Http\Controllers;

use App\Http\Models\business\Workplatform;
use App\Http\Models\common\AdvanceService;
use App\Http\Models\table\Jcr_apply;
use App\Http\Models\table\Jcr_users;
use App\Http\Models\table\Jcr_verify;
use Request;
use App\Http\Models\table\Jcr_bill;
use App\Http\Models\common\Constant;

class AdvanceController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 业务员提交征信接口
     * @return array|string
     */
    public function creditsubmit()
    {
        //customer_telephone 客户电话
        //customer_name 客户姓名
        //customer_certificate_number 身份证号
        //merchant_name  客户来源
        //product_name 产品类型
        //product_id 产品类型对应的id
        //imgs[1]['source_lists'][0]['org']
        //imgs[1]['source_lists'][0]['alt']
        $params =  Request::input();
        $applyId = isset($params['applyid'])?$params['applyid']:0;//如果是用户提交给业务员的(已经生成数据了)就有此id，否则就没有
        $params['merchant_name'] =  Request::input('merchant_name', '经销商');
        $params['merchant_id'] =  Request::input('merchant_id', '1');
        $params['spouse_name'] =  Request::input('spouse_name', '');
        $params['spouse_certificate_number'] =  Request::input('spouse_certificate_number', '');
        $params['bondsman_certificate_numberbondsman_spouse_name'] =  Request::input('bondsman_certificate_numberbondsman_spouse_name', '');
        $params['bondsman_name'] =  Request::input('bondsman_name', '');
        $params['bondsman_certificate_number'] =  Request::input('bondsman_certificate_number', '');
        $params['bondsman_spouse_name'] =  Request::input('bondsman_spouse_name', '');
        $params['bondsman_spouse_idcard'] =  Request::input('bondsman_spouse_idcard', '');
        if (!isset($params['customer_name']) || $params['customer_name'] == NULL || !isset($params['customer_certificate_number']) || $params['customer_certificate_number'] == NULL|| !isset($params['customer_telephone']) || $params['customer_telephone'] == NULL || !isset($params['product_id']) || $params['product_id'] == NULL || !isset($params['product_name']) || $params['product_name'] == NULL || !isset($params['imgs']) || $params['imgs'] == NULL) {
            return $this->_common->output(false, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //都是图片
        if(!empty($params['imgs']) && is_array($params['imgs'])){
            foreach($params['imgs'] as $k => $val){
                $params['imgs'][$k]['source_type'] = 'image';
            }
        }
        $workPlatform = new Workplatform();
        try{
            $rs = $workPlatform->creditrequestsubmit($params['_user']['user_id'], $params);
            //如果存在这个id就修改work字段否则就新增一条数据
            $rs = json_decode($rs,true);
            if($rs['error_no'] == 200){
                if(is_numeric($applyId) && $applyId > 0){
                    $ja = Jcr_apply::where(['id'=>$applyId])->first();
                    $ja->work_id = $rs['result']['id'];
                    $ja->customer_status = 1;
                    $ja->merchant_name = $params['merchant_name'];
                    $ja->product_name = $params['product_name'];
                    $ja->add_time = time();
                    if(!$ja->save()){
                        return $this->_common->output(false,Constant::ERR_FAILED_NO,'修改数据失败');
                    }
                }else{
                    //添加数据
                    $user = Request::input('_user');
                    $jcrApply = new Jcr_apply();
                    $jcrApply->customer_name = $params['customer_name'];
                    $jcrApply->customer_telephone = $params['customer_telephone'];
                    $jcrApply->customer_user_phone = $user['account'];
                    $jcrApply->customer_order_num = $this->getNo();
                    $jcrApply->add_time = time();
                    $jcrApply->customer_status = 1;
                    $jcrApply->work_id = $rs['id'];
                    $jcrApply->user_id = $user['user_id'];
                    $jcrApply->to_user_id = $user['user_id'];
                    $jcrApply->user_type = 2;
                    $jcrApply->merchant_name = $params['merchant_name'];
                    $jcrApply->product_name = $params['product_name'];
                    $rs = $jcrApply->save();
                    if(!$rs){
                        return $this->_common->output(false,Constant::ERR_FAILED_NO,'保存数据失败');
                    }
                }
            }else{
                return $this->_common->output(false,Constant::ERR_FAILED_NO,$rs['error_msg']);
            }
        } catch(\Exception $e) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, constant::ERR_FAILED_DATA_MSG);
        }
//        $return['id'] = $rs['id'];
//        $return['request_no'] = $rs['request_no'];
//        $return['customer_name'] = $rs['customer_name'];
//        $return['customer_telephone'] = $rs['customer_telephone'];
//        $return['customer_sex'] = $rs['customer_sex'];
//        $return['customer_certificate_number'] =$rs['customer_certificate_number'];
//        $return['merchant_name'] = $rs['merchant_name'];
//        $return['product_name'] = $rs['product_name'];
        unset($rs);
        return $this->_common->output(true,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }

    /**
     * 获取用户垫资车辆列表
     * @return string
     */
    public function getborrowplan()
    {
        $advanceService = new AdvanceService();
        $user = Request::input('_user');
        $list = $advanceService->getTransferListByUid($user['user_id']);
        if(empty($list)){
          return  $this->_common->output([],Constant::ERR_SUCCESS_NO,'没有数据！');
        }
        return $this->_common->output($list,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }


    /**
     * 车商提交垫资申请
     */
    public function applysubmit(){
        $data['customer_name'] = Request::input('customer_name','');//客户姓名
        $data['customer_telephone'] = Request::input('customer_telephone','');//客户电话
        $data['customer_user_phone'] = Request::input('customer_user_phone','');//按揭联系人
        $data['customer_car_id'] = Request::input('customer_car_id','');//选择垫资车辆的id
        $isPhone = $this->_common->checktelephone($data['customer_telephone']);
        if($data['customer_user_phone'] != ''){
            $userPhone = $this->_common->checktelephone($data['customer_user_phone']);
            if (!$userPhone) {
                return $this->_common->output(false,Constant::ERR_FAILED_NO,'按揭联系人手机号格式错误');
            }
        }
        if (!$isPhone) {
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'客户电话格式错误');
        }
        $carinfo = [];
        if($data['customer_car_id'] != ''){
            if(!is_numeric($data['customer_car_id']) || $data['customer_car_id'] <= 0){
                return $this->_common->output(false,Constant::ERR_FAILED_NO,'垫资车辆id非法');
            }
            $carinfo = Jcr_bill::where(['id'=>$data['customer_car_id']])->select(['carbrand','money'])->first();
            if(is_null($carinfo)){
                return $this->_common->output(false,Constant::ERR_FAILED_NO,'车商信息不存在');
            }
            $carinfo = $carinfo->toArray();
        }
        if($data['customer_name'] == ''){
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'客户姓名不能为空');
        }
        $advaceService = new AdvanceService();
        $user = Request::input('_user');
        $phone = $advaceService->getAdvanceUserByPhoneAndUid($user['user_id'],$data['customer_user_phone']);
        if(!$phone){
            $phone = new \stdClass();
            $phone->account = '';
            $phone->id = 0;
        }
        $time = time();
        $jcrApply = new Jcr_apply();
        $jcrApply->customer_name = $data['customer_name'];
        $jcrApply->customer_telephone = $data['customer_telephone'];
        $jcrApply->customer_user_phone = $phone->account;
        $jcrApply->customer_car_id = $data['customer_car_id']==''?0:$data['customer_car_id'];
        $jcrApply->product_brand = isset($carinfo['carbrand'])?$carinfo['carbrand']:'';
        $jcrApply->product_price = isset($carinfo['money'])?$carinfo['money']:0;
        $jcrApply->product = empty($carinfo)?0:1;
        $jcrApply->customer_order_num = $this->getNo();
        $jcrApply->add_time = $time;
        $jcrApply->customer_status = 0;
        $jcrApply->work_id = 0;
        $jcrApply->user_id = $user['user_id'];
        $jcrApply->to_user_id = $phone->id;
        $jcrApply->user_type = 1;
        $rs = $jcrApply->save();
        if($rs){
            return $this->_common->output(true,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
        }else{
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'保存失败');
        }
    }

    /**
     * 业务员获取车商垫资申请列表
     */
    public function lists(){
        $status = Request::input('status',0);
        $page = Request::input('page',1);
        $pageSize = Request::input('pagesize',10);
        $offset = ($page-1)*$pageSize;
        if($status == 0){
            return $this->getWateList($offset,$pageSize);
        }else{
            return $this->getStartList($offset,$pageSize);
        }
    }


    /**
     * 业务员获取待处理的列表
     * @param $offset
     * @param $pageSize
     * @return array
     */

    private function getWateList($offset,$pageSize){
        $user = Request::input('_user');
        $list = Jcr_apply::where(['to_user_id'=>$user['user_id'],'customer_status'=>0,'user_type'=>1])->orderBy('id','desc')->select(['id','customer_order_num','add_time','customer_name','customer_telephone','customer_status','work_id'])->offset($offset)->limit($pageSize)->get();

        if(!is_null($list)){
            $list = $list->toArray();
        }else{
            $list = [];
        }
        $cnt = Jcr_apply::where(['to_user_id'=>$user['user_id'],'customer_status'=>0,'user_type'=>1])->count();
        $arr['total'] = $cnt;
        $arr['rows'] = $list;
        return $this->_common->output($arr,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }


    /**
     * 业务员获取已经处理的列表
     * @param $offset
     * @param $pageSize
     * @return array
     */
    public function getStartList($offset,$pageSize){
        $user = Request::input('_user');
        $list = Jcr_apply::where(['to_user_id'=>$user['user_id'],'customer_status'=>1])->orderBy('id','desc')->select(['id','customer_order_num','add_time','customer_name','customer_telephone','customer_status','work_id'])->offset($offset)->limit($pageSize)->get();
        if(!is_null($list)){
            $list = $list->toArray();
            $workplatform = new Workplatform();
            foreach($list as &$val){
                $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
                $val['customer_telephone'] = substr($val['customer_telephone'],0,3).'-'.substr($val['customer_telephone'],3,11);
                $val['flow'] = $workplatform->listtasks(['work_id'=>$val['work_id'],'process_id'=>'1']);
            }
        }else{
            $list = [];
        }
        $cnt = Jcr_apply::where(['to_user_id'=>$user['user_id'],'customer_status'=>1])->count();
        $arr['total'] = $cnt;
        $arr['rows'] = $list;
        return $this->_common->output($arr,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }


    /**
     * 业务员获取申请详情
     * @return string
     */
    public function getinfo(){
        $id = Request::input('id','');
        if(!is_numeric($id) || $id <= 0){
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'id非法');
        }
        $info = Jcr_apply::where(['id'=>$id])->first();
        if(is_null($info)){
            return $this->_common->output([],Constant::ERR_SUCCESS_NO,'没有数据');
        }
        $info = $info->toArray();
//        $user = Jcr_verify::where(['loginname'=>$info['customer_telephone']])->first();
        $_user = Jcr_users::where(['user_id'=>$info['user_id']])->first();
//        if(is_null($user)){
//            return $this->_common->output([],Constant::ERR_SUCCESS_NO,'不存在用户');
//        }
//        $info['customer_telephone'] = substr($info['customer_telephone'],0,3).'-'.substr($info['customer_telephone'],3,7).'-'.substr($info['customer_telephone'],7,11);
        $workplatform = new Workplatform();
        if(!is_null($_user)){
            $info['merchant_name'] = $_user->name;
        }
//        $info['identy_number'] = $user->certificate_number;
        $info['flow'] = $workplatform->listtasks(['work_id'=>$info['work_id'],'process_id'=>'1']);
        return $this->_common->output($info,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }

    /**
     * 获取用户信息
     * @return string
     */
    public function getuserinfo(){
        $user_id = Request::input('userid');
        if(!is_numeric($user_id) || $user_id <= 0){
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'用户id非法');
        }
        $userinfo = Jcr_users::where(['user_id'=>$user_id])->first();
        if(is_null($userinfo)){
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'不存在用户');
        }
        $userinfo = $userinfo->toArray();
        return $this->_common->output($userinfo,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }

    private function getNo(){
        return "DZ".rand(100000000,999999999);//虚拟单号
    }

    /**
     * 车商获取审批进度
     */
    public function applylists(){
        $user = Request::input('_user');
        $page = Request::input('page',1);
        $pageSize = Request::input('pagesize',10);
        $offset = ($page-1)*$pageSize;
        $status = Request::input('status',0);//0=未通过，,1=审核中,2=已放款
        //WHERE user_id = ?
        switch ($status){
            case 1:
                $sql = "SELECT t.* FROM (SELECT
                    ja.* ,MAX(atk.task_status) stat
                FROM
                    jcr_apply AS ja
                LEFT JOIN anjie_task AS atk ON ja.work_id = atk.work_id
                WHERE
                    ja.user_id = {$user['user_id']}
                AND (
                    atk.task_status = NULL 
                    AND atk.item_id = 39
                    OR ja.customer_status IN (0, 1)
                )
                GROUP BY
                    ja.id
                ORDER BY
                    ja.add_time DESC
                ) AS t WHERE t.stat = 1 OR t.stat IS NULL
                LIMIT {$offset},{$pageSize}";//对应财务打款未成功（审核中）
                $cntSql = "SELECT COUNT(*) AS cnt FROM (SELECT
                    ja.id ,MAX(atk.task_status) stat
                FROM
                    jcr_apply AS ja
                LEFT JOIN anjie_task AS atk ON ja.work_id = atk.work_id
                WHERE
                    ja.user_id = {$user['user_id']}
                AND (
                    atk.task_status = NULL 
                    AND atk.item_id = 39
                    OR ja.customer_status IN (0, 1)
                )
                GROUP BY
                    ja.id
                ) AS t WHERE t.stat = 1 OR t.stat IS NULL";
                break;
            case 2:
                $sql = "SELECT
                            ja.*
                        FROM
                            jcr_apply AS ja
                        LEFT JOIN anjie_task AS atk ON ja.work_id = atk.work_id
                        WHERE
                            ja.user_id = {$user['user_id']}
                        AND (
                            atk.task_status = 1
                            AND atk.item_id = 39
                        )
                        GROUP BY
                            ja.id
                        ORDER BY
                            ja.add_time DESC
                        LIMIT {$offset},{$pageSize}";//39对应财务打款已成功(已放款)
                $cntSql = "SELECT
                            COUNT(*) AS cnt
                        FROM
                            jcr_apply AS ja
                        LEFT JOIN anjie_task AS atk ON ja.work_id = atk.work_id
                        WHERE
                            ja.user_id = {$user['user_id']}
                        AND (
                            atk.task_status = 1
                            AND atk.item_id = 39
                        )
                        GROUP BY
                            ja.id";
                break;
            default:
                $sql = "SELECT
                            ja.*
                        FROM
                            jcr_apply AS ja
                        LEFT JOIN anjie_task AS atk ON ja.work_id = atk.work_id
                        WHERE
                            ja.user_id = {$user['user_id']}
                        AND (atk.task_status = 2)
                        GROUP BY
                            ja.id
                        ORDER BY
                            ja.add_time DESC
                        LIMIT {$offset},{$pageSize}";
                $cntSql = "SELECT COUNT(*) AS cnt
                        FROM
                            jcr_apply AS ja
                        LEFT JOIN anjie_task AS atk ON ja.work_id = atk.work_id
                        WHERE
                            ja.user_id = {$user['user_id']}
                        AND (atk.task_status = 2)
                        GROUP BY
                            ja.id";
                break;
        }
        $advanceService = new AdvanceService();
        $list = $advanceService->getCarList($cntSql,$sql);
        return $this->_common->output($list,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }


    /**
     * 车商认证之后重新提交资料
     */
    public function submitagain(){
        $id = Request::input('id','');
        if(!is_numeric($id) || $id <= 0){
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'id非法');
        }
        $info = Jcr_apply::where(['id'=>$id])->first();
        if(is_null($info)){
            return $this->_common->output([],Constant::ERR_SUCCESS_NO,'没有数据');
        }
        $info = $info->toArray();
        $jcrVerfy = new Jcr_verify();
        $user = $jcrVerfy->getginfobyuserid($info['user_id']);
        if(empty($user)){
            die(json_encode(['result'=>null,'error_no'=>'400','error_msg'=>'用户未认证！']));
        }
        $advaceService = new AdvanceService();
        $user = Request::input('_user');
        $phone = $advaceService->getAdvanceUserByPhoneAndUid($user['user_id'],$info->customer_user_phone);
        if(!$phone){
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'您还没有认证');
        }
        $info->customer_user_phone = $phone->account;
        $info->to_user_id = $phone->id;
        if($info->save()){
            return $this->_common->output(true,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
        }else{
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'操作失败');
        }
    }

    /**
     * 根据work_id获取信息
     * @return string
     */
    public function getworkflow(){
        $work_id = Request::input('workid');
        if(!is_numeric($work_id) || $work_id <= 0){
            return $this->_common->output(false,Constant::ERR_FAILED_NO,'workid非法');
        }
        $workplatform = new Workplatform();
        $rs = $workplatform->listtasks(['work_id'=>$work_id,'process_id'=>'1']);
        return $this->_common->output($rs,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
    }



}
