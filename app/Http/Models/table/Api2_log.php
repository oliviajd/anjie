<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Api2_log extends Model
{
    protected  $table='api2_log';
    public $primaryKey='id';
    public $timestamps=false;


    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    /**  
    *插入日志
    *@params  data      数据 
    */
    public function insertLog($data)
    {
        $sql = "select * from anjie_work where id = ?";
        $userinfo = $this->_pdo->fetchOne($sql, array($data['work_id']));
        $data['customer_certificate_number'] = empty($userinfo) ? '' : $userinfo['customer_certificate_number'];  //身份证
        $data['merchant_no'] = empty($userinfo) ? '' : $userinfo['merchant_no'];  //商户编号
        $sql = "insert into api2_log (uid, username, action, url, error_no, msg, script_time, create_time, work_id, customer_certificate_number, merchant_no, path, ip) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($data['uid'], $data['username'], $data['action'], $data['url'], $data['error_no'], $data['msg'], $data['script_time'], $data['create_time'], $data['work_id'], $data['customer_certificate_number'], $data['merchant_no'], $data['path'], $data['ip']));
        return $rs;
    }
    //获取操作日志
    public function getactionlog($limit, $order, $where)
    {
        $sql = "select a.customer_certificate_number, a.merchant_no, a.username, a.create_time, a.ip, a.path, b.requestname from api2_log as a left join anjie_privilege as b on a.path = b.path ". $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //获取操作日志的条数
    public function getCount($where)
    {
        $sql = "select count(1) as count from api2_log as a ". $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
}
