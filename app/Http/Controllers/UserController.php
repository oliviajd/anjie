<?php

/**
 * 用户附属表相关接口
 * @author cxj
 */

namespace App\Http\Controllers;

use App\Http\Models\table\Anjie_work_affiliated;
use Request;
use App\Http\Models\common\Constant;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }


    /**
     * 用户是否已经设置交易密码
     * @return array|string
     */
    public function gettransaction()
    {
        return $this->transaction('get',Request::input('id'));
    }


    /**
     * 用户设置交易密码
     * @return boolean
     */

    public function settransaction(){
        return $this->transaction('set',Request::input('id'));
    }


    private function transaction($type,$id){
        $_user = Anjie_work_affiliated::where(['identity'=>$id])->first();
        if($type == 'get'){
            if(!is_null($_user)){
                if($_user->transaction_pwd){
                    return  $this->_common->output(true,Constant::ERR_SUCCESS_NO,'已设置交易密码');
                }
            }
            return $this->_common->output(false,Constant::ERR_SUCCESS_NO,'未设置交易密码');
        }else{
            if(!is_null($_user) && $_user->transaction_pwd){
                $data['password'] = $_user->transaction_pwd;
                return $this->_common->output($data,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
            }else{
                $pwd = rand(100000,999999);
                $anjieUser = new Anjie_work_affiliated();
                $anjieUser->identity = $id;
                $anjieUser->transaction_pwd = $pwd;
                if($anjieUser->save()){
                    $data['password'] = $pwd;
                    return $this->_common->output($data,Constant::ERR_SUCCESS_NO,Constant::ERR_SUCCESS_MSG);
                }else{
                    return $this->_common->output(false,Constant::ERR_FAILED_NO,'设置失败');
                }
            }
        }
    }


}
