<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Jcr_verify extends Model
{
    protected $table = 'jcr_verify';
    public $primaryKey = 'id';
    public $timestamps = false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    //添加认证信息
    public function jcrverify($params)
    {
        $sql = "insert into jcr_verify (user_id, name, sex, age, loginname, certificate_number, verify_type, shopname, area, address, business_license_number, company_name, verify_status,quota,create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '3', ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($params['user_id'], $params['name'], $params['sex'], $params['age'], $params['loginname'], $params['certificate_number'], $params['verify_type'], $params['shopname'], $params['area'], $params['address'], $params['business_license_number'], $params['company_name'], 50, time(), time()));
        $return = $params;
        $return['id'] = $this->_pdo->lastInsertId();
        if ($return) {
            return $return;
        }
        return false;
    }

    //通过条件查询
    public function getdetail($where, $order = '', $limit = '')
    {
        $sql = "select * from jcr_verify  " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    //通过条件查询
    public function getcount($where)
    {
        $sql = "select count(1) as count from jcr_verify  " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function getinfobyidandstatus($id, $status)
    {
        $sql = "select * from jcr_verify where id = ? and verify_status = ? and is_delete = 2";
        $rs = $this->_pdo->fetchOne($sql, array($id, $status));
        return $rs;
    }

    public function getginfobyuseridandstatus($user_id, $status)
    {
        $sql = "select * from jcr_verify where user_id = ? and verify_status = ? and is_delete = 2";
        $rs = $this->_pdo->fetchOne($sql, array($user_id, $status));
        return $rs;
    }

    public function getginfobyuserid($user_id)
    {
        $sql = "select * from jcr_verify where user_id = ? and is_delete = 2";
        $rs = $this->_pdo->fetchOne($sql, array($user_id));
        return $rs;
    }

    public function getinfobyid($id)
    {
        $sql = "select * from jcr_verify where id = ? and is_delete = 2";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }

    public function getinfobyid2($id)
    {
        $sql = "select * from jcr_verify where id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }

    //认证申请处理
    public function handleverify($params)
    {
        if ($params['verify_result'] == '1') {   //通过
            $sql = "update jcr_verify set verify_type =?, shopname =?, area=?, address=?, business_license_number=?, company_name=?, verify_status=?, modify_time=?, verify_time=?, quota=?  where id =? and is_delete = 2";
            $rs = $this->_pdo->execute($sql, array($params['verify_type'], $params['shopname'], $params['area'], $params['address'], $params['business_license_number'], $params['company_name'], $params['verify_result'],  time(), time(),$params['quota'], $params['verify_id']));
        } elseif ($params['verify_result'] == '4') {  //拒绝
            $sql = "update jcr_verify set verify_type =?, shopname =?, area=?, address=?, business_license_number=?, company_name=?, verify_status=?, modify_time=?, verify_time = ?, is_delete=1  where id =? and is_delete = 2";
            $rs = $this->_pdo->execute($sql, array($params['verify_type'], $params['shopname'], $params['area'], $params['address'], $params['business_license_number'], $params['company_name'], $params['verify_result'], time(), time(), $params['verify_id']));

        }
        if ($rs !== false) {
            return true;
        } else {
            return false;
        }
    }

    //暂存
    public function storageverify($params)
    {
        $sql = "update jcr_verify set verify_status = ? where id = ? and is_delete = 2";
        $rs = $this->_pdo->execute($sql, array($params['verify_status'], $params['verify_id']));
        return $rs;

    }

    //删除某条认证信息
    public function deleteverify($params)
    {
        $sql = "update jcr_verify set is_delete = 1 where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['verify_id']));
        return $rs;
    }

    //添加银行卡号
    public function addcardno($params)
    {
        $sql = "update jcr_verify set card_No = ? where user_id = ? and is_delete = 2";
        $rs = $this->_pdo->execute($sql, array($params['card_No'], $params['user_id']));
        return $rs;

    }

    //评分信息
    public function addscore($params)
    {
        $sql = "update jcr_verify set foundtime = ?, main_business =?, credit_score=?, manage_score=?, assets_score=?, debt_score=?, conducive_score=?, has_score =? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['foundtime'], $params['main_business'], $params['credit_score'], $params['manage_score'], $params['assets_score'], $params['debt_score'], $params['conducive_score'], '1', $params['verify_id']));
        return $rs;
    }

    public function decrementField($where, $field, $fieldValue){
        DB::table('jcr_verify')->where($where)->decrement($field, $fieldValue);
    }

    public function incrementField($where, $field, $fieldValue){
        DB::table('jcr_verify')->where($where)->increment($field, $fieldValue);
    }

    public static function updateByUserId($user_id, $params)
    {
        self::where('user_id', $user_id)->update($params);

        return true;
    }
}
