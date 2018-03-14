<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;
use Illuminate\Support\Facades\Log;

class Anjie_users extends Model
{
    protected  $table='anjie_users';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    public function getdetail($where, $order, $limit)
    {
      if ($where !== '') {
        $where = $where . ' and is_valid =1';
      }
        $sql = "select * from anjie_users " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    public function getAppdetail($where, $order, $limit)
    {
        if ($where !== '') {
          $where = $where . ' and is_valid =1';
        }
        $sql = "select id, account, name, employee_number,visit_lng, visit_lat,visit_location,city, head_portrait from anjie_users " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //该件的用户的user_id,查询from_user_id等于他的这个件的to_user_id的这个人的信息
    public function getsubordinate($user_id, $work_id)
    {
        $sql = "select b.name, account from anjie_visit as a, anjie_users as b where a.from_user_id = ? and a.work_id = ? and a.to_user_id = b.id and b.is_valid =1";
        $rs = $this->_pdo->fetchOne($sql, array($user_id, $work_id));
        return $rs;
    }
    //通过账号获取用户信息
    public function getInfoByAccount($account)
    {
      $path = 'http://' . $_SERVER['HTTP_HOST'];
      $sql = "select * from anjie_users where account = ? and is_valid = 1";
      $rs = $this->_pdo->fetchOne($sql, array($account));
      if (!empty($rs) && $rs['head_portrait'] !=='') {
        if(strpos($rs['head_portrait'],'http') !== false || strpos($rs['head_portrait'],'https') !== false){
          $rs['head_portrait'] = $rs['head_portrait'];
        } else {
          $rs['head_portrait'] = $path . $rs['head_portrait'];
        }
      }
      return $rs;
    }
    //通过userid获取用户信息
    public function getInfoById($id)
    {
      $sql = "select * from anjie_users where id = ? and is_valid = 1";
      $rs = $this->_pdo->fetchOne($sql, array($id));
      return $rs;
    }
    //通过userid获取用户信息
    public function getAppinfoById($id)
    {
      $sql = "select id, account, province, city, town, area_add, name, head_portrait, employee_number, visit_lat, visit_lng,visit_location from anjie_users where id = ? and is_valid = 1";
      $rs = $this->_pdo->fetchOne($sql, array($id));
      return $rs;
    }
    /**
     * 添加用户
     * @param account          账号
     * @param name             姓名
     * @param province         省
     * @param city             城市
     * @param town             区
     * @param area_add         除了省和城市之外需要的区域
     * @param password         密码
     * @param password_confirm 确认密码
     */
    public function addUser($user)
    {
      $param = array(
        'account'       => isset($user['account']) ? $user['account'] : '',
        'business_area' => isset($user['area']) ? $user['area'] : '',
        'name'          => isset($user['name']) ? $user['name'] : '',
        'passwd'        => isset($user['password']) ? $user['password'] : '',
        'province'      => isset($user['province']) ? $user['province'] : '',
        'city'        => isset($user['city']) ? $user['city'] : '',
        'town'        => isset($user['town']) ? $user['town'] : '',
        'area_add'        => isset($user['area_add']) ? $user['area_add'] : '',
        'head_portrait' => isset($user['head_portrait']) ? $user['head_portrait'] : '',
        'type' => isset($user['type']) ? $user['type'] : '',
      );
      $sql = "insert into anjie_users(account, business_area, name, passwd, province, city, town, area_add, head_portrait, creat_time, modify_time, type) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $rs = $this->_pdo->execute($sql, array($param['account'], $param['business_area'], $param['name'], $param['passwd'], $param['province'], $param['city'], $param['town'], $param['area_add'], $param['head_portrait'], time(), time(), $param['type']));
      $result = $param;
      $result['id'] = $this->_pdo->lastInsertId();
      $result['employee_number'] = 'jc' . sprintf("%04d", $result['id']);
      $sql = "update anjie_users set employee_number = ? where id = ?";
      $rs = $this->_pdo->execute($sql, array($result['employee_number'], $result['id']));
      if ($rs !== false) {
        return $result;
      }
      return $rs;
    }
    /**
     * 编辑用户
     * @param account          账号
     * @param name             姓名
     * @param province         省
     * @param city             城市
     * @param town             区
     * @param area_add         除了省和城市之外需要的区域
     * @param password         密码
     * @param password_confirm 确认密码
     */
    public function editUser($user)
    {
      $param = array(
        'account'       => isset($user['account']) ? $user['account'] : '',
        'business_area' => isset($user['area']) ? $user['area'] : '',
        'province'      => isset($user['province']) ? $user['province'] : '',
        'city'        => isset($user['city']) ? $user['city'] : '',
        'town'        => isset($user['city']) ? $user['town'] : '',
        'area_add'        => isset($user['area_add']) ? $user['area_add'] : '',
        'name'          => isset($user['name']) ? $user['name'] : '',
        'oldaccount' => isset($user['oldaccount']) ? $user['oldaccount'] : '',
      );
      $sql = "update anjie_users set account = ?, business_area = ?, name = ?, province = ?, city = ?, town=?, area_add = ?, modify_time=? where account = ?";
      $rs = $this->_pdo->execute($sql, array($param['account'], $param['business_area'], $param['name'], $param['province'], $param['city'], $param['town'], $param['area_add'],time(), $param['oldaccount']));
      $result = $param;
      return $result;
    }
    //更新用户的状态，status=2为逻辑删除
    public function updateuserstatus($status, $user_id)
    {
      $sql = "update anjie_users set is_valid = ? where id = ?";
      $rs = $this->_pdo->execute($sql, array($status, $user_id));
      return $rs;
    }
//通过姓名模糊查询获取个人信息
    public function getInfoByName($name)
    {
      $sql = "select * from anjie_users where is_valid = 1 and name like '% ".$name." %'";
      $rs = $this->_pdo->fetchAll($sql, array($account));
      return $rs;
    }
//通过姓名或员工编号获取个人信息
    public function getDetailByUsername($loginname)
    {
      $sql = "select * from anjie_users where (name = ? or employee_number = ? or account = ?)  and is_valid = 1";
      $rs = $this->_pdo->fetchOne($sql, array($loginname, $loginname, $loginname));
      return $rs;
    }
//通过账号修改新密码
    public function changepassword($account, $newpassword)
    {
      $sql = "update anjie_users set passwd = ?, modify_time =? where account = ?";
      $rs = $this->_pdo->execute($sql, array($newpassword, time(), $account));
      return $rs;
    }
//通过条件获取用户列表
    public function getUserlist($order, $limit, $where='')
    {
      $where = ltrim($where, 'where ');
      if ($where !== '') {
        $where = ' and ' . $where;
      }
      $sql = "select * from anjie_users where is_valid = 1" . $where . $order . $limit;
      $rs = $this->_pdo->fetchAll($sql, array());
      return $rs;
    }
//通过条件获取用户列表
    public function getnoidentifylist($order, $limit, $where='')
    {
      $where = ltrim($where, 'where ');
      if ($where !== '') {
        $where = ' and ' . $where;
      }
      $sql = "select id, account, name, employee_number from anjie_users where is_valid = 1" . $where . $order . $limit;
      $rs = $this->_pdo->fetchAll($sql, array());
      return $rs;
    }
//通过条件获取用户数量
    public function getCount($where='')
    {
        $where = ltrim($where, 'where ');
        if ($where !== '') {
          $where = ' and ' . $where;
        }
        $sql = "select count(1) as count from anjie_users where is_valid = 1" . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
//写入最新的登录时间
    public function setlogintime($login_time, $account)
    {
      $sql = "update anjie_users set last_logintime = ?, modify_time=? where account = ?";
      $rs = $this->_pdo->execute($sql, array($login_time, time(), $account));
      if ($rs !== false) {
        return true;
      }
      return $rs;
    }
    /**
   * 设置家访员位置
   * @param lat 纬度
   * @param lng 经度
   * @param userid 用户ID
   * @param address 位置
   */
    public function setLocation($param)
    {
      $sql = "update anjie_users set visit_lat=?, visit_lng=?, visit_location=?, modify_time=? where id = ?";
      $rs = $this->_pdo->execute($sql, array($param['lat'], $param['lng'], $param['address'], time(), $param['userid']));
      return $rs;
    }
//写入头像字段
    public function setheadportrait($user_id, $head_portrait)
    {
      $path = 'http://' . $_SERVER['HTTP_HOST'];
      $head_portrait = str_replace($path, '',$head_portrait);
      $sql = "update anjie_users set head_portrait =?, modify_time=? where id = ?";
      $rs = $this->_pdo->execute($sql, array($head_portrait, time(), $user_id));
      return $rs;
    }
//写入地址字段
    public function setaddress($user_id, $param)
    {
      $business_area = $param['province'].$param['city'].$param['town'].$param['area_add'];
      $sql = "update anjie_users set province=?, city=?, town=?, area_add=?, business_area = ?, modify_time=? where id = ?";
      $rs = $this->_pdo->execute($sql, array($param['province'], $param['city'], $param['town'], $param['area_add'], $business_area, time(), $user_id));
      return $rs;
    }
//写入app端的角色字段
    public function addapprole($user_id, $param)
    {
      $sql = "update anjie_users set app_role =?, name =?, modify_time=? where id = ?";
      $rs = $this->_pdo->execute($sql, array($param['app_role'], $param['name'], time(), $user_id));
      return $rs;
    }
    
}
