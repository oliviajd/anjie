<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\pdo;

class Credit_query_log extends Model
{
    protected  $table='credit_query';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new pdo();
    }

    //获取数据
    public function getlast()
    {
        $sql = "select * from credit_query_log";
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function updatelog($lastqueryid)
    {
        $sql = "update credit_query_log set lastqueryid = ? where `key` = 'last_id'";
        $rs = $this->_pdo->execute($sql, array($lastqueryid));
        return $rs;
    }
    
}
