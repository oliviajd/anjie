<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Bankpdo;

class Tb_yuqi extends Model
{
    protected  $table='tb_yuqi';
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
        $sql = "select * from tb_yuqi where id_card =? and name = ?";
        $rs = $this->_pdo->fetchOne($sql, array($id_card, $name));
        return $rs;
    }
    //获取所有
    public function getDetail($where, $order, $limit)
    {
        $sql = "select *,tb_yuqi.id as yuqi_id, anjie_work.id as work_id from tb_yuqi left join anjie_work on anjie_work.customer_certificate_number=tb_yuqi.id_card " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getcount($where)
    {
        $sql = "select count(1) as count from tb_yuqi ". $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

}
