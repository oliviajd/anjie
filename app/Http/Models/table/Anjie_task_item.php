<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_task_item extends Model
{
    protected  $table='anjie_task_item';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //通过当前的item_id获得申请件查询的显示规则
    public function getShowByItemid($current_item_id)
    {
        $current_item_id = str_replace('|', '', $current_item_id);
        $arr = explode(',', $current_item_id);
        $curr = 0;
        foreach ($arr as $key => $value) {
            if ($value > $curr) {
                $curr = $value;
            }
        }
        $res = $this->getShowByItemid1($curr);
        return $res;
    }
    public function getShowByItemid1($current_item_id)
    {
        $sql = "select * from anjie_task_item where v1_item_id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($current_item_id));
        return $rs;
    }
    //通过当前的role_id获得分区规则
    public function getInfoByRoleid($role_id)
    {
        $sql = "select * from anjie_task_item where role_id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($role_id));
        return $rs;
    }

    public function getrolelist()
    {
        $sql = "select id, title, orderby, role_id from anjie_task_item where need_show = 1 order by orderby";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
}
