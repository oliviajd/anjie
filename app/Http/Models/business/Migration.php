<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Common;
use App\Http\Models\table\B_image;
use App\Http\Models\table\B_guestinfo;
use App\Http\Models\table\B_surveyorder;
use App\Http\Models\table\B_budgetorder;
use App\Http\Models\table\B_workflow;
use App\Http\Models\table\G_dict;
use App\Http\Models\table\Anjie_work;
use App\Http\Models\table\Anjie_task;
use App\Http\Models\table\Anjie_product;
use App\Http\Models\table\Linrun_image;
use App\Http\Models\table\Anjie_file;
use Mail;

class Migration extends Model
{
  protected $_b_image = null;
  protected $_bankinfo = array(
    '01' => '济南市中工行',
    '02' => '济南乐源支行',
    '03' => '临沂经开行'
  );

  public function __construct()
  {
    parent::__construct();
    $this->_common = new Common();
    // $this->_b_image = new B_image();
    $this->_b_guestinfo = new B_guestinfo();
    $this->_b_surveyorder = new B_surveyorder();
    $this->_b_budgetorder = new B_budgetorder();
    $this->_b_workflow = new B_workflow();
    $this->_g_dict = new G_dict();
    $this->_anjie_work = new Anjie_work();
    $this->_anjie_task = new Anjie_task();
    $this->_anjie_product = new Anjie_product();
    $this->_linrun_image = new Linrun_image();
    $this->_anjie_file = new Anjie_file();
  }
  public function migrationdata2()
  {
    $where = " where RecId is not null";
    $all = $this->_anjie_work->getdetail2($where);
    foreach ($all as $key => $value) {
      $first_pay_ratio = strval(round(floatval($value['first_pay_ratio']), 2)) . '%';
      $gross_premium = floatval($value['compulsory_insurance']) + floatval($value['vehicle_vessel_tax']) + floatval($value['commercial_insurance']);
      $updatefirstpayratio = $this->_anjie_work->updatefirstpayratio($first_pay_ratio, $gross_premium, $value['id']);
      // $delete = array();
      // $task = $this->_anjie_task->getinfobyworkid($value['id']);
      // foreach ($task as $k => $v) {
      //   if ($k != '0') {
      //     $delete[] = $v['id'];
      //   }
      // }
      // $deletetask = $this->_anjie_task->deletebyid($delete);
      // if ($deletetask == false) {
      //   echo "有错误";exit;
      // }
    }
  }
  public function imigratefile()
  {
    ini_set('max_execution_time', '0');
    $where = " where has_transfer = 2";
    $limit = " order by ID asc limit 300";
    $image = $this->_linrun_image->getimage($where, $limit);
    foreach ($image as $key => $value) {
      $path = 'uploads/linrun/'; 
      $info = $this->_anjie_work->getinfoByRecid($value['RecID']);
      if (!empty($info)) {
        $work_id = $info['id'];
      } else {
        $updatestatus = $this->_linrun_image->updatestatus3($value['ID']);
        continue;
      }
      $params['work_id'] = $work_id;
      $params['file_id'] =  $value['ID'] .'_ID_'. $value['Name'];
      $params['file_path'] = $path . $params['file_id'] . '.jpg';
      $addfile = $this->_anjie_file->addimage2($params);
      $updatestatus = $this->_linrun_image->updatestatus($value['ID']);
    }
  }

  public function get()
  { 
      ini_set('max_execution_time', '0');
      // $url = "http://feature-lsk-oss.apitest-feature.ifcar99.com/sts/get_web_config?file_type=image";
      // $imageaccess = $this->_common->object_array(json_decode($this->_common->curltest($url)['data']));
      $limitout = " limit 1";
  		$image = $this->_b_image->get($limitout);
  	  	foreach ($image as $key => $value) {
  	  		$filename = $value['Name'] . '.jpg';
  	  		$path = 'uploads/linrun/';   //文件上传的路径，基础路径为public
  	  		$fileput = file_put_contents($path . $filename, $value['Data']);
  	  		$this->_linrun_image->addimage($value['ID'], $value['RecID'], $value['Type'], $value['Name'], $value['Width'], $value['Height'], $value['Weights'], $value['IsDeleted'], $imageaccess);
          // $unlinkfile = unlink(public_path('uploads/linrun/'.$filename));   //删除文件
  	  	}
  }
  //照片迁移
  public function migrationimage()
  {
    $where = " where aliyun_status = 2";
    $limit = " limit 1";
    $rs = $this->_linrun_image->getimage($where, $limit);
    foreach ($rs as $key => $value) {
      $file = fopen(public_path('uploads/linrun/'.$value['Name'] . '.jpg'), 'r');
      $url = "http://static02.ifcar99.com/";
      $post_data = array (
        "file" => new \CURLFile(public_path('uploads/linrun/'.$value['Name'] . '.jpg'))
      );
      $rs = $this->curl_post_aliyun($url, $post_data);
      var_dump($rs);
    }
  }

