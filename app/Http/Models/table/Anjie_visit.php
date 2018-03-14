<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_visit extends Model
{
    protected  $table='anjie_visit';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    /*  
        通过file_token获取信息
        @params  file_token      文件token 
    */
    public function getDetail($where, $order='', $limit='')
    {
        if ($where !== '') {
            $where = $where . ' and anjie_visit.is_valid = 1'; 
        }
        $sql = "select * from anjie_visit " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    public function adduservisit($user_id, $visit_works)
    {
        $arr = array();
        $str = '';
        foreach ($visit_works as $key=>$value) {
            $arr[] = $value['work_id'];
            $arr[] = $value['visit_task_instance_id'];
            $arr[] = $user_id;
            $arr[] = $value['from_user_id'];
            $arr[] = $value['has_assign'];
            $arr[] = $value['has_pickup'];
            $arr[] = $value['supplement_status'];
            $arr[] = $value['visit_status'];
            $arr[] = $value['product_class_number'];
            $arr[] = $value['credit_city'];
            $arr[] = $value['type'];
            $arr[] = $value['create_time'];
            $arr[] = $value['modify_time'];
            $arr[] = $value['visit_date'];
            $arr[] = $value['pick_up_userid'];
            $arr[] = $value['pick_up_time'];
            $arr[] = $value['supplement_task_instance_id'];
            $str = $str."(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),";
        }
        $str = substr($str, 0, strlen($str)-1);
        $sql = "insert into anjie_visit (work_id, visit_task_instance_id, to_user_id, from_user_id, has_assign, has_pickup, supplement_status, visit_status, product_class_number, credit_city, type, create_time, modify_time, visit_date, pick_up_userid, pick_up_time, supplement_task_instance_id) values ".$str;
        $rs = $this->_pdo->execute($sql, $arr);
        return $rs;

    }
    //工作流中给visit表中插入待家访数据
    public function workflowvisit($params, $visit_userids)
    {
        $arr = array();
        $str = '';
        foreach ($visit_userids as $key=>$value) {
            $arr[] = $params['work_id'];
            $arr[] = $params['task_instance_id'];
            $arr[] = $value;
            $arr[] = 0;
            $arr[] = 2;  //默认2未分配
            $arr[] = 2;  //默认2未认领
            $arr[] = 1;  //补件的状态，1不需要补件，2需要补件，3已补件
            $arr[] = 2;  //家访状态 2未家访，3已家访
            $arr[] = $params['product_class_number'];    //产品类别编号,XC:新车，ES:二手车
            $arr[] = $params['credit_city'];
            $arr[] = $params['type'];    //类型，1为家访，2为补件
            $arr[] = time();
            $arr[] = time();
            $str = $str."(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),";
        }
        $str = substr($str, 0, strlen($str)-1);
        $sql = "insert into anjie_visit (work_id, visit_task_instance_id, to_user_id, from_user_id, has_assign, has_pickup, supplement_status, visit_status, product_class_number, credit_city, type, create_time, modify_time) values ".$str;
        $rs = $this->_pdo->execute($sql, $arr);
        return $rs;
    }
    //工作流修改所有的件为待补件
    public function workflowsupplement($params)
    {
      $sql = "update anjie_visit set supplement_task_instance_id = ?, supplement_status = ?, type = ?, modify_time = ? where work_id = ? and has_pickup =1 and has_assign =2";
      $rs = $this->_pdo->execute($sql, array($params['task_instance_id'], '2', $params['type'], time(), $params['work_id']));
      return $rs;
    }
    //获取家访列表
    public function getVisitlist($order, $limit, $where='')
    {
        $sql = "select * from anjie_visit " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //获取该条件下的件总数
    public function getCount($where='')
    {
        if ($where !== '') {
            $where = $where . ' and anjie_visit.is_valid = 1'; 
        }
        $sql = "select count(1) as count from anjie_visit " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }
    //获取日期区间内的件数列表
    public function getaccountlist($group, $order, $where)
    {
        if ($where !== '') {
            $where = $where . ' and anjie_visit.is_valid = 1'; 
        }
        $sql = 'select visit_date as date, count(*) as account from anjie_visit ' . $where . $group . $order;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //家访中认领任务
    public function pickupvisit($user_id, $param)
    {
        $sql = "select * from anjie_visit where work_id =? and to_user_id = ? and is_valid = 1";
        $check = $this->_pdo->fetchOne($sql, array($param['work_id'], $user_id));
        if (empty($check)) {
            return false;
        }
        $sql = "update anjie_visit set has_pickup = 1, pick_up_userid =?,  visit_date = ?, modify_time=?, pick_up_time=? where work_id = ?";
        $rs = $this->_pdo->execute($sql, array($user_id, $param['visit_date'], time(), time(),  $param['work_id']));
        if($rs !== false) {
            $sql = "select work_id, visit_task_instance_id from anjie_visit where to_user_id = ? and work_id =? and is_valid = 1";
            $rs = $this->_pdo->fetchOne($sql, array($user_id, $param['work_id']));
            return $rs;
        }
        return $rs;
    }
    //根据work_id获得所有
    public function getInfoByworkid($work_id)
    {
        $sql = "select * from anjie_visit where work_id= ? and is_valid = 1";
        $rs = $this->_pdo->fetchAll($sql, array($work_id));
        return $rs;
    }   
    //验证该任务是否已经被领取
    public function haspickup($param)
    {
        $sql = "select * from anjie_visit where work_id = ? and is_valid = 1";
        $rs = $this->_pdo->fetchOne($sql, array($param['work_id']));
        if (!empty($rs) && $rs['has_pickup'] == '1') {
            return true;
        }
        return false;
    }
    //验证家访记录是否存在
    public function checkvisit($user_id, $param)
    {
        $sql = "select * from anjie_visit where work_id =? and to_user_id =? and has_pickup =1 and has_assign=2 and is_valid = 1";
        $rs = $this->_pdo->fetchOne($sql, array($param['work_id'], $user_id));
        return $rs;
    }
    public function checkpickup($user_id, $param)
    {
        $sql = "select * from anjie_visit where to_user_id =? and has_pickup = 1 and has_assign =2 and visit_status =2 and is_valid =1 and pick_up_userid = ? and work_id =?";
        $rs = $this->_pdo->fetchOne($sql, array($user_id, $user_id, $param['work_id']));
        return $rs;
    }
    // .to_user_id = ". $user_id . " and a.has_pickup=1 and a.has_assign=2 and a.visit_status=2 and a.is_valid =1 and pick_up_userid = ". $user_id
    //验证是否可以被分派
    public function checkassignvisit($user_id, $param)
    {
        $sql = "select * from anjie_visit where work_id = ? and to_user_id = ? and has_assign =2 and has_pickup = 2 and is_valid = 1";
        $rs = $this->_pdo->fetchOne($sql, array($param['work_id'], $user_id));
        return $rs;
    }
    //验证是否是待补件
    public function checksupplement($user_id, $param)
    {
        $sql = "select * from anjie_visit where work_id = ? and to_user_id = ? and supplement_status=2 and is_valid = 1";
        $rs = $this->_pdo->fetchOne($sql, array($param['work_id'], $user_id));
        return $rs;
    }
    //家访中的回退任务
    public function backvisit($user_id, $param)
    {
        $check = $this->getinfoByuseridAndWorkid($param['to_user_id'], $param['work_id']);
        if (!empty($check) && $check['from_user_id'] == '0') {
            $sql = "update anjie_visit set has_pickup =2, has_assign = 2, modify_time=?  where work_id =?";
            $rs = $this->_pdo->execute($sql, array(time(), $param['work_id']));
        } else {
            $sql = "update anjie_visit set has_pickup =2, has_assign = 2, modify_time=?  where to_user_id = ? and work_id =?";
            $rs = $this->_pdo->execute($sql, array(time(), $param['to_user_id'], $param['work_id']));
        }        
        if ($rs !== false) {
            $sql = "delete from anjie_visit where to_user_id = ? and work_id = ?";
            $rs = $this->_pdo->execute($sql, array($user_id, $param['work_id']));
        }
        return $rs;
    }
    //家访中的分派任务
    public function assignvisit($user_id, $param)
    {
        //获取分派时的初始数据
        $check = $this->checkassignvisit($user_id, $param);
        if (empty($check)) {   //如果不是他的件则不能分派
          return false;
        }
        $sql = "update anjie_visit set has_assign = 1, modify_time=? where work_id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $param['work_id']));
        if ($rs == false) {
            return false;
        }
        $sql = "insert into anjie_visit (work_id, visit_task_instance_id, to_user_id, from_user_id, has_assign, has_pickup, supplement_status, visit_status, product_class_number, type, create_time, modify_time, credit_city) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
        $rs = $this->_pdo->execute($sql, array($param['work_id'], $check['visit_task_instance_id'], $param['subordinate_userid'], $user_id, '2', '2', '1', '2', $check['product_class_number'], $check['type'], time(), time(), $param['credit_city']));
        if ($rs == false) {
            return false;
        }
        return true;
    }
    //家访中的拒件任务
    public function refusevisit($user_id, $param)
    {
        $sql = "update anjie_visit set visit_status = 4, modify_time=? where work_id =?";   //拒件的时候把该件的所有记录都置为拒件
        $rs = $this->_pdo->execute($sql, array(time(), $param['work_id']));
        if ($rs == false) {
            return false;
        }
        return true;
    }
    //完成补件
    public function completesupplement($user_id, $param)
    {
        $sql = "update anjie_visit set supplement_status =3, modify_time=? where work_id =?";
        $rs = $this->_pdo->execute($sql, array(time(), $param['work_id']));
        if ($rs == false) {
            return false;
        }
        return true;
    }
    //完成补件
    public function completevisit($user_id, $param)
    {
        $sql = "update anjie_visit set visit_status =3, modify_time=? where work_id =?";
        $rs = $this->_pdo->execute($sql, array(time(), $param['work_id']));
        if ($rs == false) {
            return false;
        }
        return true;
    }

    public function getinfoByuseridAndWorkid($user_id, $work_id)
    {
        $sql = "select * from anjie_visit where to_user_id = ? and work_id = ? and is_valid = 1";
        $rs = $this->_pdo->fetchOne($sql, array($user_id, $work_id));
        return $rs;
    }

    public function deleteByuserid($params)
    {
        $sql ="update anjie_visit set is_valid = 2, modify_time = ? where to_user_id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $params['user_id']));
        return $rs;
    }

    public function visitgiveup($user_id, $params)
    {
        $sql = "update anjie_visit set has_pickup =2, pick_up_userid = ?, modify_time =? where work_id =?";
        $rs = $this->_pdo->execute($sql, array('', time(), $params['work_id']));
        $delete=true;
        $cancelassign=true;
        $checkfrompickup = $this->getinfoByuseridAndWorkid($params['from_user_id'], $params['work_id']);
        if (empty($checkfrompickup)) {
            return false;
        }
        if ($params['highest_charge'] =='2' && $params['haschild'] = '2') {     //家访专员
          //如果是家访专员   to_user_id = user_id的记录is_valid=2 ，所有记录has_pickup=2 and pickup_user_id=''  .. to_user_id= from_user_id的记录has_assign=2  
          $sql = "update anjie_visit set is_valid = 2 where to_user_id = ? and work_id = ?";
          $delete = $this->_pdo->execute($sql, array($user_id, $params['work_id']));
        }
        if ($checkfrompickup['from_user_id'] == '0') {
            $sql = "update anjie_visit set has_assign = 2 where work_id = ?";
            $cancelassign = $this->_pdo->execute($sql, array($params['work_id']));
        } else {
            $sql = "update anjie_visit set has_assign = 2 where to_user_id = ? and work_id = ?";
            $cancelassign = $this->_pdo->execute($sql, array($params['from_user_id'], $params['work_id']));
        }
        if (($rs==false) || ($delete == false) || ($cancelassign ==false)) {
            return false;
        } else {
            return true;
        }

    }

}