<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_work extends Model
{
    protected  $table='anjie_work';
    public $primaryKey='id';
    public $timestamps=false;
    protected $_pdo = null;
    //id对应的流程title
    protected $_anjie_task_item = array(
        '|2|' => '征信申请',
        '|3|' => '银行征信查询',
        '|7|' => '家访签约',
        '|8|' => '申请录入',
        '|9|' => '人工一审',
        '|37|' => '人工二审',
        '|39|' => '财务打款',
        '|40|' => '回款确认',
        '|41|' => '寄件登记',
        '|42|' => '抄单登记',
        '|43|' => 'GPS登记',
        '|44|' => '抵押登记',
        '|45|' => '申请件补件',
        '|47|' => '申请打款',
        '|48|' => '打款审核',
        '|60|' => '银行征信查询',
        '|-1|' => '已拒件',
        '|-2|' => '已完成',
    );
    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //获取所有
    public function getall()
    {
        $sql = "select * from anjie_work";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs; 
    }
    //
    public function getdetailByid($id)
    {
        $sql = "select * from anjie_work where id =? and status =1";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }
    public function checkRecid($RecId)
    {
        $sql = "select * from anjie_work where RecId = ?";
        $rs = $this->_pdo->fetchOne($sql, array($RecId));
        return $rs;
    }

    public function setSuccessStatus($work_id){
        $sql = "UPDATE anjie_work set upload_status = 1 WHERE id = ? and status =1";
        return $this->_pdo->execute($sql,[$work_id]);
    }

    //联表查询获取该件的详情
    public function getAppDeatail($order, $limit, $where)
    {
        if (!empty($where)) {
            $where = $where . ' and status =1';
        }
        $sql = "select a.work_id, a.supplement_status, b.customer_sex, a.visit_date, a.visit_status, a.has_pickup, a.to_user_id, a.create_time, a.pick_up_time, b.customer_name, b.customer_telephone, b.request_no, b.product_class_number as carclass from anjie_visit as a, anjie_work as b " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //联表查询获取该查询条件的数量
    public function getAppCount($where)
    {
        if (!empty($where)) {
            $where = $where . ' and status =1';
        }
        $sql = "select count(1) as count from anjie_visit as a, anjie_work as b ". $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
    
    //通过id获取该件的所有信息
    public function getInfoByid($id)
    {
        $sql = "select a.*, b.title as current_item_title from anjie_work as a left join anjie_task_item as b on a.current_item_id= b.v1_item_id where a.id =? and a.status =1";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        if (empty($rs)) {
            return $rs;
        }
        $current_item_title = array();
        $current_item_ids = explode(',', $rs['current_item_id']);
        $current_item_ids = array_unique($current_item_ids);
        foreach ($current_item_ids as $k => $v) {
            if (isset($this->_anjie_task_item[$v])) {
                $current_item_title[] = $this->_anjie_task_item[$v];
            }
        }
        $rs['current_item_title'] = implode('，', $current_item_title);
        return $rs;
    }

    public function getAppinfoById($id)
    {
        $sql = "select customer_name, customer_sex, customer_certificate_number, receiver_name, receiver_telephone, customer_address, customer_telephone, product_name, request_no, inquire_result, inquire_description,constract_no, visit_description, visit_arrive_time, visit_leave_time, visit_date, visit_arrive_lat, visit_arrive_lng, visit_leave_lat, visit_leave_lng, visit_arrive_address, visit_leave_address, merchant_name from anjie_work where id =? and status =1";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }

    public function getAppinfoByIds($ids, $where ='')
    {
        $idsarr = "'".implode("','", $ids)."'";
        $sql = "select id as work_id, customer_name, customer_telephone, request_no, product_class_number as carclass from anjie_work where status =1 and  id in(".$idsarr.")". $where ." order by modify_time desc";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //征信申请，插入数据
    public function creditrequest($params, $salesinfo)
    {
        // $params['customer_address'] = $params['customer_province'] . $params['customer_city'] .$params['customer_town'] . $params['customer_address_add'];   //地址
        $sql = "insert into anjie_work (customer_name, customer_certificate_number, customer_sex, customer_telephone, merchant_id, product_id, merchant_name, product_name,  create_time, modify_time, product_class_number, merchant_class_number, customer_birthdate, salesman_id, customer_age, credit_city, customer_has_bondsman, customer_marital_status, spouse_name, spouse_certificate_number, bondsman_name, bondsman_certificate_number, bondsman_spouse_name, bondsman_spouse_idcard, loan_bank) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($params['customer_name'], $params['customer_certificate_number'], $params['customer_sex'], $params['customer_telephone'], $params['merchant_id'], $params['product_id'], $params['merchant_name'], $params['product_name'], time(), time(), $params['product_class_number'], $params['merchant_class_number'], $params['customer_birthdate'], $salesinfo['id'], $params['customer_age'], $salesinfo['city'], $params['customer_has_bondsman'], $params['customer_marital_status'], $params['spouse_name'], $params['spouse_certificate_number'], $params['bondsman_name'], $params['bondsman_certificate_number'], $params['bondsman_spouse_name'], $params['bondsman_spouse_idcard'], $params['loan_bank']));
        $return = $params;
        $return['id'] = $this->_pdo->lastInsertId();
        if ($return) {
            return $return;
        }
        return false;
    }
    //取该申请件来源今天的单数
    public function getAccounttoday($id, $merchant, $time)
    {
        $sql = "select count(1) as count from anjie_work where create_time >= ? and merchant_class_number =? and id <= ?";
        $rs = $this->_pdo->fetchOne($sql, array($time, $merchant, $id));
        return $rs;
    }
    //取该产品对应的单数
    public function getAccountByproduct($product_id, $id)
    {
        $sql = "select count(1) as count from anjie_work where product_id = ? and id <= ?";
        $rs = $this->_pdo->fetchOne($sql, array($product_id, $id));
        return $rs;
    }
    //取该产品类型对应的数量
    public function getAccountByproductclass($product_class_number, $id)
    {
        $sql = "select count(1) as count from anjie_work where product_class_number = ? and id <= ?";
        $rs = $this->_pdo->fetchOne($sql, array($product_class_number, $id));
        return $rs;
    }
    //写入商机编号等编号
    public function setnumbers($merchant_no, $product_no, $request_no, $id)
    {
        $sql = "update anjie_work set merchant_no = ?, product_no=?, request_no =?, modify_time=?  where id = ?";
        $rs = $this->_pdo->execute($sql, array($merchant_no, $product_no, $request_no, time(), $id));
        return $rs;
    }
    //写入合同编号
    public function setconstractno($params)
    {
        $sql = "update anjie_work set constract_no = ?, modify_time=?  where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['constract_no'], time(), $params['work_id']));
        return $rs;
    }
    //写入拜访总结
    public function setvisitdescrip($params)
    {
        $sql = "update anjie_work set visit_description = ?, modify_time=?  where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['visit_description'], time(), $params['work_id']));
        return $rs;
    }

    //写入客户地址
    public function setcustomeraddress($params)
    {
        $sql = "update anjie_work set customer_address = ?, modify_time=?  where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['customer_address'], time(), $params['work_id']));
        return $rs;
    }

    //写入拜访到达时间
    public function setbeginvisit($params)
    {
        $sql = "update anjie_work set visit_arrive_time = ?, visit_arrive_lat =?, visit_arrive_lng =?, visit_arrive_address =?, modify_time=?  where id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $params['lat'], $params['lng'], $params['address'], time(), $params['work_id']));
        return $rs;
    }
    //写入拜访结束时间
    public function setendvisit($params, $user_id)
    {
        $sql = "update anjie_work set visit_leave_time = ?, visit_leave_lat =?, visit_leave_lng =?, visit_leave_address =?, modify_time=? , visitor_id =? where id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $params['lat'], $params['lng'], $params['address'], time(), $user_id, $params['work_id']));
        return $rs;
    }
//获取记录条数
    public function getCount($where = '')
    {
        if (!empty($where)) {
            $where = $where . ' and a.status =1';
        }
        $sql = "select count(1) as count from anjie_work as a " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function getdetail($where, $order, $limit)
    {
        if (!empty($where)) {
            $where = $where . ' and a.status =1';
        }
        $sql = "select a.id, a.merchant_no, a.request_no, a.customer_name, a.customer_telephone, a.product_class_number, a.product_name, a.merchant_name, a.create_time, a.visit_status, a.current_item_id, a.loan_prize, a.constract_prize, a.constract_no, a.constract_prize, a.return_prize, a.remittance_prize, a.customer_certificate_number, a.item_status, a.salessupplement_status, a.first_pay, a.credit_city from anjie_work as a  " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        foreach ($rs as $key => $value) {
            $current_item_title = array();
            $current_item_ids = explode(',', $value['current_item_id']);
            $current_item_ids = array_unique($current_item_ids);
            foreach ($current_item_ids as $k => $v) {
                if (isset($this->_anjie_task_item[$v])) {
                    $current_item_title[] = $this->_anjie_task_item[$v];
                }
            }
            $rs[$key]['current_item_title'] = implode('，', $current_item_title);
        }
        return $rs;
    }
//引擎平台修改某个件的任务状态逻辑
    public function workflowitem($params)
    {
        $arr = explode(',', str_replace('|', '', $params['current_item_id']));
        foreach ($arr as $key => $value) {
            if ($value !== '') {
                $arr[$key] = '|'. $value . '|';
            }
        }
        $params['current_item_id'] = implode($arr, ',');
        $sql = "update anjie_work set current_item_id =?, modify_time=?  where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['current_item_id'], time(), $params['work_id']));
        return $rs;
    }
//征信
    public function inquire($params)
    {
        $sql = "update anjie_work set inquire_result =?, inquire_description =?, inquire_status =?, modify_time=? where id =?";
        $rs = $this->_pdo->execute($sql, array($params['inquire_result'], $params['inquire_description'], $params['inquire_status'], time(), $params['work_id']));
        return $rs;
    }
    public function gettellerinfo($BankID)
    {
        $sql = "SELECT * FROM `g_tellernum` where BankID = ?  order by rand() limit 1";
        $rs = $this->_pdo->fetchOne($sql, array($BankID));
        return $rs;
    }
    //申请录入
    public function inputrequest($rs, $work_id)
    {
      $tellerinfo = $this->gettellerinfo($rs['loan_bank']);
      if (!empty($tellerinfo)) {
          $rs['TellerNum'] = $tellerinfo['TellerNum'];
      } else {
          $rs['TellerNum'] = '160201989';
      }
      $rs['car_price'] = round($rs['car_price'], 2);
      $rs['loan_prize'] = round($rs['loan_prize'], 2);
      $rs['first_pay'] = round($rs['first_pay'], 2);
      $rs['loan_rate'] = round($rs['loan_rate'], 2);
      $sql = "update anjie_work set customer_marital_status =?, customer_has_bondsman=?, customer_name=?, customer_certificate_number=?, customer_telephone=?, customer_address=?, hukou=?, customer_sex=?, customer_age =?, customer_company_name=?, customer_company_phone_number=?, company_address=?, spouse_name=?, spouse_certificate_number=?, spouse_telephone=?, spouse_company_name=?, spouse_company_telephone=?, spouse_company_address=?, contacts_man_name=?, contacts_man_relationship=?, contacts_man_certificate_number=?, contacts_man_telephone=?, bondsman_name=?, bondsman_certificate_number=?, bondsman_telephone=?, bondsman_company_name=?, bondsman_company_telephone=?, bondsman_company_address=?, bondsman_spouse_name=?, bondsman_spouse_idcard =?, constract_no=?, car_brand=?, car_type=?, car_price=?, car_vehicle_identification_number=?, loan_prize=?, loan_date=?, first_pay=?, first_pay_ratio=?, loan_rate=?, has_insurance=?, loan_bank=?, TellerNum=?, insurance_company=?, commercial_insurance=?, compulsory_insurance=?, vehicle_vessel_tax=?, gross_premium=?, total_expense=?, inputrequest_description=?, modify_time=?, carshop_name=?, carshop_address=?, contact_sex=?, spouse_sex=?, idcard_valid_starttime=?, idcard_valid_endtime=?, idcard_authority=?, housing_situation=?, housing_postcode=?, education_level=?, profession=?, business_nature=?, car_evaluation_price=?, car_evaluation_authority=?, car_use_years=?  where id = ?";
      $rs = $this->_pdo->execute($sql, array($rs['customer_marital_status'], $rs['customer_has_bondsman'], $rs['customer_name'], $rs['customer_certificate_number'], $rs['customer_telephone'], $rs['customer_address'], $rs['hukou'], $rs['customer_sex'], $rs['customer_age'], $rs['customer_company_name'], $rs['customer_company_phone_number'], $rs['company_address'], $rs['spouse_name'], $rs['spouse_certificate_number'], $rs['spouse_telephone'], $rs['spouse_company_name'], $rs['spouse_company_telephone'], $rs['spouse_company_address'], $rs['contacts_man_name'], $rs['contacts_man_relationship'], $rs['contacts_man_certificate_number'], $rs['contacts_man_telephone'] , $rs['bondsman_name'], $rs['bondsman_certificate_number'], $rs['bondsman_telephone'], $rs['bondsman_company_name'], $rs['bondsman_company_telephone'], $rs['bondsman_company_address'], $rs['bondsman_spouse_name'], $rs['bondsman_spouse_idcard'], $rs['constract_no'], $rs['car_brand'], $rs['car_type'], $rs['car_price'], $rs['car_vehicle_identification_number'], $rs['loan_prize'], $rs['loan_date'], $rs['first_pay'], $rs['first_pay_ratio'], $rs['loan_rate'], $rs['has_insurance'], $rs['loan_bank'], $rs['TellerNum'], $rs['insurance_company'], $rs['commercial_insurance'], $rs['compulsory_insurance'], $rs['vehicle_vessel_tax'], $rs['gross_premium'], $rs['total_expense'], $rs['inputrequest_description'], time(), $rs['carshop_name'], $rs['carshop_address'], $rs['contact_sex'], $rs['spouse_sex'], $rs['idcard_valid_starttime'], $rs['idcard_valid_endtime'], $rs['idcard_authority'], $rs['housing_situation'], $rs['housing_postcode'], $rs['education_level'], $rs['profession'], $rs['business_nature'], $rs['car_evaluation_price'], $rs['car_evaluation_authority'], $rs['car_use_years'], $work_id));
       return $rs;
    }
    //销售补件
    public function salessupplement($rs, $work_id)
    {
      $tellerinfo = $this->gettellerinfo($rs['loan_bank']);
      if (!empty($tellerinfo)) {
          $rs['TellerNum'] = $tellerinfo['TellerNum'];
      } else {
          $rs['TellerNum'] = '160201989';
      }
      $rs['car_price'] = round($rs['car_price'], 2);
      $rs['loan_prize'] = round($rs['loan_prize'], 2);
      $rs['first_pay'] = round($rs['first_pay'], 2);
      $rs['loan_rate'] = round($rs['loan_rate'], 2);
      $sql = "update anjie_work set customer_marital_status =?, customer_has_bondsman=?, hukou=?, customer_company_name=?, customer_company_phone_number=?, company_address=?, spouse_name=?, spouse_certificate_number=?, spouse_telephone=?, spouse_company_name=?, spouse_company_telephone=?, spouse_company_address=?, contacts_man_name=?, contacts_man_relationship=?, contacts_man_certificate_number=?, contacts_man_telephone=?, bondsman_name=?, bondsman_certificate_number=?, bondsman_telephone=?, bondsman_company_name=?, bondsman_company_telephone=?, bondsman_company_address=?, bondsman_spouse_name=?, bondsman_spouse_idcard, constract_no=?, car_brand=?, car_type=?, car_price=?, car_vehicle_identification_number=?, loan_prize=?, loan_date=?, first_pay=?, first_pay_ratio=?, loan_rate=?, has_insurance=?, loan_bank=?, insurance_company=?, commercial_insurance=?, compulsory_insurance=?, vehicle_vessel_tax=?, gross_premium=?, total_expense=?, inputrequest_description=?, modify_time=?, carshop_name=?, carshop_address=?, salessupplement_status=2, contact_sex=?, spouse_sex=?, idcard_valid_starttime=?, idcard_valid_endtime=?, idcard_authority=?, housing_situation=?, housing_postcode=?, education_level=?, profession=?, business_nature=?, car_evaluation_price=?, car_evaluation_authority=?, car_use_years=? where id = ?";
      $rs = $this->_pdo->execute($sql, array($rs['customer_marital_status'], $rs['customer_has_bondsman'], $rs['hukou'], $rs['customer_company_name'], $rs['customer_company_phone_number'], $rs['company_address'], $rs['spouse_name'], $rs['spouse_certificate_number'], $rs['spouse_telephone'], $rs['spouse_company_name'], $rs['spouse_company_telephone'], $rs['spouse_company_address'], $rs['contacts_man_name'], $rs['contacts_man_relationship'], $rs['contacts_man_certificate_number'], $rs['contacts_man_telephone'] , $rs['bondsman_name'], $rs['bondsman_certificate_number'], $rs['bondsman_telephone'], $rs['bondsman_company_name'], $rs['bondsman_company_telephone'], $rs['bondsman_company_address'], $rs['bondsman_spouse_name'], $rs['bondsman_spouse_idcard'], $rs['constract_no'], $rs['car_brand'], $rs['car_type'], $rs['car_price'], $rs['car_vehicle_identification_number'], $rs['loan_prize'], $rs['loan_date'], $rs['first_pay'], $rs['first_pay_ratio'], $rs['loan_rate'], $rs['has_insurance'], $rs['loan_bank'], $rs['insurance_company'], $rs['commercial_insurance'], $rs['compulsory_insurance'], $rs['vehicle_vessel_tax'], $rs['gross_premium'], $rs['total_expense'], $rs['inputrequest_description'], time(), $rs['carshop_name'], $rs['carshop_address'], $rs['contact_sex'], $rs['spouse_sex'], $rs['idcard_valid_starttime'], $rs['idcard_valid_endtime'], $rs['idcard_authority'], $rs['housing_situation'], $rs['housing_postcode'], $rs['education_level'], $rs['profession'], $rs['business_nature'], $rs['car_evaluation_price'], $rs['car_evaluation_authority'], $rs['car_use_years'], $work_id));
       return $rs;
    }

    //人工审核1
    public function artificialone($params, $work_id)
    {
        $sql = "update anjie_work set artificial_status=?,  artificial_refuse_reason=?, artificial_description =?, modify_time =? where id= ?";
        if ($params['artificial_status'] == '3') {
            $sql = "update anjie_work set salessupplement_status=1, artificial_status=?,  artificial_refuse_reason=?, artificial_description =?, modify_time =? where id= ?";
        }
        $rs = $this->_pdo->execute($sql, array($params['artificial_status'], $params['artificial_refuse_reason'], $params['artificial_description'], time(), $work_id));
        return $rs;
    }
    //人工审核2
    public function artificialtwo($params, $work_id)
    {
        $sql = "update anjie_work set artificialtwo_status=?,  artificialtwo_refuse_reason=?, artificialtwo_description =?, modify_time =? where id= ?";
        if ($params['artificial_status'] == '3') {
            $sql = "update anjie_work set salessupplement_status=1, artificial_status=?,  artificial_refuse_reason=?, artificial_description =?, modify_time =? where id= ?";
        }
        $rs = $this->_pdo->execute($sql, array($params['artificial_status'], $params['artificialtwo_refuse_reason'], $params['artificialtwo_description'], time(), $work_id));
        return $rs;
    }
    /**
     * 财务打款
     * @param work_id                   工作id
     * @param constract_prize           合同金额
     * @param remittance_prize          打款金额
     * @param remittance_man            打款人
     * @param remittance_time           打款时间
     * @param remittance_card           收款账户
     * @param finance_description       财务打款备注
     */
    public function finance($params, $work_id)
    {
        $sql = "update anjie_work set constract_prize =?, remittance_prize=?, remittance_man=?, remittance_time=?,remittance_card=?,finance_description =?, finance_status =?, modify_time=? where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['constract_prize'], $params['remittance_prize'], $params['remittance_man'], $params['remittance_time'], $params['remittance_card'], $params['finance_description'], $params['finance_status'], time(), $work_id));
        return $rs;
    }
    /**
     * 申请打款
     * @param work_id                   工作id
     * @param loan_principal           本金
     * @param finance_name          打款户名
     * @param finance_driving            打给车行
     * @param finance_account           账号
     * @param finance_deposit_bank           开户行
     * @param finance_amount       打款金额
     * @param finance_date       打款日期
     * @param finance_apply_description       申请打款备注
     * @param applyremittance_status       申请打款状态
     */
    public function applyremittance($params, $work_id)
    {
        $sql = "update anjie_work set finance_name=?, finance_driving=?, finance_account=?,finance_deposit_bank=?, finance_date=?, finance_apply_description=?,  applyremittance_status =?, modify_time=?, return_car_rate=?, return_car_principal=?, car_final_pay=?, remittance_total_money=?, gross_profit_rate=?, integration_principal=?, bank_lending=?, car_name=?, business_place=? where id= ?";
        $rs = $this->_pdo->execute($sql, array( $params['finance_name'], $params['finance_driving'], $params['finance_account'], $params['finance_deposit_bank'], $params['finance_date'], $params['finance_apply_description'], $params['applyremittance_status'], time(), $params['return_car_rate'], $params['return_car_principal'], $params['car_final_pay'], $params['remittance_total_money'], $params['gross_profit_rate'], $params['integration_principal'], $params['bank_lending'], $params['car_name'], $params['business_place'], $work_id));
        return $rs;
    }
    /**
     * 打款审核
     * @param work_id                   工作id
     * @param moneyaudit_status         打款审核的状态
     */
    public function moneyaudit($params, $work_id)
    {
        $sql = "update anjie_work set moneyaudit_status =?, modify_time=? where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['moneyaudit_status'], time(), $work_id));
        return $rs;
    }
    /**
     * 回款确认
     * @param work_id                   工作id
     * @param return_prize              回款金额
     * @param return_time               回款时间
     * @param return_card               回款账户
     * @param return_confirm_time       回款确认时间
     * @param return_description        回款备注
     */
    public function returnmoney($params, $work_id)
    {
        $sql = "update anjie_work set return_prize =?, return_time=?, return_card=?, return_confirm_time=?,return_description=?, returnmoney_status=?, modify_time=? where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['return_prize'], $params['return_time'], $params['return_card'], $params['return_confirm_time'], $params['return_description'], $params['returnmoney_status'], time(), $work_id));
        return $rs;
    }
    /**
     * 寄件登记
     * @param work_id                    工作id
     * @param courier_man                寄件人
     * @param courier_business           快递商
     * @param courier_number             快递单号
     * @param courier_time               寄件时间
     * @param courier_description        寄件备注
     */
    public function courier($params, $work_id)
    {
        $sql = "update anjie_work set courier_man =?, courier_business=?, courier_number=?, courier_time=?,courier_description=?, courier_status=?, modify_time=? where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['courier_man'], $params['courier_business'], $params['courier_number'], $params['courier_time'], $params['courier_description'], $params['courier_status'], time(), $work_id));
        return $rs;
    }
    /**
     * 抄单登记
     * @param work_id                             工作id
     * @param copytask_courier_man                抄单寄件人
     * @param copytask_courier_business           抄单快递商
     * @param copytask_courier_number             抄单快递单号
     * @param copytask_courier_time               抄单寄件时间
     * @param copytask_courier_description        抄单寄件备注
     */
    public function copytask($params, $work_id)
    {
        $sql = "update anjie_work set copytask_courier_man =?, copytask_courier_business=?, copytask_courier_number=?, copytask_courier_time=?,copytask_courier_description=?, copytask_status=?, modify_time=? where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['copytask_courier_man'], $params['copytask_courier_business'], $params['copytask_courier_number'], $params['copytask_courier_time'], $params['copytask_courier_description'], $params['copytask_status'], time(), $work_id));
        return $rs;
    }
    /**
     * 车辆gps登记
     * @param work_id                            工作id
     * @param license_number                     车牌号
     * @param gps_number                         GPS编号
     * @param install_man                        安装人
     * @param install_time                       安装时间
     * @param gps_description                    gps登记备注
     */
    public function gps($params, $work_id)
    {
        $sql = "update anjie_work set license_number =?, gps_number=?, gps_number2 =?, install_man=?, install_time=?,gps_description=?, gps_status=?, modify_time=?, install_position=? where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['license_number'], $params['gps_number'], $params['gps_number2'], $params['install_man'], $params['install_time'], $params['gps_description'], $params['gps_status'], time(), $params['install_position'], $work_id));
        return $rs;
    }
    /**
     * 抵押登记
     * @param work_id                            工作id
     * @param is_mortgage                     是否办理抵押
     * @param mandate_number                         委托书编号
     * @param transactor                        办理人
     * @param transact_time                       办理时间
     * @param mortgage_description                    抵押备注
     */
    public function mortgage($params, $work_id)
    {
        $sql = "update anjie_work set is_mortgage =?, mandate_number=?, transactor=?, transact_time=?,mortgage_description=?, mortgage_status=?,  modify_time=?, AmortNum =?, car_vehicle_identification_number=?, car_number=? where id= ?";
        $rs = $this->_pdo->execute($sql, array($params['is_mortgage'], $params['mandate_number'], $params['transactor'], $params['transact_time'], $params['mortgage_description'], $params['mortgage_status'], time(), $params['AmortNum'], $params['car_vehicle_identification_number'], $params['car_number'], $work_id));
        return $rs;
    }
    //拒件
    public function refusework($params)
    {
        $sql = "update anjie_work set item_status = 3, current_item_id = '|-1|', modify_time=? where id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $params['work_id']));
        return $rs;
    }
    //完成
    public function completework($params)
    {
        $sql = "update anjie_work set item_status = 2, current_item_id = '|-2|', modify_time=? where id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $params['work_id']));
        return $rs;
    }
    // //需要销售补件，且未完成
    // public function salessupplement($params)
    // {
    //     $sql = "update anjie_work set salessupplement_status = 1 where id = ?";
    //     $rs = $this->_pdo->execute($sql, array($params['work_id']));
    //     return $rs;
    // }
    public function hasIdCard($customer_certificate_number)
    {
        $sql = "select * from anjie_work where customer_certificate_number = ? and item_status=1 and status =1";
        $rs = $this->_pdo->fetchOne($sql, array($customer_certificate_number));
        return $rs;
    }
    //存入打包下载文件
    public function updatepackage($params)
    {
        $sql = "update anjie_work set file_package = ?, modify_time=? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['file_package'], time(), $params['work_id']));
        return $rs;
    }
    //下载word文档
    public function updateword($params)
    {
        $sql = "update anjie_work set word_url = ?, modify_time=? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['word_url'], time(), $params['work_id']));
        return $rs;
    }
    public function updateficodata($work_id, $params)
    {
        $sql = "update anjie_work set retCode = ?, need_new_request=?, reason=?, reasonback=?,  score=?, scoreID=?, recAction=? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['retCode'], $params['need_new_request'], $params['reason'], $params['reasonback'], $params['score'], $params['scoreID'], $params['recAction'], $work_id));
        return $rs;
    }
    public function bairongrequest($bairong_new_request, $obj, $work_id)
    {
        $sql = "update anjie_work set bairong_new_request =?, bairong_code =?, ex_bad1_name=?, ex_bad1_casenum=?, ex_bad1_court =?, ex_bad1_time=?, ex_execut1_name=?, ex_execut1_casenum=?, ex_execut1_court=?, ex_execut1_time=?, ex_execut1_money=?, ex_execut1_statute=? where id = ?";
        $rs = $this->_pdo->execute($sql, array($bairong_new_request, $obj['code'], $obj['ex_bad1_name'], $obj['ex_bad1_casenum'], $obj['ex_bad1_court'], $obj['ex_bad1_time'], $obj['ex_execut1_name'], $obj['ex_execut1_casenum'], $obj['ex_execut1_court'], $obj['ex_execut1_time'], $obj['ex_execut1_money'], $obj['ex_execut1_statute'], $work_id));
        return $rs;
    }
    public function updateficostatus($params)
    {
        $sql = "update anjie_work set need_new_request = ?,bairong_new_request=? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['need_new_request'], $params['bairong_new_request'], $params['work_id']));
        return $rs;
    }

    public function updateworkstatus($status, $work_id)
    {
        $sql = "update anjie_work set status = ? where id =?";
        $rs = $this->_pdo->execute($sql, array($status, $work_id));
        return $rs;
    }
    public function updatefirstpayratio($first_pay_ratio, $gross_premium, $work_id)
    {
        $sql = "update anjie_work set first_pay_ratio =?, gross_premium= ? where id =?";
        return $this->_pdo->execute($sql,array($first_pay_ratio, $gross_premium, $work_id));
    }
    public function getinfoByRecid($RecId)
    {
        $sql = "select * from anjie_work where RecId = ?";
        $rs = $this->_pdo->fetchOne($sql, array($RecId));
        return $rs;
    }
    //征信申请，插入数据
    public function creditrequestmigration($params, $salesinfo)
    {
        // $params['customer_address'] = $params['customer_province'] . $params['customer_city'] .$params['customer_town'] . $params['customer_address_add'];   //地址
        $sql = "insert into anjie_work (customer_name, customer_certificate_number, customer_sex, customer_telephone, merchant_id, product_id, merchant_name, product_name,  create_time, modify_time, product_class_number, merchant_class_number, customer_birthdate, salesman_id, customer_age, credit_city, item_status, current_item_id, RecId) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($params['customer_name'], $params['customer_certificate_number'], $params['customer_sex'], $params['customer_telephone'], $params['merchant_id'], $params['product_id'], $params['merchant_name'], $params['product_name'], strtotime($params['create_time']), strtotime($params['modify_time']),  $params['product_class_number'], $params['merchant_class_number'], $params['customer_birthdate'], $salesinfo['id'], $params['customer_age'], $salesinfo['city'], $params['item_status'], $params['current_item_id'], $params['RecId']));
        $return = $params;
        $return['id'] = $this->_pdo->lastInsertId();
        if ($return) {
            return $return;
        }
        return false;
    }
    public function migrationdata($rs, $work_id)
    {
      $rs['car_price'] = round($rs['car_price'], 2);
      $rs['loan_prize'] = round($rs['loan_prize'], 2);
      $rs['first_pay'] = round($rs['first_pay'], 2);
      $rs['loan_rate'] = round($rs['loan_rate'], 2);
      $sql = "update anjie_work set customer_marital_status =?, customer_has_bondsman=?, customer_name=?, customer_certificate_number=?, customer_telephone=?, customer_address=?, hukou=?, customer_sex=?, customer_age =?, customer_company_name=?, customer_company_phone_number=?, company_address=?, spouse_name=?, spouse_certificate_number=?, spouse_telephone=?, spouse_company_name=?, spouse_company_telephone=?, spouse_company_address=?, contacts_man_name=?, contacts_man_relationship=?, contacts_man_certificate_number=?, contacts_man_telephone=?, bondsman_name=?, bondsman_certificate_number=?, bondsman_telephone=?, bondsman_company_name=?, bondsman_company_telephone=?, bondsman_company_address=?, constract_no=?, car_brand=?, car_type=?, car_price=?, car_vehicle_identification_number=?, loan_prize=?, loan_date=?, first_pay=?, first_pay_ratio=?, loan_rate=?, has_insurance=?, loan_bank=?, insurance_company=?, commercial_insurance=?, compulsory_insurance=?, vehicle_vessel_tax=?, gross_premium=?, total_expense=?, inputrequest_description=?, modify_time=? where id = ?";
      $rs = $this->_pdo->execute($sql, array($rs['customer_marital_status'], $rs['customer_has_bondsman'], $rs['customer_name'], $rs['customer_certificate_number'], $rs['customer_telephone'], $rs['customer_address'], $rs['hukou'], $rs['customer_sex'], $rs['customer_age'], $rs['customer_company_name'], $rs['customer_company_phone_number'], $rs['company_address'], $rs['spouse_name'], $rs['spouse_certificate_number'], $rs['spouse_telephone'], $rs['spouse_company_name'], $rs['spouse_company_telephone'], $rs['spouse_company_address'], $rs['contacts_man_name'], $rs['contacts_man_relationship'], $rs['contacts_man_certificate_number'], $rs['contacts_man_telephone'] , $rs['bondsman_name'], $rs['bondsman_certificate_number'], $rs['bondsman_telephone'], $rs['bondsman_company_name'], $rs['bondsman_company_telephone'], $rs['bondsman_company_address'], $rs['constract_no'], $rs['car_brand'], $rs['car_type'], $rs['car_price'], $rs['car_vehicle_identification_number'], $rs['loan_prize'], $rs['loan_date'], $rs['first_pay'], $rs['first_pay_ratio'], $rs['loan_rate'], $rs['has_insurance'], $rs['loan_bank'], $rs['insurance_company'], $rs['commercial_insurance'], $rs['compulsory_insurance'], $rs['vehicle_vessel_tax'], $rs['gross_premium'], $rs['total_expense'], $rs['inputrequest_description'], time(), $work_id));
       return $rs;

    }
    //获取所有
    public function getdetail2($where = '')
    {
        $sql = "select * from anjie_work" . $where;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs; 
    }
    public function updateworkcredit($params)
    {
        $sql = "update anjie_work set result=?, loanCrdt=?, cardCrdt=?, leftNum=?, leftAmount=?, note=? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['result'], $params['loanCrdt'], $params['cardCrdt'], $params['leftNum'], $params['leftAmount'], $params['note'], $params['orderno']));
        return $rs;
    }
}
