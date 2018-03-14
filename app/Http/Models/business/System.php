<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Common;
use App\Http\Models\table\Anjie_login;
use App\Http\Models\table\Api2_log;
use App\Http\Models\table\Anjie_manaudit;
use App\Http\Models\table\T_address_province;
use App\Http\Models\table\T_address_city;
use App\Http\Models\table\T_address_town;
use App\Http\Models\table\Anjie_origin;
use App\Http\Models\table\Anjie_product;
use App\Http\Models\table\Tb_huankuan;
use App\Http\Models\table\Tb_yuqi;
use App\Http\Models\table\Jcr_version;
use App\Http\Models\table\Anjie_version;

class System extends Model
{
    protected $_anjie_login = null;
    protected $_api2_log = null;
    protected $_anjie_manaudit = null;
    protected $_t_address_province = null;
    protected $_t_address_city = null;
    protected $_t_address_town = null;
    protected $_anjie_origin = null;
    protected $_anjie_product = null;
    protected $_jcr_version = null;
    protected $_anjie_version = null;

    public function __construct()
    {
        parent::__construct();
        $this->_anjie_login = new Anjie_login();
        $this->_api2_log = new Api2_log();
        $this->_anjie_manaudit = new Anjie_manaudit();
        $this->_t_address_province = new T_address_province();
        $this->_t_address_city = new T_address_city();
        $this->_t_address_town = new T_address_town();
        $this->_anjie_origin = new Anjie_origin();
        $this->_anjie_product = new Anjie_product();
        $this->_tb_huankuan = new Tb_huankuan();
        $this->_tb_yuqi = new Tb_yuqi();
        $this->_jcr_version = new Jcr_version();
        $this->_anjie_version = new Anjie_version();
    }

    /**
     * 列出登录日志
     * @param page 页码
     * @param size 每一页的条数
     * @param condition 查询的条件
     * @return array
     */
    public function listloginlog($page, $size, $condition)
    {
        $limitout = '';
        if ($page && $size) {
            $limitout = " limit " . intval(($page - 1) * $size) . ', ' . intval($size);
        }
        $where = " where 1=1 ";
        if (isset($condition['end_time'])  && isset($condition['end_time']) && $condition['start_time'] !== '' && $condition['end_time'] !== '') {
            $where .= " and login_time >= " . $condition['start_time'] . " and login_time <= " . $condition['end_time'];
        }
        $where .= " and account like '%" . $condition['keyword'] . "%'";
        $order = " order by login_time desc";
        $r = $this->_anjie_login->getloginlog($limitout, $where, $order);
        return $r;
    }

    /**
     * 获得登录日志的条数
     * @param keyword 搜索时的关键词
     * @param condition 查询的条件
     * @return int
     */
    public function countloginlog($condition)
    {
        $where = " where 1=1 ";
        if (isset($condition['end_time'])  && isset($condition['end_time']) && $condition['start_time'] !== '' && $condition['end_time'] !== '') {
            $where .= " and login_time >= " . $condition['start_time'] . " and login_time <= " . $condition['end_time'];
        }
        $where .= " and account like '%" . $condition['keyword'] . "%'";
        $rs = $this->_anjie_login->getCount($where);
        return $rs['count'];
    }

    /**
     * 列出操作日志
     * @param page 页码
     * @param size 每一页的条数
     * @param condition 查询的条件
     * @return array
     */
    public function listactionlog($page, $size, $condition)
    {
        $limitout = '';
        if ($page && $size) {
            $limitout = " limit " . intval(($page - 1) * $size) . ', ' . intval($size);
        }
        $where = " where a.work_id != ''";
        if (isset($condition['end_time'])  && isset($condition['end_time']) && $condition['start_time'] !== '' && $condition['end_time'] !== '') {
            $where .= " and a.create_time >= " . $condition['start_time'] . " and a.create_time <= " . $condition['end_time'];
        }
        if ($condition['merchant_no'] !== '') {
            $where .= " and  a.merchant_no like '%" . $condition['merchant_no'] . "%' ";
        }
        if ($condition['customer_certificate_number'] !== '') {
            $where .= " and  a.customer_certificate_number like '%" . $condition['customer_certificate_number'] . "%' ";
        }
        $order = ' order by a.create_time desc';
        $r = $this->_api2_log->getactionlog($limitout, $order, $where);
        return $r;
    }

    /**
     * 获得操作日志的条数
     * @param keyword 搜索时的关键词
     * @param condition 查询的条件
     * @return int
     */
    public function countactionlog($condition)
    {
        $where = " where a.work_id != ''";
        if (isset($condition['end_time'])  && isset($condition['end_time']) && $condition['start_time'] !== '' && $condition['end_time'] !== '') {
            $where .= " and a.create_time >= " . $condition['start_time'] . " and a.create_time <= " . $condition['end_time'];
        }
        if ($condition['merchant_no'] !== '') {
            $where .= " and  a.merchant_no like '%" . $condition['merchant_no'] . "%' ";
        }
        if ($condition['customer_certificate_number'] !== '') {
            $where .= " and  a.customer_certificate_number like '%" . $condition['customer_certificate_number'] . "%' ";
        }
        $rs = $this->_api2_log->getCount($where);
        return $rs['count'];
    }

    /**
     * 列出人工审核的数据
     * @param page 页码
     * @param size 每一页的条数
     * @param condition 查询的条件
     * @return array
     */
    public function listmanaudit($page, $size, $condition)
    {
        $limitout = '';
        if ($page && $size) {
            $limitout = " limit " . intval(($page - 1) * $size) . ', ' . intval($size);
        }
        $where = " where 1=1 and status =1 ";
        if ($condition['type'] !== '') {
            $where .= " and audit_type = " . $condition['type'];
        }
        $order = " order by sort ";
        $rs = $this->_anjie_manaudit->getmanauditinfo($limitout, $where, $order);
        return $rs;
    }

