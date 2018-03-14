<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Jcr_bill extends Model
{
    protected  $table='jcr_bill';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //添加标的信息
    public function addbill($params)
    {
        $res = $this->getdetailbycsrid($params['id']);
        if (!$res){
            $sql = "insert into jcr_bill (csr_id, carbrand, cartype, money, billrequest_status, create_time, modify_time) values (?, ?, ?, ?, '1', ?, ?)";
            $rs = $this->_pdo->execute($sql, array($params['id'], $params['car_model'], $params['car_type'], $params['money'], time(), time()));
            $return = $params;
            $return['id'] = $this->_pdo->lastInsertId();
            if ($return) {
                return $return;
            }
            return false;
        }

        return true;
    }
    //通过条件查询
    public function getdetail($where, $order='', $limit='')
    {
        $sql = "select a.id,c.name,c.shopname,b.deadline,b.rate,b.money,b.car_stock,c.loginname, b.csr_no,c.area, a.create_time from `jcr_bill` as a, jcr_csr as b, jcr_verify as c   " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //通过条件查询
    public function getcount($where)
    {
        $sql = "select count(1) as count from `jcr_bill` as a, jcr_csr as b, jcr_verify as c   " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
    //根据车商融id获取所有标的信息
    public function getdetailbycsrid($csr_id)
    {
        $sql = "select * from jcr_bill where csr_id = ? and is_delete = 2";
        $rs = $this->_pdo->fetchAll($sql, array($csr_id));
        return $rs;
    }
    //根据车商融id获取所有标的信息
    public function getdetailbyfinancebillid($finance_bill_id)
    {
        $sql = "select * from jcr_bill where finance_bill_id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($finance_bill_id));
        return $rs;
    }
    //根据车商融id获取所有标的信息
    public function getloanbycsrid($csr_id, $loan)
    {
        $sql = "select * from jcr_bill where csr_id = ? and has_loan = ?";
        $rs = $this->_pdo->fetchAll($sql, array($csr_id, $loan));
        return $rs;
    }
    //添加聚车的finance_bill_id
    public function addfinancebillid($finance_bill_id, $bill_id)
    {
        $sql = "update jcr_bill set finance_bill_id =? where id = ?";
        $rs = $this->_pdo->execute($sql, array($finance_bill_id, $bill_id));
        return $rs;
    }
    //聚车金融标的状态推送
    public function updatebillnotify($sql)
    {
        $rs = $this->_pdo->execute($sql, array());
        return $rs;
    }
    public function getdetailtransfer($where, $order, $limit)
    {
        $sql = "select *,jcr_bill.id as bill_id from jcr_bill "  . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    public function getdecounttransfer($where)
    {
        $sql = "select count(1) as count from jcr_bill  ". $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
    public function getinfobyid($id)
    {
        $sql = "select * from jcr_bill where id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        return $rs;
    }

    public function deletenotbill($csr_id)
    {
        $sql = "update jcr_bill set is_delete = 1 where csr_id = ? and finance_bill_id IS null";
        $rs = $this->_pdo->execute($sql, array($csr_id));
        return $rs;
    }

    public function getUserBillByUserId($userId){
        $sql = "SELECT
                t.csr_id,
                t.bill_id,
                t.money,
                jcc.reg_date,
                jcc.mile,
                jcc.zone_name,
                jcc.model_name,
                jcc.series_name,
                jcc.brand_name,
                t.history_id
            FROM
                (
                    SELECT
                        jcr_bill.csr_id,
                        jcr_bill.money,
                        jcr_bill.id AS bill_id,
                        jcr_csr.history_id
                    FROM
                        jcr_bill
                    LEFT JOIN jcr_csr ON jcr_csr.id = jcr_bill.csr_id
                    WHERE
                        jcr_bill.has_repay = 2
                    AND jcr_bill.has_loan = 1
                    AND jcr_csr.user_id = ?
                ) AS t left join jcr_csr_car jcc on t.history_id = jcc.id";
        return $this->_pdo->fetchAll($sql,[$userId]);
    }
}
