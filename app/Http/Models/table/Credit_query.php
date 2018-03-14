<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Zhaohuipdo;

class Credit_query extends Model
{
    protected  $table='credit_query';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Zhaohuipdo();
    }

    //获取数据
    public function getdata($where = '')
    {
        $sql = "select * from credit_query ". $where . ' order by id asc';
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    
}