    /**
     * 获得人工审核的条数
     * @param keyword 搜索时的关键词
     * @param condition 查询的条件
     * @return int
     */
    public function countmanaudit($condition)
    {
        $where = " where 1=1 and status =1 ";
        if ($condition['type'] !== '') {
            $where .= " and audit_type = " . $condition['type'];
        }
        $order = " order by sort ";
        $rs = $this->_anjie_manaudit->getCount($where, $order);
        return $rs['count'];
    }

    /**
     * 更新人工审核的信息
     * @param keyword 搜索时的关键词
     * @param condition 查询的条件
     * @return int
     */
    public function updatemanaudit($params)
    {
        $rs = $this->_anjie_manaudit->updatemanaudit($params);
        return $rs;
    }

//获取所有省的列表
    public function getprovince()
    {
        $rs = $this->_t_address_province->getAllprovince();
        return $rs;
    }

//通过传入省代码，获取市列表
    public function getcityBypcode($pcode)
    {
        $rs = $this->_t_address_city->getcityBypcode($pcode);
        return $rs;
    }

//通过传入市代码，获取区列表
    public function gettownBypcode($pcode)
    {
        $rs = $this->_t_address_town->gettownBypcode($pcode);
        return $rs;
    }
    //通过传入市名称，获取市代码
    public function getcitycodebyname($name)
    {
        $rs = $this->_t_address_city->getcodeByname($name);
        return $rs['code'];
    }
    //通过传入省名称，获取省代码
    public function getprovincecodebyname($name)
    {
        $rs = $this->_t_address_province->getcodeByname($name);
        return $rs['code'];
    }

    //获取所有城市
    public function getCity()
    {
        $rs = $this->_t_address_city->getAllCity();
        return $rs;
    }

//通过来源编号获取来源类型
    public function getmerchantclass($merchant_class_no)
    {
        $rs = $this->_anjie_origin->getInfoBymerchantclassno($merchant_class_no);
        return $rs;
    }

//获取所有产品名称
    public function getproductname()
    {
        $rs = $this->_anjie_product->getInfo();
        return $rs;
    }

//列出银行数据查询结果
    public function listbankdataquery($param)
    {
        $rs = array(
            'name' => '',
            'id_card' => '',
            'car_no' => '',
            'huankuan' => '',
            'yuqi_status' => '',
            'yuqi_days' => '',
            'yuqi_money' => '',
            'shouquan_money' => '',
        );
        $huankuandata = $this->_tb_huankuan->getinfobyidcardandname($param['id_card'], $param['name']);
        $yuqidata = $this->_tb_yuqi->getinfobyidcardandname($param['id_card'], $param['name']);
        if (!empty($huankuandata)) {
            $rs['name'] = $huankuandata['name'];
            $rs['id_card'] = $huankuandata['id_card'];
            $rs['car_no'] = $huankuandata['car_no'];
            $rs['huankuan'] = $huankuandata['huankuan'];
        }
        if (!empty($yuqidata)) {
            $rs['name'] = $yuqidata['name'];
            $rs['id_card'] = $yuqidata['id_card'];
            $rs['car_no'] = $yuqidata['car_no'];
            $rs['yuqi_status'] = $yuqidata['yuqi_status'];
            $rs['yuqi_days'] = $yuqidata['yuqi_days'];
            $rs['yuqi_money'] = $yuqidata['yuqi_money'];
            $rs['shouquan_money'] = $yuqidata['shouquan_money'];
        }
        return $rs;
    }
    //版本控制的接口
    public function versioncontrol($params)
    {
        $newversion = $this->_jcr_version->getnewversion($params['origin']);
        if (empty($newversion)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '暂无可用版本');                  //当前版本不存在
        }
        $versioninfo = $this->_jcr_version->getinfobyversion($params['current_version'], $params['origin']);
        if (empty($versioninfo)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '当前版本不存在');                  //当前版本不存在
        }
        $rs['newversion'] = $newversion;
        $rs['oldversion'] = $versioninfo;
        if ($params['current_version'] == $newversion['version'] && $params['origin'] == $newversion['origin']) {
            $rs['need_update'] = '2';         
        } else {
            $rs['need_update'] = '1';
        }
        $checkforceupdate = $this->_jcr_version->checkforceupdate($params['origin'], $versioninfo['sort']);
        if (empty($checkforceupdate)) {
            $rs['force_update'] = '2';
        } else {
            $rs['force_update'] = '1';
        }
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    //版本控制的接口
    public function anjieversion($params)
    {
        $newversion = $this->_anjie_version->getnewversion($params['origin']);
        if (empty($newversion)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '暂无可用版本');                  //当前版本不存在
        }
        $versioninfo = $this->_anjie_version->getinfobyversion($params['current_version'], $params['origin']);
        if (empty($versioninfo)) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '当前版本不存在');                  //当前版本不存在
        }
        $rs['newversion'] = $newversion;
        $rs['oldversion'] = $versioninfo;
        if ($params['current_version'] == $newversion['version'] && $params['origin'] == $newversion['origin']) {
            $rs['need_update'] = '2';         
        } else {
            $rs['need_update'] = '1';
        }
        $checkforceupdate = $this->_anjie_version->checkforceupdate($params['origin'], $versioninfo['sort']);
        if (empty($checkforceupdate)) {
            $rs['force_update'] = '2';
        } else {
            $rs['force_update'] = '1';
        }
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

}

