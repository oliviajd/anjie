<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Jcr_users extends Model
{
    protected  $table='jcr_users';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
//添加聚车融账号
    public function addjcrusers($params)
    {
        $params['over_time'] = time() + 3600 * 24 * 60;
        $params['name'] = 'jcd' . $this->_common->generate_code(3);
    	$sql = "insert into jcr_users (token, loginname, `from`, device, user_id, realname, name, create_time, modify_time, over_time, verify_status) values (?,?, ?, ?, ?, ?, ?, ?, ?, ?, '2')";
    	$rs = $this->_pdo->execute($sql, array($params['token'], $params['loginname'], $params['from'], $params['device'], $params['user_id'], $params['realname'], $params['name'], time(), time(), $params['over_time']));
        return $rs;
    }

    public function updatejcrusers($params)
    {
        $params['over_time'] = time() + 3600 * 24 * 60;
        $sql = "update jcr_users set token = ?, `from` =?, device =?, user_id =?, realname=?, over_time=? where loginname =?";
        $rs = $this->_pdo->execute($sql, array($params['token'], $params['from'], $params['device'], $params['user_id'], $params['realname'], $params['over_time'], $params['loginname']));
        return $rs;
    }

    public function getjcruserinfo($loginname)
    {
        $sql = "select * from jcr_users where loginname = ?";
        $rs = $this->_pdo->fetchOne($sql, array($loginname));
        return $rs;
    }
    //通过token获取用户信息
    public function getuserinfobytoken($token)
    {
        $sql = "select * from jcr_users where token = ?";
        $rs = $this->_pdo->fetchOne($sql, array($token));
        return $rs;
    }

    public function setheadportrait($params)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $head_portrait = str_replace($path,'',$params['head_portrait']);
        $sql = "update jcr_users set head_portrait =?, modify_time=? where token = ?";
        $rs = $this->_pdo->execute($sql, array($head_portrait, time(), $params['token']));
        return $rs;
    }

    public function verfiyuser($user_id, $status)
    {
        $sql = "update jcr_users set verify_status = ? where user_id = ?";
        $rs = $this->_pdo->execute($sql, array($status, $user_id));
        return $rs;
    }
    // 开通银行存管账户
    public function baAccountOpen($params)
    {
        $sql = "update jcr_users set bankaccount_status = 1, accountId =?, acqRes =?, id_No =?, card_No=?, channel=? where user_id = ?";
        $rs = $this->_pdo->execute($sql, array($params['accountId'], $params['acqRes'], $params['id_No'], $params['card_No'], $params['channel'], $params['user_id']));
        return $rs;
    }
    //添加融资子账号id
    public function addfinanceid($finance_account_sub_id, $user_id)
    {
        $sql = "update jcr_users set finance_account_sub_id =? where user_id = ?";
        $rs = $this->_pdo->execute($sql, array($finance_account_sub_id, $user_id));
        return $rs;
    }
    //获取用户信息通过user_id
    public function getinfobyuserid($user_id)
    {
        $sql = "select * from jcr_users where user_id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($user_id));
        return $rs;
    }
    //更新姓名
    public function updatename($name, $user_id)
    {
        $sql = "update jcr_users set name = ? where user_id = ?";
        $rs = $this->_pdo->execute($sql, array($name, $user_id));
        return $rs;
    }
}
