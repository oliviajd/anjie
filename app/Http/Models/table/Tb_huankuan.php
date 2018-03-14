<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Bankpdo;

class Tb_huankuan extends Model
{
    protected  $table='tb_huankuan';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Bankpdo();
    }
//通过市代码获取所有的区信息
    public function getinfobyidcardandname($id_card, $name)
    {
        $sql = "select * from tb_huankuan where id_card =? and name = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id_card, $name));
        return $rs;
    }
     public function getinfobyidcard($id_card)
    {
        $sql = "select * from tb_huankuan where id_card =?";
        $rs = $this->_pdo->fetchOne($sql, array($id_card));
        return $rs;
    }
    //获取所有
    public function getDetail($where, $order, $limit)
    {
        $sql = "select *,tb_huankuan.id as huankuan_id, anjie_work.id as work_id from tb_huankuan left join anjie_work on anjie_work.customer_certificate_number=tb_huankuan.id_card " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getcount($where)
    {
        $sql = "select count(1) as count from tb_huankuan ". $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
}
