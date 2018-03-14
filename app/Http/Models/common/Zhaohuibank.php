<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\business\File;
use App\Http\Models\table\Zhaohui_log;
use App\Http\Models\table\T_address_city;
use Mail;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 杭州朝晖支行的接口
 *
 * @author win7
 */
class Zhaohuibank
{

    /**
     * 查询征信的调用接口
     * @param 
     * </p>
     * @return 
     * 
     */
    public function applyCredit($param='')
    {
      $common = new Common();
      $currentTime = $common->getMillisecond();
      $salt = $this->random_str(8);
      $cmpdate = date('Ymd');
      $cmptime = date('His');
      $cmpseq = $cmpdate . $cmptime . $this->random_str(6);
      $this->_t_address_city = new T_address_city();
      $zoneno = $this->_t_address_city->getcodeByname($param['credit_city']);
      $array = array(
          'pub'=>array(
            'assurerno'=>'22', //担保单位编号
            'cmpdate'=>$cmpdate,
            'cmpseq'=>$cmpseq,
            'cmptime'=>$cmptime,
            'orderno'=>$param['id'], //合作机构订单号,就写work_id
            'phybrno'=>'12020221',  //业务受理网点
            'platno'=>'zjhb',   //平台编号
            'zoneno'=>$zoneno['bankcode'],
          ),
          'req'=>array(
            'customer'=>array(
              'custname'=>$param['customer_name'],   //姓名
              'idno'=>$param['customer_certificate_number'],  //身份证号码
              'idtype'=>'000',   //000代表身份证
              'relation'=>'本人', //关系为本人
            ),
            'pics'=>array(
                array(
                  'picid'=>'0005',  //征信查询的类型固定为0005
                  'picurl'=>env('ZHAOHUIBANK_FILEPATH') . '?imageName=' . $param['word_filename'],
                ),
            )
          ),
      );
      $secretKey = "whosyourdaddy!";
      $inputJson = stripslashes(json_encode($array, JSON_UNESCAPED_UNICODE));
      $query =  $currentTime .'#' . $inputJson . '#' . $salt;
      $orgSign = md5(md5($secretKey . $query) . $salt);
      $url = env('ZHAOHUIBANK_API_URL') . "/icbc/api/applyCredit.action";
      $data = '&inputJson=' . $inputJson . '&currentTime=' . $currentTime . '&salt=' . $salt . '&orgSign=' . $orgSign;
      $common = new Common();
      $rs = $common->object_array(json_decode($common->curl_post($url, $data)));
      $log =array();
      $log['url'] = $url;
      $log['param'] = $data;
      $log['response'] = json_encode($rs);
      $log['orderno'] = $param['id'];
      $log['idno'] = $param['customer_certificate_number'];
      $this->_zhaohui_log = new Zhaohui_log();
      $setlog = $this->_zhaohui_log->addlog($log);
      if (!isset($rs['result']) || ($rs['result'] == false)) {
        $flag = Mail::send('common.zhaohuiemail',['arr'=>$params],function($message){
            $to = 'jiangd@ifcar99.com';
            $message->to($to)->subject('朝晖支行数据返回');
        });
      }
      return $rs;
    }
    //工行文件传输
    public function sftppush($param)
    {
      $localpath = public_path($param['word_path']);
      $serverpath = env('ZHAOHUIBANK_SFTP_SERVER_PATH') . $param['filename'];
      $resConnection = ssh2_connect(env('ZHAOHUIBANK_SFTP_SERVER'), env('ZHAOHUIBANK_SFTP_SERVER_PORT'));
      if(ssh2_auth_password($resConnection, env('ZHAOHUIBANK_SFTP_SERVER_USERNAME'), env('ZHAOHUIBANK_SFTP_SERVER_PASSWORD'))){
        $resSFTP = ssh2_sftp($resConnection);
        if (!is_file("ssh2.sftp://{$resSFTP}/" . env('ZHAOHUIBANK_SFTP_SERVER_PATH') . $param['filename'])) {
            $copy = copy($localpath,"ssh2.sftp://{$resSFTP}".$serverpath);
            return $copy;
        }
      }
    }
    public function sftpfile($param)
    {
      foreach ($param as $key => $value) {
        $localpath = public_path($value['file_path']);
        if ($value['file_type_name'] == 'image') {
          $serverpath = env('ZHAOHUIBANK_SFTP_SERVER_PATH') .'/'. $value['file_id'] . '.jpg';
        } else {
          $serverpath = env('ZHAOHUIBANK_SFTP_SERVER_PATH') .'/'. $value['real_file_name'];
        }
        $resConnection = ssh2_connect(env('ZHAOHUIBANK_SFTP_SERVER'), env('ZHAOHUIBANK_SFTP_SERVER_PORT'));
        if(ssh2_auth_password($resConnection, env('ZHAOHUIBANK_SFTP_SERVER_USERNAME'), env('ZHAOHUIBANK_SFTP_SERVER_PASSWORD'))){
          $resSFTP = ssh2_sftp($resConnection);
          if (!is_file("ssh2.sftp://{$resSFTP}/" . env('ZHAOHUIBANK_SFTP_SERVER_PATH') . $param['filename'])) {
              $copy = copy($localpath,"ssh2.sftp://{$resSFTP}".$serverpath);
              if ($copy !== true) {
               return false;
             }
          }
        }
      }
      return true;
    }
    //放图片的位置
    public function putword($param)
    {
      $localpath = public_path($param['word_path']);
      $serverpath = env('ZHAOHUIBANK_SFTP_SERVER_PATH') .'/'. $param['filename'];
      if (file_exists($localpath)) {
        return copy($localpath,$serverpath);
      }
    }
    //放图片的位置
    public function putfile($param)
    {
      $this->_file = new File();
      foreach ($param as $key => $value) {
        if(strpos($value['file_path'],'http') !== false || strpos($value['file_path'],'https') !== false){    
          $parseurl = parse_url($value['file_path']);
          $value['real_file_path'] = $parseurl['path'];
          $fileinfo = explode('/', $value['real_file_path']);
          array_pop($fileinfo);
          if ($fileinfo[0] !== '') {
            array_shift($fileinfo);
          } else {
            array_shift($fileinfo);
            array_shift($fileinfo);
          }
          $path = implode($fileinfo, '/');
          if(!file_exists(public_path($path))){
              if(!mkdir(public_path($path), 0777, true)){
              }
          }
          $value['real_file_path'] = $path . '/' . $value['real_file_name'];
          $this->_file->downfile(public_path($value['real_file_path']), $value['file_path']);
          $localpath = $value['real_file_path'];
        } else {
          $localpath = public_path($value['file_path']);
        }
        if ($value['file_type_name'] == 'image') {
          $serverpath = env('ZHAOHUIBANK_SFTP_SERVER_PATH') .'/'. $value['file_id'] . '.jpg';
        } else {
          $serverpath = env('ZHAOHUIBANK_SFTP_SERVER_PATH') .'/'. $value['real_file_name'];
        }
        if (file_exists($localpath)) {
           $copy = copy($localpath,$serverpath);
           if ($copy !== true) {
             return false;
           }
        }
      }
      return true;
    }
    //新车
    public function applyDiviGeneralForFirst($params)
    {
      $common = new Common();
      $currentTime = $common->getMillisecond();
      $salt = $this->random_str(8);
      $cmpdate = date('Ymd');
      $cmptime = date('His');
      $cmpseq = $cmpdate . $cmptime . $this->random_str(6);
      $this->_t_address_city = new T_address_city();
      $zoneno = $this->_t_address_city->getcodeByname($params['credit_city']);
      $array = array(
          'pub'=>array(
            'assurerno'=>'22', //担保单位编号
            'busitype'=>'1',  //1:一手车业务,907:二手车业务
            'cmpdate'=>$cmpdate,
            'cmpseq'=>$cmpseq,
            'cmptime'=>$cmptime,
            'orderno'=>$params['id'], //合作机构订单号,就写work_id
            'phybrno'=>'12020221',  //业务受理网点
            'platno'=>'zjhb',   //平台编号
            'zoneno'=>$zoneno['bankcode'],
          ),
          'req'=>array(
            'busi'=>array(
              'car' => array(
                'carNo1'=>$params['car_vehicle_identification_number'],   //车架号
                'carNo2'=>$params['license_number'],  //车牌号
                'carRegNo'=>$params['AmortNum'],   //登记证书编号
                'carType'=>$params['car_type'], //车辆型号
                'price'=>$params['car_price'],    //车辆价格
                'shorp4s'=>$params['carshop_name'],  //4S店名称
              ),
              'divi'=>array(
                'amount'=>$params['loan_prize'],     //分期金额
                'card'=>'',     //信用卡
                'feeMode'=>'2',     //手续费收取方式1:首期收取;  2:分期收取
                'interest'=>$params['loan_rate'],     //分期手续费率如手续费是8.5%，就传8.5过来，不传0.085
                'isAssure'=>'1',     //是否担保1:是; 0:否
                'isPawn'=>'1',     //是否抵押1:是; 0:否
                'paidAmt'=>$params['first_pay'],     //首付金额
                'pawnGood'=>$params['car_vehicle_identification_number'].$params['car_type'],     //抵押物=车架号+车辆型号
                'term'=>$params['loan_date'],     //分期期数
                'tiexiFlag'=>'0',     //是否贴息1:是; 0:否
                'tiexiRate'=>'',     //贴息费率如是8.5%，就传8.5过来，不传0.085
              ),
            ),
            'customer'=>array(
              'address'=>$params['customer_address'],   //家庭住址
              'custName'=>$params['customer_name'],   //客户名称
              'idNo'=>$params['customer_certificate_number'], //证件编号
              'idType'=>'000', //证件类型
              'mobile'=>$params['customer_telephone'], //联系电话
              'unit'=>$params['customer_company_name'], //单位名称
            ),
            'note'=>$params['inputrequest_description'],//申请备注,合作机构对客户情况的描述等
            'pics'=>$params['pictures'],
            'resubmit'=>$params['resubmit'],  //继续申请标志0：申请；1：继续申请; 2:资料补充
          ),
      );
      $secretKey = "whosyourdaddy!";
      $inputJson = stripslashes(json_encode($array, JSON_UNESCAPED_UNICODE));
      $query =  $currentTime .'#' . $inputJson . '#' . $salt;
      $orgSign = md5(md5($secretKey . $query) . $salt);
      $url = env('ZHAOHUIBANK_API_URL') . "/icbc/api/applyDiviGeneralForFirst.action";
      $data = '&inputJson=' . $inputJson . '&currentTime=' . $currentTime . '&salt=' . $salt . '&orgSign=' . $orgSign;
      $common = new Common();
      $rs = $common->object_array(json_decode($common->curl_post($url, $data)));
      $log =array();
      $log['url'] = $url;
      $log['param'] = $data;
      $log['response'] = json_encode($rs);
      $log['orderno'] = $params['id'];
      $log['idno'] = $params['customer_certificate_number'];
      $this->_zhaohui_log = new Zhaohui_log();
      $setlog = $this->_zhaohui_log->addlog($log);
      if (!isset($rs['result']) || ($rs['result'] == false)) {
        $flag = Mail::send('common.zhaohuiemail',['arr'=>$params],function($message){
            $to = 'jiangd@ifcar99.com';
            $message->to($to)->subject('朝晖支行数据返回');
        });
      }
      return $rs;
    }
    //二手车
    public function applyDiviGeneralForSecond($params)
    {
      $common = new Common();
      $currentTime = $common->getMillisecond();
      $salt = $this->random_str(8);
      $cmpdate = date('Ymd');
      $cmptime = date('His');
      $cmpseq = $cmpdate . $cmptime . $this->random_str(6);
      $this->_t_address_city = new T_address_city();
      $zoneno = $this->_t_address_city->getcodeByname($param['credit_city']);
      $array = array(
          'pub'=>array(
            'assurerno'=>'22', //担保单位编号
            'busitype'=>'907',  //1:一手车业务,907:二手车业务
            'cmpdate'=>$cmpdate,
            'cmpseq'=>$cmpseq,
            'cmptime'=>$cmptime,
            'orderno'=>$param['id'], //合作机构订单号,就写work_id
            'phybrno'=>'12020221',  //业务受理网点
            'platno'=>'zjhb',   //平台编号
            'zoneno'=>$zoneno['bankcode'],
          ),
          'req'=>array(
            'busi'=>array(
              'car' => array(
                'assessOrg'=>$params['car_evaluation_authority'],//评估机构
                'assessPrice'=>$params['car_evaluation_price'],//车辆评估价格（元）
                'carNo1'=>$params['car_vehicle_identification_number'],   //车架号
                'carNo2'=>$params['license_number'],  //车牌号
                'carRegNo'=>$params['AmortNum'],   //登记证书编号
                'carType'=>$params['car_type'], //车辆型号
                'price'=>$params['car_price'],    //车辆价格
                'shorp4s'=>$params['carshop_name'],  //4S店名称
                'usedYears'=>strval(ceil(floatval($params['car_use_years']) * 12)),//使用年限（月)
              ),
              'divi'=>array(
                'amount'=>$params['loan_prize'],     //分期金额
                'card'=>'',     //信用卡
                'feeMode'=>'2',     //手续费收取方式1:首期收取;  2:分期收取
                'interest'=>$params['loan_rate'],     //分期手续费率如手续费是8.5%，就传8.5过来，不传0.085
                'isAssure'=>'1',     //是否担保1:是; 0:否
                'isPawn'=>'1',     //是否抵押1:是; 0:否
                'paidAmt'=>$params['first_pay'],     //首付金额
                'pawnGood'=>$params['car_vehicle_identification_number'].$params['car_type'],     //抵押物=车架号+车辆型号
                'term'=>$params['loan_date'],     //分期期数
              ),
            ),
            'customer'=>array(
              'address'=>$params['customer_address'],   //家庭住址
              'custName'=>$params['customer_name'],   //客户名称
              'idNo'=>$params['customer_certificate_number'], //证件编号
              'idType'=>'000', //证件类型
              'mobile'=>$params['customer_telephone'], //联系电话
              'unit'=>$params['customer_company_name'], //单位名称
            ),
            'note'=>$params['inputrequest_description'],//申请备注,合作机构对客户情况的描述等
            'pics'=>$params['pictures'],
            'resubmit'=>$params['resubmit'],  //继续申请标志0：申请；1：继续申请; 2:资料补充
          ),
      );
      $secretKey = "whosyourdaddy!";
      $inputJson = stripslashes(json_encode($array, JSON_UNESCAPED_UNICODE));
      $query =  $currentTime .'#' . $inputJson . '#' . $salt;
      $orgSign = md5(md5($secretKey . $query) . $salt);
      $url = env('ZHAOHUIBANK_API_URL') . "/icbc/api/applyDiviGeneralForSecond.action";
      $data = '&inputJson=' . $inputJson . '&currentTime=' . $currentTime . '&salt=' . $salt . '&orgSign=' . $orgSign;
      $common = new Common();
      $rs = $common->object_array(json_decode($common->curl_post($url, $data)));
      $log =array();
      $log['url'] = $url;
      $log['param'] = $data;
      $log['response'] = json_encode($rs);
      $log['orderno'] = $param['id'];
      $log['idno'] = $param['customer_certificate_number'];
      $this->_zhaohui_log = new Zhaohui_log();
      $setlog = $this->_zhaohui_log->addlog($log);
      if (!isset($rs['result']) || ($rs['result'] == false)) {
        $flag = Mail::send('common.zhaohuiemail',['arr'=>$params],function($message){
            $to = 'jiangd@ifcar99.com';
            $message->to($to)->subject('朝晖支行数据返回');
        });
      }
      return $rs;
    }
    public function creditCardApply($params)
    {
      switch ($params['customer_marital_status']) {
        case '0' :
          $params['mrtlstat'] = '0';    //未知
          break;
        case '1':
          $params['mrtlstat'] = '2';    //已婚
          break;
        case '2':
          $params['mrtlstat'] = '1';     //未婚
          break;
        case '3':
          $params['mrtlstat'] = '4';     //离婚
          break;
        case '4':
          $params['mrtlstat'] = '5';     //丧偶
          break;
        case '5':
          $params['mrtlstat'] = '3';     //分居
        case '6':
          $params['mrtlstat'] = '6';     //其他
        default:
          $params['mrtlstat'] = '6';
          break;
      }
      if ($params['mrtlstat'] == '2') {
        $params['reltname1'] = $params['spouse_name']; 
        $params['reltsex1'] = $params['spouse_sex'];
        $params['reltmobl1'] = $params['spouse_telephone'];
        $params['relaphone1'] = $params['spouse_telephone'];
        $params['reltship1'] = '5';
      } else {
        $params['reltname1'] = $params['contacts_man_name'];
        $params['reltsex1'] = $params['contact_sex'];
        $params['reltmobl1'] = $params['contacts_man_telephone'];
        $params['relaphone1'] = $params['contacts_man_telephone'];
        switch ($params['contacts_man_relationship']) {
          case '1':
            $params['reltship1'] = '1';
            break;
          case '2':
            $params['reltship1'] = '8';
            break;
          case '3':
            $params['reltship1'] = '4';
            break;
          case '4':
            $params['reltship1'] = '9';
            break;
          default:
            $params['reltship1'] = '9';
            break;
        }
      }
      $common = new Common();
      $currentTime = $common->getMillisecond();
      $salt = $this->random_str(8);
      $cmpdate = date('Ymd');
      $cmptime = date('His');
      $cmpseq = $cmpdate . $cmptime . $this->random_str(6);
      $this->_t_address_city = new T_address_city();
      $zoneno = $this->_t_address_city->getcodeByname($params['credit_city']);
      $array = array(
          'pub'=>array(
            'assurerno'=>'22', //担保单位编号
            'cmpdate'=>$cmpdate,
            'cmpseq'=>$cmpseq,
            'cmptime'=>$cmptime,
            'orderno'=>$params['id'], //合作机构订单号,就写work_id
            'phybrno'=>'12020221',  //业务受理网点
            'platno'=>'zjhb',   //平台编号
            'zoneno'=>$zoneno['bankcode'],
          ),
          'req'=>array(
            'loaninfo'=>array(
              'loanamount'=>$params['loan_prize'],    //申请分期金额,单位到元   loan_prize
              'term'=>$params['loan_date'],    //贷款期限，单位到月  loan_date
              'feeratio'=>$params['loan_rate'],    //手续费率，如手续费是8.5%，就传8.5过来，不传0.085  loan_rate
              'feeamount'=>strval(floatval($params['loan_prize']) * floatval($params['loan_rate']) / 100),    //手续费，单位到元  手续费=贷款金额*总利率/100
              'carprice'=>$params['car_price'],    //车辆价格，单位到元  car_price
              'loanratio'=>strval(100-floatval($params['first_pay_ratio'])),    //贷款成数，贷款百分比，如百分比是85%，就传85过来，不传0.85  贷款成数=100 - 首付比例(first_pay_ratio)
            ),
            'dscode'=>'',    //营销代码
            'drawzono'=>'',    //领卡地区号
            'drawbrno'=>'',    //领卡网点号
            'custsort'=>'000',    //证件类型，必输
            'custcode'=>$params['customer_certificate_number'],    //证件号码，必输
            'chnsname'=>$params['customer_name'],    //姓名，必输
            'engname'=>strtolower($this->random_str(6)),    //姓名拼音或英文名，必输
            'sex'=>($params['customer_sex']=='男') ? '1' : '2',    //性别，必输，1：男 2：女
            'mrtlstat'=>$params['mrtlstat'],    //婚姻状况 0：未知1=>未婚（无配偶）2=>已婚（有配偶）3=>分居4=>离异5=>丧偶6=>其他  customer_marital_status
            'birthdate'=>$params['customer_birthdate'],    //出生日期。YYYYMMDD
            'edulvl'=>$params['education_level'],    //教育程度必输 0=>未知 1=>博士及以上2=>硕士研究生3=>大学本科4=>大学专科5=>中专6=>技校职高7=>高中8=>初中9=>小学及以下  education_level
            'homestat'=>$params['housing_situation'],  //住宅状况1=>自有住房2=>分期付款购房3=>租房4=>其他5=>集体宿舍6=>单位分配 housing_situation
            'hadrchoic'=>'3',    //住宅地址选择1-预查询，2-修改，3-新增。默认送3
            'hprovince'=>'0',    //住宅地址省份，必输
            'hcity'=>'0',    //住宅地址市，必输
            'hcounty'=>'0',    //住宅地址县，必输
            'haddress'=>$params['customer_address'],    //住宅地址，必输
            'haddrid'=>'',    //住宅地址ID
            'homezip'=>$params['housing_postcode'],    //住宅邮编，必输  housing_postcode
            'hphonzono'=>'',    //住宅电话区号
            'hphoneno'=>'0',    //住宅电话号码，必输,若无送0
            'hphonext'=>'',    //住宅电话分机
            'mblchoic'=>'3',    //手机选择，1-预查询，2-修改，3-新增。默认送3
            'mvblno'=>$params['customer_telephone'],    //手机号码。必输，必须为运营商号段  customer_telephone
            'mblid'=>'',    //手机ID
            'unitname'=>$params['customer_company_name'],    //工作单位，必输
            'cophozono'=>'0',    //单位电话区号，必输,若无送0
            'cophoneno'=>'0',    //单位电话号码，必输,若无送0
            'cophonext'=>'0',    //单位电话分机，必输,若无送0
            'cadrchoic'=>'3',    //单位地址选择，1-预查询，2-修改，3-新增。默认送3
            'cprovince'=>'0',    //单位地址省份，必输 
            'ccity'=>'0',    //单位地址市，必输
            'ccounty'=>'0',    //单位地址县，必输
            'caddress'=>$params['company_address'],    //工作单位地址，必输 company_address
            'caddrid'=>'',    //单位地址ID
            'corpzip'=>'0',    //单位邮编，必输,若无送0
            'yearincome'=>'',    //本人年收入，元为单位   
            'modelcode'=>$params['business_nature'],    //单位性质，必输10=>国有110=>民营190=>其他60=>私营70=>个体  business_nature
            'occptn'=>$params['profession'],    //职业必输1=>公务员17=>学生26=>管理人员29=>无职业3=>其它行业职员;30=>私人业主4=>军人5=>自由职业者7=>农民profession
            'comchoic'=>'3',    //通讯地址选择1-预查询，2-修改，3-新增。当通讯区地址省份不为空时，当新增   
            'commprov'=>'',    //通讯地址省份
            'commcity'=>'',    //通讯地址市，当通讯地址省份为空，必须为空
            'commcounty'=>'',    //通讯地址县。当通讯地址省份为空，必须为空
            'commaddr'=>'',    //通讯地址。当通讯地址省份为空，必须为空
            'commaddrid'=>'',    //通讯地址ID
            'commazip'=>'',    //通讯地址邮编，当通讯地址省份不为空，必须不为空；否则必须为空
            'reltname1'=>$params['reltname1'],    //联系人一姓名,必输  根据关系取联系人或配偶的手机号  
            'reltsex1'=>$params['reltsex1'],    //联系人一性别,必输
            'reltship1'=>$params['reltship1'],    //联系人一与主卡申请关系必输 1-父子2-母子3-兄弟姐妹4-亲属5-夫妻6-同学7-同乡8-朋友9-同事
            'reltmobl1'=>$params['reltmobl1'],    //联系人一手机必输，只能输入数字，无送0  根据关系取联系人或配偶的手机号
            'reltphzon1'=>'',    //联系人一联系电话区,只能输入数字
            'relaphone1'=>$params['relaphone1'],    //联系人一联系电话号,必输,只能输入数字，无送0
            'reltphext1'=>'',    //联系人一联系电话分,只能输入数字
            'reltname2'=>$params['reltname1'],    //联系人二姓名,必输
            'reltsex2'=>$params['reltsex1'],    //联系人二性别,必输
            'reltship2'=>$params['reltship1'],    //联系人二与主卡申请 必输  1-父子2-母子3-兄弟姐妹4-亲属5-夫妻6-同学7-同乡8-朋友9-同事
            'reltmobl2'=>$params['reltmobl1'],    //联系人二手机，必输只能输入数字，无送0
            'rtcophzn2'=>'',    //联系人二联系电话区，只能输入数字
            'rtcophon2'=>$params['reltmobl1'],    //联系人二联系电话号，必输  只能输入数字，无送0
            'rtcophet2'=>'',    //联系人二联系电话分  只能输入数字
            'cardkind'=>'',    //申请卡种 ，后台默认无需输入
            'cardlogo'=>'',    //申请品牌，后台默认无需输入
            'cardtype'=>'',    //申请卡类，后台默认无需输入
            'cardmedm'=>'',    //卡片介质，后台默认无需输入
            'allyno'=>'',    //联名编号，后台默认无需输入
            'almebno'=>'',    //联名单位会员号
            'fcurrtyp'=>'001',    //币种，必输（送001）
            'expdate'=>'',    //有效期，后台默认无需输入
            'feeflag'=>'',    //年费标志，后台默认无需输入
            'frfeeflag'=>'',    //免年费标志，后台默认无需输入
            'accgetm'=>'4',    //对帐单寄送方式，必输0=>自取1=>寄送2=>自助打印3=>对账簿4=>不打印
            'accaddrf'=>'1',    //对帐单寄送地址，1-单位地址
            'rmbcred'=>'',    //申请本币额度，元为单位
            'mamobile'=>$params['customer_telephone'],    //主卡发送移动电话，必须输入，系统传送客户手机号   customer_telephone
            'smsphone'=>$params['customer_telephone'],    //发送短信帐单手机号码，必须输入，系统传送客户手机号  customer_telephone
            'qesno'=>'',    //问题编号  问题编号和问题答案只能同时输入,或者都不输 
            'answer'=>'',    //问题答案， 问题编号和问题答案只能同时输入,或者都不输
            'crdrflag'=>'',    //产品标志
            'authref'=>$params['idcard_authority'],    //发证机关  必输  idcard_authority
            'statdate'=>$params['idcard_valid_endtime'],    //证件有效期 必输 yyyymmdd   截止日期  idcard_valid_endtime
            'indate'=>'00000000',    //何时入住现址 必输 YYYYMMDD  系统默认传空值
            'joindate'=>'000000',    //进入单位时间 必输 YYYYMMDD  系统默认传空值
            'reltno'=>'',    //关系账号
            'cardbin'=>'',    //申请卡BIN，后台默认无需输入
            'drawmode'=>'1',    //卡片领取方式 必输 1：自取2：寄送
            'drawaddr'=>'',    //卡片寄送地址类型  1-单位地址2-住宅地址3-通讯地址领卡方式选择邮寄时为必输项，对应地址不允许为空
            'saleno'=>'',    //营销档案编号，后台默认无需输入
            'machgf'=>'0',    //主卡开通余额变动提醒 必输 0：否 1：是
            'machgmobile'=>'',    //主卡余额提醒发送手机号码 余额变动提醒打开必输
            'emladdrf'=>'0',    //开通email对账单，必输 0：否1：是
            'stmtemail'=>'',    //对帐单EMAIL，开通email对账单必输
            'postxflg'=>'',    //POS转帐标志，后台默认无需输入
            'slftxflg'=>'',    //自助终端转帐标志，后台默认无需输入
            'atmtxflg'=>'',    //ATM转帐标志，后台默认无需输入
            'ddtrxtype'=>'',    //自动还款交易类型，后台默认无需输入
            'outacctype1'=>'',    //自动还款转出帐户类型1，1-贷记卡、准贷记卡、专用卡
            'outcardno1'=>'',    //转出卡号/帐号1
            'ddamttype1'=>'',    //还款金额类型1，0-最优还款额还款
            'outacctype2'=>'',    //自动还款转出帐户类型2  1-贷记卡、准贷记卡、专用卡
            'outcardno2'=>'',    //转出卡号/帐号2
            'ddamttype2'=>'',    //还款金额类型2 0-最优还款额还款
            'paydays'=>'',    //提前还款天数 0至23
            'fftrxtype'=>'',    //同步签订外币自动还款 输入(申请币种不是单币种和双币种时必须输入,否则不输入)
            'ecshflag'=>'2',    //电子现金标志1-  是2-否 默认送1
            'appkind'=>'',    //申请渠道 0=>联机录入1=>个人网银，后台默认无需输入
            'creditcardno'=>'',    //产品编号 9字节
            'prodname'=>'',    //产品简称 20字节
            'featgype'=>'',    //产品特色类别 20字节
            'featname'=>'',    //产品特色名称 40字节
            'fakaorg'=>'',    //所属发卡机构名称
            'pics'=>$params['card_applications'],
            'resubmit'=>$params['resubmit'],  //继续申请标志0：申请；1：继续申请; 2:资料补充
          ),
      );
      $secretKey = "whosyourdaddy!";
      $inputJson = stripslashes(json_encode($array, JSON_UNESCAPED_UNICODE));
      $query =  $currentTime .'#' . $inputJson . '#' . $salt;
      $orgSign = md5(md5($secretKey . $query) . $salt);
      $url = env('ZHAOHUIBANK_API_URL') . "/icbc/api/creditCardApply.action";
      $data = '&inputJson=' . $inputJson . '&currentTime=' . $currentTime . '&salt=' . $salt . '&orgSign=' . $orgSign;
      $common = new Common();
      $rs = $common->object_array(json_decode($common->curl_post($url, $data)));
      $log =array();
      $log['url'] = $url;
      $log['param'] = $data;
      $log['response'] = json_encode($rs);
      $log['orderno'] = $params['id'];
      $log['idno'] = $params['customer_certificate_number'];
      $this->_zhaohui_log = new Zhaohui_log();
      $setlog = $this->_zhaohui_log->addlog($log);
      if (!isset($rs['result']) || ($rs['result'] == false)) {
        $flag = Mail::send('common.zhaohuiemail',['arr'=>$params],function($message){
            $to = 'jiangd@ifcar99.com';
            $message->to($to)->subject('朝晖支行数据返回');
        });
      }
      return $rs;
    }
    /**
     *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
     * 
     * @author Wu Junwei <www.wujunwei.net>
     * 
     * @param int $length 需要生成的字符串的长度
     * @return string 包含 大小写英文字母 和 数字 的随机字符串
     */
    public function random_str($length)
    {
        //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
        $arr = array_merge(range('a', 'z'), range('A', 'Z'));
     
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }
     
        return $str;
    }
}
function curl_zhaohui($post_url, $post_data) 
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $post_url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    return $error ? $error : $result;
}