  public function updateusers()
  {
    ini_set('max_execution_time', '0');
    $users = $this->_linrun_image->getusers();
    var_dump($users);

  }

  public function curl_post_aliyun($post_url, $post_data) 
  {
      $headers = array();
      $headers[] = 'Content-Disposition: PIC_20160728_161815_055.jpg';
      $headers[] = 'Content-Encoding: gzip, deflate';
      $headers[] = 'Expires: 1512617261';
      $headers[] = 'Authorization: OSS STS.GB2JaAfJo6NoUGyMFoVdpBMSb:EytpMpuY7kuc6n1JaXGwHanOvF4=';
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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
  //数据迁移
  public function migrationdata()
  {
    ini_set('max_execution_time', '0');
    $needmigration = $this->_b_surveyorder->getneed();
    $result = array();
    foreach ($needmigration as $key => $value) {
      $checkRecid = $this->_anjie_work->checkRecid($value['RecID']);
      if (!empty($checkRecid)) {
        continue;
      }
      $result[$key]['RecId'] = $value['RecID'];    //档案号
      $result[$key]['constract_no'] = $value['RecID'];  //合同编号等于档案号

      $guestinfo = $this->_b_guestinfo->getinfobyRecId($value['RecID']);                     
      $result[$key]['customer_name'] = $guestinfo['GuestName'];                            //客户姓名
      $result[$key]['customer_telephone'] = $guestinfo['GuestPhone'];                      //客户电话
      $result[$key]['create_time'] = $guestinfo['CreateTime'];                             //入库时间
      $result[$key]['modify_time'] = $guestinfo['CreateTime'];                             //修改时间
      $result[$key]['customer_sex'] = $guestinfo['GuestSex'];                              //性别
      $result[$key]['customer_certificate_number'] = $guestinfo['GuestID'];                //身份证号
      $result[$key]['customer_age'] = $this->_common->getAgeByID($guestinfo['GuestID']);   //年龄
      $result[$key]['customer_birthdate'] = intval(substr($guestinfo['GuestID'],6,8));
      $result[$key]['customer_address'] = $guestinfo['HomeAddress'];                        //居住地  
      if ($guestinfo['Guarantor'] == NULL) {
        $result[$key]['customer_has_bondsman'] = '2';                                       //1为有担保人，2为无担保人
      } else {
        $result[$key]['customer_has_bondsman'] = '1';                                       //1为有担保人，2为无担保人
        $Guarantor = explode(',', $guestinfo['Guarantor']);
        if (!empty($Guarantor)) {
          $result[$key]['bondsman_name'] = $Guarantor['1'];                                   //担保人姓名
          $result[$key]['bondsman_certificate_number'] = $Guarantor['3'];                     //担保人身份证号
          $result[$key]['bondsman_telephone'] = $Guarantor['2'];                              //担保人移动电话
          $result[$key]['bondsman_company_name'] = $Guarantor['4'];                           //担保人工作单位
          $result[$key]['bondsman_company_telephone'] = '';                                   //担保人单位电话
          $result[$key]['bondsman_company_address'] = $Guarantor['5'];                        //担保人公司地址
        }
      }         
      $result[$key]['customer_company_name'] = $guestinfo['GuestCompanyName'];                             //客户工作单位
      $result[$key]['customer_company_phone_number'] = $guestinfo['GuestCompanyTelePhone'];                  //客户公司电话
      $result[$key]['company_address'] = $guestinfo['GuestCompanyAddress'];                  //单位地址
      if ($value['Relationship'] == '夫妻') {
        $result[$key]['customer_marital_status'] = '1';                                  //婚姻状况 1：已婚，2：未婚
        $result[$key]['spouse_name'] = $guestinfo['MateName'];                  //配偶地址
        $result[$key]['spouse_certificate_number'] = $guestinfo['MateID'];                  //配偶身份证
        $result[$key]['spouse_telephone'] = $guestinfo['MatePhone'];                  //配偶手机号
        $result[$key]['spouse_company_name'] = $guestinfo['MateCompanyName'];                  //配偶公司名称
        $result[$key]['spouse_company_telephone'] = $guestinfo['MateCompanyTelePhone'];                  //配偶公司电话
        $result[$key]['spouse_company_name'] = $guestinfo['MateCompanyAddress'];
      } else {
        $result[$key]['customer_marital_status'] = '2';                                   //婚姻状况 1：已婚，2：未婚
        if ($guestinfo['FamilyMembers'] != NULL) {
          $FamilyMembers = explode(',', $guestinfo['FamilyMembers']);
          $result[$key]['contacts_man_relationship'] = '4';                              //联系人关系：其他                  
          $result[$key]['contacts_man_name'] = $FamilyMembers['1'];                      //联系人姓名
          $result[$key]['contacts_man_certificate_number'] = $FamilyMembers['3'];        //联系人身份证号
          $result[$key]['contacts_man_telephone'] = $FamilyMembers['2'];                 //联系人手机号
        }
      }

      $budgetorder = $this->_b_budgetorder->getinfobyRecId($value['RecID']);      
      if ($budgetorder['PurchaseType'] == '0') {                                            //车辆类型
        $result[$key]['product_class_number'] = 'XC';
        $result[$key]['product_id'] = '1';
        $result[$key]['product_name'] = '新车';
      } else {
        $result[$key]['product_class_number'] = 'ES';
        $result[$key]['product_id'] = '2';
        $result[$key]['product_name'] = '二手车';
      }
      $result[$key]['car_brand'] = $budgetorder['CarBrands'];                                 //车辆品牌
      $result[$key]['car_type'] = $budgetorder['CarDisplacement'];                                 //车辆型号
      $result[$key]['car_price'] = $budgetorder['CarPrice'];                                 //车辆价格
      $result[$key]['loan_bank'] = $budgetorder['BankID'];                                 //贷款银行
      $result[$key]['loan_prize'] = $budgetorder['LoanMoney'];                             //贷款金额
      $result[$key]['loan_date'] = $budgetorder['LoanTime'].'个月';                             //贷款期数
      $result[$key]['first_pay'] = $budgetorder['FirstPay'];                             //首付款
      $result[$key]['first_pay_ratio'] = $budgetorder['FirstPay']/$budgetorder['CarPrice'];                             //首付比例
      $result[$key]['loan_rate'] = $budgetorder['BankRate'];                             //利率
      if ($budgetorder['InsuranceCompany'] == '无') {
        $result[$key]['has_insurance'] = '2';
      } else {
        $result[$key]['has_insurance'] = '1';
        $result[$key]['insurance_company'] = $budgetorder['InsuranceCompany'];   //保险公司
        $result[$key]['commercial_insurance'] = $budgetorder['InsuranceMoney1']; //商业险
        $result[$key]['compulsory_insurance'] = $budgetorder['InsuranceMoney2']; //交强险
        $result[$key]['vehicle_vessel_tax'] = $budgetorder['InsuranceMoney3']; //车船税
      }
      $result[$key]['merchant_id'] = '1';
      $result[$key]['merchant_name'] = '经销商';
      $result[$key]['merchant_class_number'] = 'HB';
      if ($value['OrderState'] == '17') {   //已完成
        $result[$key]['item_status'] = '2';
        $result[$key]['current_item_id'] = '|-2|';
        $result[$key]['task_status'] = '1';
      } else {
        $result[$key]['item_status'] = '3';
        $result[$key]['current_item_id'] = '|-1|';
        $result[$key]['task_status'] = '2';
      }
      $inputrequst = $this->checkinputrequestparams($result[$key]);
      $salesinfo = array(
        'id' => '',
        'city' => '',
      );
      $requestrs = $this->_anjie_work->creditrequestmigration($inputrequst, $salesinfo);
      $productinfo = $this->_anjie_product->getInfoByid($requestrs['product_id']);   //产品信息
      $time = strtotime(date('Ymd'));   //今天凌晨的时间戳
      $count = $this->_anjie_work->getAccounttoday($requestrs['id'], $requestrs['merchant_class_number'], $time);   //获取今天的第几笔记录
      $merchant_no = 'HB' . date('Ymd') . sprintf("%05d", $count['count']);      //商机编号
      $productcount = $this->_anjie_work->getAccountByproduct($requestrs['product_id'], $requestrs['id']);    //获取该产品的第几笔
      $product_no = $productinfo['product_number']. sprintf("%05d", $productcount['count']);       //产品编号
      $productclasscount = $this->_anjie_work->getAccountByproductclass($requestrs['product_class_number'], $requestrs['id']);    //获取该来源的第几笔
      $request_no = $productinfo['product_number'] . sprintf("%08d", $productcount['count']); //申请编号
      $setnumbers = $this->_anjie_work->setnumbers($merchant_no, $product_no, $request_no, $requestrs['id']);
      $inputinsert = $this->_anjie_work->migrationdata($inputrequst, $requestrs['id']);
      $workflowinfo = $this->_b_workflow->getinfobyfileid($value['RecID']);
      $params['msg'] = '';
      foreach ($workflowinfo as $k => $v) {
        $userinfo = $this->_b_workflow->getuserinfo($v['OptID']);
        $workinfo = $this->_g_dict->getinfobyitemid($v['ResultID']);
        $params['msg'] =  $params['msg'] .  $workinfo['ITEMNOTE'] . '  '. $userinfo['EMPNAME'] . '  ' . $v['ModifyTime'] .'<br/>' ;
      }
      $params['work_id'] = $requestrs['id'];
      $params['task_status'] =$result[$key]['task_status'];
      $this->_anjie_task->migrationtask($params);
    }
  }
  //验证申请录入中的参数
  public function checkinputrequestparams($params)
  {
    $rs['customer_marital_status'] = isset($params['customer_marital_status']) ? $params['customer_marital_status'] : '';   //婚姻状况，1为已婚，2为未婚'
    $rs['customer_has_bondsman'] = isset($params['customer_has_bondsman']) ? $params['customer_has_bondsman'] : '';   //客户是否有担保人
    $rs['customer_name'] = isset($params['customer_name']) ? $params['customer_name'] : '';   //客户姓名
    $rs['customer_certificate_number'] = isset($params['customer_certificate_number']) ? $params['customer_certificate_number'] : '';   //身份证号
    $rs['customer_telephone'] = isset($params['customer_telephone']) ? $params['customer_telephone'] : '';   //手机号
    $rs['customer_address'] = isset($params['customer_address']) ? $params['customer_address'] : '';   //户口所在地
    $rs['hukou'] = isset($params['hukou']) ? $params['hukou'] : '';   //户口所在地
    $rs['customer_company_name'] = isset($params['customer_company_name']) ? $params['customer_company_name'] : '';   //客户公司地址
    $rs['customer_company_phone_number'] = isset($params['customer_company_phone_number']) ? $params['customer_company_phone_number'] : '';   //客户公司电话
    $rs['company_address'] = isset($params['company_address']) ? $params['company_address'] : '';   //客户公司地址
    $rs['spouse_name'] = isset($params['spouse_name']) ? $params['spouse_name'] : '';   //配偶姓名
    $rs['spouse_certificate_number'] = isset($params['spouse_certificate_number']) ? $params['spouse_certificate_number'] : '';   //配偶身份证号码
    $rs['spouse_telephone'] = isset($params['spouse_telephone']) ? $params['spouse_telephone'] : '';   //'配偶电话号码'
    $rs['spouse_company_name'] = isset($params['spouse_company_name']) ? $params['spouse_company_name'] : '';   //配偶公司名称
    $rs['spouse_company_telephone'] = isset($params['spouse_company_telephone']) ? $params['spouse_company_telephone'] : '';   //配偶公司电话
    $rs['spouse_company_address'] = isset($params['spouse_company_address']) ? $params['spouse_company_address'] : '';   //配偶公司地址
//联系人信息
    $rs['contacts_man_name'] = isset($params['contacts_man_name']) ? $params['contacts_man_name'] : '';   //联系人姓名
    $rs['contacts_man_relationship'] = isset($params['contacts_man_relationship']) ? $params['contacts_man_relationship'] : '';   //关系
    $rs['contacts_man_certificate_number'] = isset($params['contacts_man_certificate_number']) ? $params['contacts_man_certificate_number'] : '';   //联系人身份证号
    $rs['contacts_man_telephone'] = isset($params['contacts_man_telephone']) ? $params['contacts_man_telephone'] : '';   //联系人手机号
    $rs['bondsman_name'] = isset($params['bondsman_name']) ? $params['bondsman_name'] : '';   //'担保人姓名',
    $rs['bondsman_certificate_number'] = isset($params['bondsman_certificate_number']) ? $params['bondsman_certificate_number'] : '';   //担保人身份证号码
    $rs['bondsman_telephone'] = isset($params['bondsman_telephone']) ? $params['bondsman_telephone'] : '';   //担保人电话
    $rs['bondsman_company_name'] = isset($params['bondsman_company_name']) ? $params['bondsman_company_name'] : '';   //担保人公司名称
    $rs['bondsman_company_telephone'] = isset($params['bondsman_company_telephone']) ? $params['bondsman_company_telephone'] : '';   //担保人公司电话
    $rs['bondsman_company_address'] = isset($params['bondsman_company_address']) ? $params['bondsman_company_address'] : '';   //担保人公司地址
    $rs['car_brand'] = isset($params['car_brand']) ? $params['car_brand'] : '';   //车辆品牌
    $rs['car_type'] = isset($params['car_type']) ? $params['car_type'] : '';   //车辆型号
    $rs['deposit_bank'] = isset($params['deposit_bank']) ? $params['deposit_bank'] : '';   //开户银行
    $rs['bank_account_name'] = isset($params['bank_account_name']) ? $params['bank_account_name'] : '';   //银行户名',
    $rs['card_number'] = isset($params['card_number']) ? $params['card_number'] : '';   //打款卡号
    $rs['constract_no'] = isset($params['constract_no']) ? $params['constract_no'] : '';   //合同编号
    $rs['car_brand'] = isset($params['car_brand']) ? $params['car_brand'] : '';   //车辆品牌
    $rs['car_type'] = isset($params['car_type']) ? $params['car_type'] : '';   //车辆型号
    $rs['car_price'] = isset($params['car_price']) ? $params['car_price'] : '';   //车辆价格
    $rs['car_vehicle_identification_number'] = isset($params['car_vehicle_identification_number']) ? $params['car_vehicle_identification_number'] : '00000000000000000';   //车架号
    $rs['loan_prize'] = isset($params['loan_prize']) ? $params['loan_prize'] : '';   //贷款金额
    $rs['loan_date'] = isset($params['loan_date']) ? $params['loan_date'] : '';   //贷款期数
    $rs['first_pay'] = isset($params['first_pay']) ? $params['first_pay'] : '';   //首付款
    $rs['first_pay_ratio'] = isset($params['first_pay_ratio']) ? $params['first_pay_ratio'] : '';   //首付比例
    $rs['loan_rate'] = isset($params['loan_rate']) ? $params['loan_rate'] : '';   //利率
    $rs['has_insurance'] = isset($params['has_insurance']) ? $params['has_insurance'] : '';   //有无保险
    $rs['loan_bank'] = isset($params['loan_bank']) ? $params['loan_bank'] : '';   //有无保险
    $rs['insurance_company'] = isset($params['insurance_company']) ? $params['insurance_company'] : '';   //保险公司
    $rs['commercial_insurance'] = isset($params['commercial_insurance']) ? $params['commercial_insurance'] : '';   //商业险
    $rs['compulsory_insurance'] = isset($params['compulsory_insurance']) ? $params['compulsory_insurance'] : '';   //交强险
    $rs['vehicle_vessel_tax'] = isset($params['vehicle_vessel_tax']) ? $params['vehicle_vessel_tax'] : '';   //车船税
    $rs['gross_premium'] = isset($params['gross_premium']) ? $params['gross_premium'] : '';   //保费总额
    $rs['total_expense'] = isset($params['total_expense']) ? $params['total_expense'] : '';   //费用合计
    $rs['inputrequest_description'] = isset($params['inputrequest_description']) ? $params['inputrequest_description'] : '';   //  备注信息
    $rs['fee'] = floatval($rs['loan_prize']) * floatval($rs['loan_rate']) /100;
    $rs['create_time'] = isset($params['create_time']) ? $params['create_time'] : '';   //创建时间
    $rs['modify_time'] = isset($params['modify_time']) ? $params['modify_time'] : '';   //修改时间
    $rs['customer_sex'] = isset($params['customer_sex']) ? $params['customer_sex'] : '';   //性别
    $rs['customer_age'] = isset($params['customer_age']) ? $params['customer_age'] : '';   //年龄
    $rs['product_class_number'] = isset($params['product_class_number']) ? $params['product_class_number'] : '';   //产品名称
    $rs['product_name'] = isset($params['product_name']) ? $params['product_name'] : '';   //产品名称
    $rs['product_id'] = isset($params['product_id']) ? $params['product_id'] : '';   //产品名称
    $rs['merchant_id'] = isset($params['merchant_id']) ? $params['merchant_id'] : '';   //产品名称
    $rs['merchant_name'] = isset($params['merchant_name']) ? $params['merchant_name'] : '';   //产品名称
    $rs['merchant_class_number'] = isset($params['merchant_class_number']) ? $params['merchant_class_number'] : '';   //产品名称
    $rs['customer_birthdate'] = isset($params['customer_birthdate']) ? $params['customer_birthdate'] : '';   //客户生日
    $rs['item_status'] = isset($params['item_status']) ? $params['item_status'] : '';   //客户生日
    $rs['current_item_id'] = isset($params['current_item_id']) ? $params['current_item_id'] : '';   //客户生日
    $rs['RecId'] = isset($params['RecId']) ? $params['RecId'] : '';   //客户生日
    return $rs;
  }
}

