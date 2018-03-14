<?php


namespace App\Http\Models\common;
use App\Http\Models\business\Workplatform;
use App\Http\Models\table\Anjie_users;
use App\Http\Models\table\Jcr_bill;
use App\Http\Models\table\Jcr_verify;
use App\Http\Models\table\V1_user_role;


/**
 * 垫资相关服务
 * Class AdvanceService
 */
class AdvanceService{

    /**
     * 根据手机号来查询最匹配的用户
     * @param $customerPhone 指定业务员的手机号
     * @param $myuid 提交资料人的用户id
     * @return String $phone
     */
    public function getAdvanceUserByPhoneAndUid($myuid,$customerPhone){
        //查询车商（我）的信息
        $myInfo = Jcr_verify::where(['user_id'=>$myuid,'verify_status'=>1])->select('area','verify_status')->first();
        if(is_null($myInfo)){
            return false;
        }
        //初始化我指定的业务员的信息
        $customerInfo = null;
        if($customerPhone != ''){
            //查询我指定业务员的信息
            $customerInfo = Anjie_users::where(['account'=>$customerPhone,'is_valid'=>1])->select(['province','city','id','account'])->first();
        }
        $province = '';
        $city = '';
        if($myInfo->area != ''){
            $area = explode(' ',$myInfo->area);
            $province = $area[0];
            if(count($area) >= 2){
                $city = $area[1];
            }
        }
        //没有找到指定业务员的角色就随机匹配
        if(is_null($customerInfo)){
            return $this->getManager($province,$city);
        }
        //如果业务员的地区不匹配
        if($customerInfo->province != $province || $customerInfo->city != $city){
            return $this->getManager($province,$city);
        }
        //如果指定的不是业务员角色重新匹配
        if(!$this->checkManager($customerInfo->id)){
            return $this->getManager($province,$city);
        }
        return $customerInfo;
    }


    /**
     * 根据用户的地区/配置地区随机匹配一个业务员
     * @param $province
     * @param $city
     * @return bool|mixed
     */
    private function getManager($province,$city){
        $roule = V1_user_role::where(['role_id'=>74])->select('user_id')->get()->toArray();
        if(empty($roule) || !is_array($roule)){
            return false;
        }
        $roule_user = [];
        foreach($roule as $user){
            $roule_user[] = $user['user_id'];
        }
        //获取到该角色的所有用户列表
        $users = Anjie_users::whereIn('id',$roule_user)->where(['province'=>$province,'city'=>$city])->select(['id','account'])->get()->toArray();
        //没找到和用户地区匹配的，就查找默认的
        if(empty($users) || !is_array($users)){
            $sql = 'SELECT * FROM jcr_apply_area_config ORDER BY id DESC LIMIT 1';
            $pdo = new Pdo();
            $rs = $pdo->fetchOne($sql);
            $users = Anjie_users::whereIn('id',$roule_user)->where(['province'=>$rs['province'],'city'=>$rs['city']])->select(['account','id'])->get()->toArray();
            if(empty($users) || !is_array($users)){
                return false;
            }
        }
        $newUser = [];
        foreach($users as $u){
            $n = new \stdClass();
            $n->id = $u['id'];
            $n->account = $u['account'];
            $newUser[] = $n;
        }
        $max = count($newUser);
        $rand = rand(0,($max-1));
        return $newUser[$rand];
    }

    private function checkManager($id){
        $roule = V1_user_role::where(['role_id'=>74])->select('user_id')->get()->toArray();
        if(empty($roule) || !is_array($roule)){
            return false;
        }
        //获取到该角色的所有用户列表
        foreach($roule as $user){
            if($user['user_id'] == $id){
                return true;
            }
        }
        return false;
    }

    /**
     * 查询车商申请的列表
     * @param $cntSql
     * @param $sql
     * @return empty|array
     */
    public function getCarList($cntSql,$sql){
        $pdo = new Pdo();
        $resutl = $pdo->fetchAll($sql);
        $arr = [];
        $return = [];
        if(!empty($resutl)){
            $workplatform = new Workplatform();
            foreach($resutl as $val){
                if(!isset($arr[$val['id']])){
                    $arr[$val['id']] = 1;
                    $val['flow'] = [];
                    if($val['work_id']){
                        $val['flow'] = $workplatform->listtasks(['work_id'=>$val['work_id'],'process_id'=>'1']);
                    }
                    $return[] = $val;
                }
            }
        }
        $cnt = $pdo->fetchOne($cntSql);
        $new['total'] = isset($cnt['cnt'])?$cnt['cnt']:"0";
        $new['rows'] = $return;
        return $new;
    }


    /**
     * 根据用户id获取带过户列表
     * @param $userId
     * @return array
     */
    public function getTransferListByUid($userId){
        $jcrBill = new Jcr_bill();
        return $jcrBill->getUserBillByUserId($userId);
    }

}