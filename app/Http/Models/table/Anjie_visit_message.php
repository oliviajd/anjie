<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_visit_message extends Model
{
    protected  $table='anjie_visit_message';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //插入消息
    public function insertmessage($visit_userids, $params)
    {
        $arr = array();
        $str = '';
        foreach ($visit_userids as $key=>$value) {
            $arr[] = $value;
            $arr[] = $params['task_instance_id'];
            $arr[] = $params['work_id'];
            $arr[] = 2;
            $arr[] = $params['type'];
            $arr[] = time();
            $arr[] = time();
            $str = $str."(?, ?, ?, ?, ?, ?, ?),";
        }
        $str = substr($str, 0, strlen($str)-1);
        $sql = "insert into anjie_visit_message (user_id, task_instance_id, work_id, has_read, type, create_time, modify_time) values ".$str;
        $rs = $this->_pdo->execute($sql, $arr);
        return $rs;
    }
    //补件时的插入信息
    public function setsupplementmessage($params)
    {
        $sql = "insert into anjie_visit_message (user_id, task_instance_id, work_id, has_read, type, create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($params['user_id'], $params['task_instance_id'], $params['work_id'],'2', $params['type'], time(), time()));
        return $rs;
    }
    //列出该用户的所有件
    public function listByuserid($user_id, $limit='')
    {
        $sql = "select * from anjie_visit_message where user_id = ? order by create_time desc" . $limit;
        $rs = $this->_pdo->fetchAll($sql, array($user_id));
        return $rs;
    }
    //把该用户的所有件都标为已读
    public function updateByuserid($user_id)
    {
        $sql = "update anjie_visit_message set has_read = 1, modify_time=? where user_id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $user_id));
        return $rs;
    }
}
