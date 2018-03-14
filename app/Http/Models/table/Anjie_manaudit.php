<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_manaudit extends Model
{
    protected  $table='anjie_manaudit';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    /*  
        获取人工审核表信息
        @params  account      账号
        @params  user_id      用户id
        @params  name         用户姓名
    */
    public function getmanauditinfo($limit, $where='', $order = '')
    {
        $sql = "select * from anjie_manaudit " . $where . $order. $limit  ;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getCount($where='', $order ='')
    {
        $sql = "select count(1) as count from anjie_manaudit " . $where . $order;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function updatemanaudit($params)
    {
        $sql = "update anjie_manaudit set first_authority = ?, second_authority =?, third_authority=?, fourth_authority=? where audit_type = ?";
        $rs = $this->_pdo->execute($sql, array($params['first_authority'], $params['second_authority'], $params['third_authority'], $params['fourth_authority'], $params['audit_type']));
        return $rs;
    }
}
