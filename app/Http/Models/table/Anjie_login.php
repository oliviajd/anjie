<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_login extends Model
{
    protected  $table='anjie_login';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    /*  
        写入登录日志表
        @params  account      账号
        @params  user_id      用户id
        @params  name         用户姓名
    */
    public function setLoginLog($account, $user_id, $name)
    {
        $loginTime = time();
        $loginIp = $_SERVER["REMOTE_ADDR"];
        $sql = "insert into anjie_login (user_id, account, name, login_ip, login_time) values(?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($user_id, $account, $name, $loginIp, $loginTime));
        return $rs;
    }

    public function getlastlogin($account)
    {
        $sql = "select * from anjie_login where account = ? order by login_time desc  limit 1,1";
        $rs = $this->_pdo->fetchOne($sql, array($account));
        return $rs;
    }

    public function getloginlog($limit, $where='', $order='')
    {
        $sql = "select * from anjie_login " . $where . $order . $limit ;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getCount($where='')
    {
        $sql = "select count(1) as count from anjie_login " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
}
