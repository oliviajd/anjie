<?php

namespace App\Http\Controllers;

use App\Http\Models\business\Jcr;
use App\Http\Models\business\Auth;
use App\Http\Models\business\File;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Storage;
use Request;
use App\Http\Models\table\Jcr_users;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Message;

//主要用于展示静态页面
class JcrController extends Controller
{
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_jcr = new Jcr();
        $this->_auth = new Auth();
        $this->_message = new Message();
        $this->_file = new File();
        $this->_jcr_users = new Jcr_users();
    }

    //聚车融登录
    public function login()
    {
        $param['loginname'] = Request::input('loginname', '');         //用户名
        $param['password_md5'] = Request::input('password_md5', '');   //密码，做一次md5后传递
        $param['from'] = Request::input('from', '');                   // web,phone,pad等
        $param['device'] = Request::input('device', '');               //设备名称
        $rs = $this->_jcr->login($param);
        return $this->_common->output($rs['result'], $rs['error_no'], $rs['error_msg']);
    }

    //发送注册手机验证码
    public function regmobilecode()
    {
        $param['mobile'] = Request::input('mobile', '');         //手机号
        $rs = $this->_jcr->regmobilecode($param);
        return $this->_common->output($rs['result'], $rs['error_no'], $rs['error_msg']);
    }

    //注册
    public function reg()
    {
        $param['channel_code'] = Request::input('loginname', '');
        $param['mobile'] = Request::input('mobile', '');        //手机号
        $param['password'] = Request::input('password', '');    // 密码
        $param['confirm_password'] = Request::input('confirm_password', '');    //确认密码
        $param['invite_userid'] = Request::input('invite_userid', '0');  //邀请用户ID
        $param['mobile_code'] = Request::input('mobile_code', '');    // 注册验证码
        $param['mobile_auth'] = Request::input('mobile_auth', '');    // 
        $param['channel'] = Request::input('channel', '');  // 注册渠道
        $rs = $this->_jcr->reg($param);
        return $this->_common->output($rs['result'], $rs['error_no'], $rs['error_msg']);
    }

    //给已有用户发送手机验证码
    public function mobilecode()
    {
        $param['mobile'] = Request::input('mobile', '');         //手机号
        $rs = $this->_jcr->mobilecode($param);
        return $this->_common->output($rs['result'], $rs['error_no'], $rs['error_msg']);
    }

    //银行存管开户
    public function baaccountopen()
    {
        $param['token'] = Request::input('jcrtoken', '');
        $param['card_No'] = Request::input('card_No', '');        //银行卡号
        $param['sms_code'] = Request::input('sms_code', '');        //短信验证码
        $param['channel'] = Request::input('channel', '');        //000001手机APP 000002网页 000003微信
        $param['sms_auth'] = Request::input('sms_auth', '');        //短信回执码
        $rs = $this->_jcr->baAccountOpen($param);
        return $rs;
    }

    //银行存管发送验证码
    public function basmscodeapply()
    {
        $param['token'] = Request::input('jcrtoken', '');
        $param['srv_code'] = Request::input('srv_code', 'accountOpen');        //accountOpen, cardBind, mobileModify, passwordReset, directRecharge, indirectRecharge, autoBidAuth, autoCreditInvestAuth,
        $param['mobile'] = Request::input('mobile', '');        // 手机号码
        $rs = $this->_jcr->basmscodeapply($param);
        if (!is_array($rs)){
            return $rs;
        }
        return $this->_common->output($rs['result'], $rs['error_no'], $rs['error_msg']);
    }

    //获取用户信息
    public function userinfo()
    {
        $param['token'] = Request::input('jcrtoken', '');
        $rs = $this->_jcr->getuserinfo($param);
        return $rs;
    }

    //设置头像
    public function setheadportrait()
    {
        $param['token'] = Request::input('jcrtoken', '');
        $param['head_portrait'] = Request::input('head_portrait', ''); //头像
        $rs = $this->_jcr->setheadportrait($param);
        return $rs;
    }

    //认证。如果之前有这个认证信息，则update
    public function jcrverify()
    {
        $param['token'] = Request::input('jcrtoken', '');
        $param['reverify'] = Request::input('reverify', '');  //是否是重新认证 1:重新认证
        $param['name'] = Request::input('name', '');  //真实姓名
        $param['certificate_number'] = Request::input('certificate_number', '');  //身份证号
        $param['verify_type'] = Request::input('verify_type', '');  //认证类型  1：个体认证，1：商户认证
        $param['shopname'] = Request::input('shopname', '');  //店铺名称
        $param['area'] = Request::input('area', '');  //所在地区
        $param['address'] = Request::input('address', '');  //详细地址
        $param['business_license_number'] = Request::input('business_license_number', '');  //营业执照号码
        $param['company_name'] = Request::input('company_name', '');  //公司名称
        $param['sms_code'] = Request::input('sms_code', '');  //短信验证码
        $params = Request::input();
        if (!isset($params['imgs']) || !is_array($params['imgs']) || $param['area'] == '' || $param['address'] == '') {
            return $this->_common->output(null, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if ($param['verify_type'] == '2') {
            if ($param['business_license_number'] == '' || $param['company_name'] == '') {
                return $this->_common->output(null, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
            }
        } elseif ($param['verify_type'] == '1') {
            $param['business_license_number'] = '';
            $param['company_name'] = '';
        } else {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '认证类型不存在');
        }
        if ($params['reverify'] == '1' && $params['sms_code'] == '') {
            return $this->_common->output(null, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        $param['imgs'] = $params['imgs'];
        $rs = $this->_jcr->jcrverify($param);
        return $rs;
    }

    /**
     * 重新认证时发送验证码
     * @param int $account 账号
     * @return object   errorcode为0时修改成功
     */
    public function sendmessage()
    {
        $this->_common->setlog();
        $param['token'] = Request::input('jcrtoken', '');
        $param['account'] = Request::input('account', '');
        $check = $this->_jcr->checkaccount($param);
        if ($check !== true) {
            return $check;
        }
        $param['type'] = Request::input('type', '3'); //1重新认证
        $param['sms_code'] = $this->_common->generate_code(6);  //生成短信验证码
        $param['sms_code'] = '111111';
        if ($param['type'] == '3') {
            $param['content'] = "欢迎使用聚车贷重新认证平台，手机验证码是：" . $param['sms_code'] . "，有效时间15分钟，若非本人操作，请忽略。";
        } else {
            return $this->_common->output(null, Constant::ERR_FAILED_NO, '发送短信验证码的类型不正确');
        }
        $r = $this->_message->sendmessage($param);
        return $r;
    }

    //获取认证信息
    public function getverifyinfo()
    {
        $param['token'] = Request::input('jcrtoken', '');
        $rs = $this->_jcr->getverifyinfo($param);
        return $rs;
    }

    //车商融申请
    public function csrrequest()
    {
        $param['token'] = Request::input('jcrtoken', '');
        $param['money'] = round(floatval(Request::input('money', '')),2);  //融资金额
        $param['car_model'] = Request::input('carModel', '');  //车系
        $param['history_id'] = Request::input('historyId', '');  //历史id
        $param['deadline'] = Request::input('deadline', '');  //融资期限
        $param['rate'] = Request::input('rate', '');  //融资利率
        $rs = $this->_jcr->csrrequest($param);
        return $rs;
    }

    //意见反馈
    public function suggestionssubmit()
    {
        $params = Request::input();
        $params['token'] = Request::input('jcrtoken', '');
        $params['suggestions'] = Request::input('suggestions', '');  //意见
        if ($params['suggestions'] == '' || $params['token'] == '') {
            return $this->_common->output(null, Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        if (isset($params['imgs']) && !is_array($params['imgs'])) {
            return $this->_common->output(null, Constant::ERR_DATA_INCOMPLETE_NO, '图片上传的格式不正确');
        }
        $rs = $this->_jcr->suggestionssubmit($params);
        return $rs;
    }

    //融资申请记录
    public function csrrequestrecord()
    {
        $params['token'] = Request::input('jcrtoken', '');
        $params['type'] = Request::input('type', ''); //1:审核中；2:募资中；3：已放款；4：未通过
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $rs = $this->_jcr->csrrequestrecord($params);
        return $rs;
    }

    //还款计划
    public function borrowplan()
    {
        $params['token'] = Request::input('jcrtoken', '');
        $params['start_time'] = Request::input('start_time', ''); //开始时间
        $params['end_time'] = Request::input('end_time', '');   //结束时间
        $params['status'] = Request::input('status', '');  //1已还款，2未还款
        $params['page'] = intval(Request::input('page', '1'));   //第几页
        $params['size'] = intval(Request::input('size', '20'));  //每一页的数量
        $rs = $this->_jcr->borrowplan($params);
        return $rs;
    }

    //获取所有的城市
    public function getallcity()
    {
        $rs = $this->_jcr->getallcity();
        return $rs;
    }

    //获取所有的品牌列表
    public function getcarbrandlist()
    {
        $rs = $this->_jcr->getcarbrandlist();
        return $rs;
    }

    //获取车系列表
    public function getcarserieslist()
    {
        $params['brandId'] = Request::input('brandId', ''); //品牌ID
        $rs = $this->_jcr->getcarserieslist($params);
        return $rs;
    }

    //获取车型列表
    public function getcarmodellist()
    {
        $params['seriesId'] = Request::input('seriesId', ''); //车系ID
        $rs = $this->_jcr->getcarmodellist($params);
        return $rs;
    }

    //基于VIN码获取车型
    public function identifymodelbyvin()
    {
        $params['token'] = Request::input('jcrtoken', '');
        $params['vin'] = Request::input('vin', ''); //vin码
        $rs = $this->_jcr->identifymodelbyvin($params);
        return $rs;
    }

    //车辆估值接口
    public function getusedcarprice()
    {
        $params['token'] = Request::input('jcrtoken', '');
        $params['model_id'] = Request::input('modelId', ''); //车型id
        $params['zone'] = Request::input('zone', ''); //城市标识
        $params['reg_date'] = Request::input('regDate', ''); //车辆上牌日期，如2012-01
        $params['mile'] = Request::input('mile', ''); //车辆行驶里程，单位是万公里//车型id
        $params['zone_name'] = Request::input('zoneName', ''); //城市名称
        $params['model_name'] = Request::input('modelName', ''); //车型名称
        $params['series_name'] = Request::input('seriesName', ''); //车系名称
        $params['brand_name'] = Request::input('brandName', ''); //品牌名称
        $params['type'] = Request::input('type', ''); //1首页 2，服务
        $rs = $this->_jcr->getusedcarprice($params);
        return $rs;
    }

    //首页总数
    public function gettotalfinancingnumber()
    {
        $rs = $this->_jcr->gettotalfinancingnumber();

        return $rs;

    }

    //注册登录
    public function reglogin()
    {
        $param['mobile'] = Request::input('mobile', '');        //手机号
        $param['mobile_code'] = Request::input('mobile_code', '');    // 注册验证码
        $param['mobile_auth'] = Request::input('mobile_auth', '');    //
        $param['channel'] = Request::input('channel', '');  // 注册渠道
        $param['device'] = Request::input('device', '');  // 设备信息
        $param['is_borrower'] = Request::input('is_borrower', 1);  // 是否借款人

        $rs = $this->_jcr->reglogin($param);

        return $rs;
    }

    //估价历史
    public function gethistorylist()
    {
        $params['token'] = Request::input('jcrtoken', '');
        $p = Request::input('page', 1);
        $n = Request::input('size', 10);

        $rs = $this->_jcr->gethistorylist($p,$n,$params);

        return $rs;
    }

    //估价历史
    public function gethistorydetail()
    {
        $params['token'] = Request::input('jcrtoken', '');
        $params['history_id'] = Request::input('historyId', ''); //歷史ID

        $rs = $this->_jcr->gethistorydetail($params);

        return $rs;
    }

    //文件上传接口
    public function upload()
    {
        $file = Request::file('file');
        $data = array();
        $token =  Request::input('jcrtoken', '');
        //验证token是否有效
        $userinfo = $this->_jcr_users->getuserinfobytoken($token);
        if (empty($userinfo) || ($userinfo['over_time'] < time())) {
            return $this->_common->output(null, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        }
        $data['user_id'] = $userinfo['user_id'];
        $data['type'] = Request::input('type', '');
        if($file && $file->isValid()){   //判断文件是否上传成功
            $originalName = $file->getClientOriginalName(); //源文件名
            $data['suffix'] = $file->getClientOriginalExtension();    //文件拓展名
            $data['size'] = $file->getClientSize();
            // $type = $file->getClientMimeType(); //文件类型
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $typeallow = File::$_typeallow;
            if (!isset($typeallow[$data['type']]) || $typeallow[$data['type']] !== 1) {
                return $this->_common->output(false, Constant::ERR_NONSUPPORT_FILE_FORMAT_NO, Constant::ERR_NONSUPPORT_FILE_FORMAT_MSG);
            }
            // $imageallow = File::$_imagesallow;
            // if ($data['type'] == 'image' && (!isset($imageallow[$data['suffix']]) || $imageallow[$data['suffix']] !== 1)) {
            //     return $this->_common->output(false, Constant::ERR_NONSUPPORT_FILE_FORMAT_NO, Constant::ERR_NONSUPPORT_FILE_FORMAT_MSG);
            // }
            $path = 'uploads/' . $data['type'] . '/' . date('Ymd') . '/';
            $data['path'] = $path;
            $data['file_token'] = md5($originalName) . microtime() . rand(10000, 99999);
            $data['token_expired_in'] = time() + 3600 * 4 * 7;
            $data['sync_to_upyun'] = Request::input('sync_to_upyun', '0');
            if (!file_exists(FCPATH . $path)) {
                mkdir(FCPATH . $path, 0777, true);
            }
            $return = $this->_file->add($data);
            $result = json_decode($return);
            $fileName =  $result->result->fid . '.' . $result->result->suffix;
            $bool = file_put_contents(FCPATH . $path . $fileName, file_get_contents($realPath));
            return $return;
        } else {
            return $this->_common->output(false, Constant::ERR_NON_FILE_NO, Constant::ERR_NON_FILE_MSG);
        }
    }
}