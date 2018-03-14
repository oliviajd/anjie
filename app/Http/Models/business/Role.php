<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Auth;
use App\Http\Models\table\Anjie_users;
use App\Http\Models\table\Anjie_role;
use App\Http\Models\table\V1_user_role;
use App\Http\Models\table\V1_role;
use App\Http\Models\table\V1_role_privilege;
use App\Http\Models\table\Anjie_category;
use App\Http\Models\table\Anjie_method;
use App\Http\Models\table\T_address_province;
use App\Http\Models\table\T_address_city;
use App\Http\Models\table\Anjie_privilege;
use App\Http\Models\table\Anjie_visit;
use App\Http\Models\table\Anjie_user_role_area_privilege;
use App\Http\Models\business\Workflow;

class Role extends Model
{
  protected $_anjie_users = null;
  protected $_anjie_role = null;
  protected $_v1_user_role = null;
  protected $_v1_role = null;
  protected $_v1_role_privilege = null;
  protected $_anjie_category = null;
  protected $_anjie_method = null;
  protected $_t_address_province = null;
  protected $_t_address_city = null;
  protected $_anjie_privilege = null;
  protected $_anjie_visit = null;
  protected $_app_role = array(
    '76' => array(
      'role_type' => '1',
      'role_name' => '家访组',
      'role_id' => '76',
    ),
    '74' => array(
      'role_type' => '2',
      'role_name' => '销售组',
      'role_id' => '74',
    ),
  );

  public function __construct()
  {
    parent::__construct();
    $this->_anjie_role = new Anjie_role();
    $this->_anjie_users = new Anjie_users();
    $this->_v1_user_role = new V1_user_role();
    $this->_v1_role = new V1_role();
    $this->_v1_role_privilege = new V1_role_privilege();
    $this->_anjie_category = new Anjie_category();
    $this->_anjie_method = new Anjie_method();
    $this->_t_address_province = new T_address_province();
    $this->_t_address_city = new T_address_city();
    $this->_anjie_privilege = new Anjie_privilege();
    $this->_anjie_visit = new Anjie_visit();
    $this->_auth = new Auth();
    $this->_anjie_user_role_area_privilege = new Anjie_user_role_area_privilege();

  }
//添加用户的逻辑
  public function addUser($user, $user_id)
  {
    $userinfo = $this->_anjie_users->getInfoById($user_id);
    if (!empty($userinfo)) {
      if ($userinfo['type'] !== '1') {
        $user['type'] = $userinfo['type'];
      }
    }
    $this->_common->setlog();
    //判断用户是否存在
    $ifisset = $this->_anjie_users->getInfoByAccount($user['account']);     
    if(!$ifisset) {
      //如果用户不存在则新增用户
      $adduser = $this->_anjie_users->addUser($user);
      return $this->_common->output($adduser, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      //如果用户已存在，返回错误
      return $this->_common->output('', Constant::ERR_ACCOUNT_EXISTS_NO, Constant::ERR_ACCOUNT_EXISTS_MSG);
    }
  }
//编辑用户的逻辑
  public function editUser($user)
  {
    $this->_common->setlog();
    //判断用户是否存在
    $ifisset = $this->_anjie_users->getInfoByAccount($user['oldaccount']);
    if($ifisset) {
      $ifissetnewaccount = $this->_anjie_users->getInfoByAccount($user['account']); 
      if (!empty($ifissetnewaccount) && $ifissetnewaccount['account'] !== $user['oldaccount']) {
        return $this->_common->output(false, Constant::ERR_ACCOUNT_NOT_EXISTS_NO, '该账号已存在');
      }
      //如果用户存在则修改用户信息
      $edituser = $this->_anjie_users->editUser($user);
      return $this->_common->output($edituser, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    } else {
      //如果用户不存在，返回错误
      return $this->_common->output($edituser, Constant::ERR_ACCOUNT_NOT_EXISTS_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_MSG);
    }
  }
  public function deleteuserpost($param)
  {
    $this->_common->setlog();
    $userinfo = $this->_anjie_users->getInfoById($param['user_id']);
    if (empty($userinfo)) {
      //如果用户不存在，返回错误
      return $this->_common->output(false, Constant::ERR_ACCOUNT_NOT_EXISTS_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_MSG);
    } elseif($userinfo['account'] !== $param['account']){
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '账号和用户id不一致');
    } 
    $deleteuser = $this->_anjie_users->updateuserstatus('2', $param['user_id']);
    if ($deleteuser == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '删除用户失败');
    }
    $deleteuser_role = $this->_v1_user_role->deleteByUserid($param['user_id']);
    return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
  }
//获取省代码
  public function getprovincecode($provincename)
  {
    $rs = $this->_t_address_province->getcodeByname($provincename);
    return $rs;
  }
//获取市代码
  public function getcitycode($cityname)
  {
    $rs = $this->_t_address_city->getcodeByname($cityname);
    return $rs;
  }

  public function lists_user($condition, $page = false, $size = false, $order = false) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      $where = $this->_condition_user($condition);
      $r = $this->_v1_user_role->getuUserrole($where, $orderout, $limitout);
      $rows = array();
      foreach ($r as $k => $v) {
          $rows[] = new obj_role_user(array(
              'role' => $this->detailrole($v['role_id']),
              'user' => $this->detailuser($v['user_id']),
              'create_time' => $v['create_time'],
          ));
      }
      return $rows;
  }

  public function lists_user_all($page, $size, $order, $keyword='', $condition='', $user_id='')
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = " order by ". $order;
      }
      
      if ($condition == '0') {
        $where = "where name like '%" . $keyword . "%'";
      } elseif ($condition == '1') {
        $where = "where business_area like '%" . $keyword . "%'";
      } elseif ($condition == '2') {
        $where = "where account like '%" . $keyword . "%'";
      } else {
        $where = ' where 1=1';
      }
      $userinfo = $this->_anjie_users->getInfoById($user_id);
      if (!empty($userinfo)) {
        if ($userinfo['type'] !== '1') {
          $where = $where . " and type = " . $userinfo['type'];
        }
      }
      $r = $this->_anjie_users->getUserlist($orderout, $limitout, $where);
      return $r;
  }

  public function count_user_all($keyword='', $condition='', $user_id)
  {
      if ($condition == '0') {   //按姓名查询
        $where = "where name like '%" . $keyword . "%'";
      } elseif ($condition == '1') {  //按业务区域查询
        $where = "where business_area like '%" . $keyword . "%'";
      } elseif ($condition == '2') {  //按账号查询
        $where = "where account like '%" . $keyword . "%'";
      } else {
        $where = ' where 1=1';
      }
      $userinfo = $this->_anjie_users->getInfoById($user_id);
      if (!empty($userinfo)) {
        if ($userinfo['type'] !== '1') {
          $where = $where . " and type = " . $userinfo['type'];
        }
      }
      $rs = $this->_anjie_users->getCount($where);
      return $rs['count'];
  }
//列出所有没有身份的人
  public function list_no_identify($page, $size, $order)
  { 
    $limitout = '';
    $orderout = '';
    if ($page && $size) {
      $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
    }
    if ($order) {
      $orderout = " order by ". $order;
    }
    //先列出所有有身份的人的user_id
    $identified = $this->_v1_user_role->getidentifiedid();
    $identified = array_column($identified, 'user_id');
    $identifiedarr = "'".implode("','", $identified)."'";
    //查询id不在这些有身份的userid里面的人的信息
    $where = " where id not in(".$identifiedarr.")";
    $r = $this->_anjie_users->getnoidentifylist($orderout, $limitout, $where);
    return $r;
  }
//计算没有身份的人的数量
  public function count_no_identify()
  {
    //先列出所有有身份的人的user_id
    $identified = $this->_v1_user_role->getidentifiedid();
    $identified = array_column($identified, 'user_id');
    $identifiedarr = "'".implode("','", $identified)."'";
    //查询id不在这些有身份的userid里面的人的信息
    $where = " where id not in(".$identifiedarr.")";
    $r = $this->_anjie_users->getCount($where);
    return $r['count'];
  }
//列出该用户的所有下属
  public function listsubordinate($condition, $page, $size, $order)
  {
    $userinfo = $this->_anjie_users->getInfoById($condition['userid']);
    if (isset($condition['visit_lat']) && isset($condition['visit_lng'])  && $condition['visit_lat'] !== '' && $condition['visit_lng'] !== '') {     //如果前端传了经纬度，则用前端所传的经纬度
      $userinfo['visit_lat'] = $condition['visit_lat'];
      $userinfo['visit_lng'] = $condition['visit_lng'];
    }
    $limitout = '';
    $orderout = '';
    if ($page && $size) {
      $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
    }
    if ($order) {
      $orderout = " order by ". $order;
    }
    $arr = $this->listsubordinaterole($condition['userid'], env('VISIT_ROLE_ID')); //获取下级的角色
    //定义该角色下所有下级角色对应的用户的数组
    $subordinatelist = array();
    //通过上面获得的角色id数组获取对应的所有用户id
    foreach ($arr as $key => $value) {
      $userlist = $this->_v1_user_role->getInfoByroleid($key);
      foreach ($userlist as $k => $v) {
        $userlist[$k]['rolename'] = $value['title'];
      }
      $subordinatelist = array_merge($subordinatelist, $userlist);   //得到所有的下级角色对应的user_id的数组
    }
    //这个out其实是为了后面根据userid找到他的角色
    $out = array();
    foreach ($subordinatelist as $key => $value) {
      if (isset($out[$value['user_id']]) && $value['role_id'] > $out[$value['user_id']]['role_id']) {    //这个比较是为了返回的是role_id最小的角色，也就是最上级的角色
      } else {
        $out[$value['user_id']] = $value;
      }
    }
    //根据userid来实现分页的情况，获取app需要的信息
    $userids = array_column($subordinatelist, 'user_id');
    $idarr = "'".implode("','", $userids)."'";
    $citys = $this->_anjie_user_role_area_privilege->getCityLists($condition['userid'],'98');
    if (!empty($citys)) {
        $where = " where id in(".$idarr.") and city in ('" . implode('\',\'',array_column($citys,'city_name')) . "')" ;
    } else {
        $where = " where id in(".$idarr.") and city = '" . $userinfo['city'] . "'" ;
    }
    $rs = $this->_anjie_users->getAppdetail($where, $orderout, $limitout);
    foreach ($rs as $key => $value) {
      //把该用户的角色名放进去
      $rs[$key]['rolename'] = $out[$value['id']]['rolename'];
      $where = " where to_user_id = ". $value['id'] . ' and has_pickup=1 and has_assign=2 and visit_status=2 and supplement_status=1';
      $taskcount = $this->_anjie_visit->getCount($where);
      $rs[$key]['taskcount'] = $taskcount['count'];
      $rs[$key]['distance'] = $this->getDistance($userinfo['visit_lat'], $userinfo['visit_lng'], $value['visit_lat'], $value['visit_lng']);
    }
    if (isset($condition['taskorder']) && $condition['taskorder'] == '1') {
      array_multisort(array_column($rs,'taskcount'),SORT_DESC,$rs);                //任务量多的排前面
    } elseif(isset($condition['taskorder']) &&$condition['taskorder'] == '2') {
      array_multisort(array_column($rs,'taskcount'),SORT_ASC,$rs);                 //任务量少的排前面
    }
    if (isset($condition['distanceorder']) && $condition['distanceorder'] == '1') {
      $no_distance=array();
      foreach ($rs as $key => $value) {
        if ($value['distance'] == false) {
          $no_distance[] = $rs[$key];
          unset($rs[$key]);
        }
      }
      array_multisort(array_column($rs,'distance'),SORT_ASC,$rs);                   //     距离近的排前面
      $rs = array_merge($rs, $no_distance);
    } elseif(isset($condition['distanceorder']) &&$condition['distanceorder'] == '2') {
      $no_distance=array();
      foreach ($rs as $key => $value) {
        if ($value['distance'] == false) {
          $no_distance[] = $rs[$key];
          unset($rs[$key]);
        }
      }
      array_multisort(array_column($rs,'distance'),SORT_DESC,$rs);                  //     距离远的排前面
      $rs = array_merge($rs, $no_distance);
    }
    return $rs;
  }
  /** 
  * @desc 根据两点间的经纬度计算距离 
  * @param float $lat 纬度值 
  * @param float $lng 经度值 
  */
  function getDistance($lat1, $lng1, $lat2, $lng2) 
  { 
    if ($lat1 == '' || $lng1 == '' || $lat2 == '' || $lng2 == '' || $lat1 == null || $lng1 == null || $lat2 ==null || $lng2 == null) {
      return false;
    }
    $earthRadius = 6367000;      
    $lat1 = ($lat1 * pi() ) / 180; 
    $lng1 = ($lng1 * pi() ) / 180;     
    $lat2 = ($lat2 * pi() ) / 180; 
    $lng2 = ($lng2 * pi() ) / 180;  
    $calcLongitude = $lng2 - $lng1; 
    $calcLatitude = $lat2 - $lat1; 
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2); 
    $stepTwo = 2 * asin(min(1, sqrt($stepOne))); 
    $calculatedDistance = $earthRadius * $stepTwo;    
    return round($calculatedDistance); 
  } 

//计算该用户的所有下属的数量
  public function count_subordinate($condition)
  {
    $rs = $this->listsubordinate($condition, '', '', '');   //不做页面的筛选的时候就是总数
    $count = count($rs);
    return $count;
  }
//获取下属详情
  public function getsubordinateinfo($user_id, $subordinate_id)
  {
    $condition['userid'] = $user_id;
    $listsubordinate = $this->listsubordinate($condition, '', '', '');   //列出当前用户的所有下属列表
    $issubordinate = false;   //默认传入的userid不是当前用户的下属，具体是不是看下面的判断
    foreach ($listsubordinate as $key => $value) {
      if($value['id'] == $subordinate_id) {
        $issubordinate = true;   //如果该userid在下属列表中，则是下属
      }
    }
    if ($issubordinate) {
      $rs = $this->_anjie_users->getAppinfoById($subordinate_id);   //通过下属的用户id获取app里面的下属的个人详情
      $where = " where user_id = " . $subordinate_id;
      $role = $this->_v1_user_role->getuUserrole($where);
      if (empty($role)) {
        return false;
      }
      $roleid = $role[0]['role_id'];
      $rolename = $this->_v1_role->getDetail($roleid);
      $rs['rolename'] = $rolename['title'];
      return $rs;
    } else {
      return $issubordinate;    //如果不是他的下属则直接返回false
    }
  }
//获取所有的下级角色
  public function listsubordinaterole($user_id, $role_id ='76')
  {
    //取所有role角色
    $allrole = $this->_v1_role->getrole('','','');
    //根据user_id获得该用户所在的角色的下属角色id
    $user_role = $this->_v1_user_role->getRoleidByuserid($user_id);
    $subordinate = array();
    //获取该角色的所有的下属角色
    foreach ($user_role as $key => $value) {
      $userRoleList = $this->_v1_role->test($value,$allrole,1);
      $subordinate = $userRoleList + $subordinate;
    }
    //获取家访即role_id=14的角色下面的所有下级角色
    $visitlist = $this->_v1_role->test($role_id, $allrole,1);
    if($visitlist == null) {
      $visitlist = array();
    }
    //取家访下级角色和该用户所有下级角色的交集，获得家访中的所有下级角色
    $arr = array_intersect_key($visitlist, $subordinate);    //得到所有的角色id 
    return $arr;
  }
//新增下属
  public function addsubordinate($user_id, $condition)
  {
    $subordinaterole = $this->listsubordinaterole($user_id, env('VISIT_ROLE_ID'));
    $issubordinaterole = false;         //传过来的角色id是否是该用户可分配的下级角色，默认false
    foreach ($subordinaterole as $key => $value) {
      if($value['id'] == $condition['role_id']) {
        $issubordinaterole = true;   //如果该角色id在下级可分配的角色列表中，则可以往下走
      }
    }
    if ($issubordinaterole == false) {
      return false;   //传过来的角色id不是该用户可分配的下级角色，则返回false
    }
    $role = $this->_v1_user_role->getRoleidByuserid($condition['subordinate_id']);   //根据需要被分配的userid取该用户的角色id，如果不为空，则说明该用户已有角色，则该用户不可以再被分配
    if (!empty($role)) {
      return false;
    }
    //添加用户角色对应关系
    $param = array(
      'user_id' => $condition['subordinate_id'],  //下属的用户id
      'role_id' => $condition['role_id'],         //被分配的角色id
      'create_time' => time(),
    );
    $rs = $this->_v1_user_role->addUserRole($param);
    if ($rs !== false) {
      return $param;
    } else {
      return $rs;
    }
  }
  //获取最顶级的角色的下级角色数组
  public function getRole()
  {
    //获取root下的所有的角色
    $where = " where parent_id = 3";   
    $highroles = $this->_v1_role->getrole($where, '', ''); 
    //取所有role角色
    $allrole = $this->_v1_role->getrole('','','');
    $rs = array();
    foreach ($highroles as $key => $value) {
      $rs[$value['id']] = $this->_v1_role->test($value['id'], $allrole,1);
    }
    return $rs;
  }
//获取角色类别
  public function getRoleType($user_id)
  {
    $role_type = '3';
    $rolelist = $this->getRole();     //获取最顶级的角色的下级角色数组
    //根据user_id获得该用户所在的角色的下属角色id
    $user_role = $this->_v1_user_role->getRoleidByuserid($user_id);
    foreach ($user_role as $key => $value) {
      if (isset($rolelist[env('SALE_ROLE_ID')][$value]) || ($value == env('SALE_ROLE_ID'))) {
        $role_type = '2';
        break;
      } elseif (isset($rolelist[env('VISIT_ROLE_ID')][$value]) || ($value == env('VISIT_ROLE_ID'))) {
        $role_type = '1';
      }
    }
    return $role_type;
  }
  //获取app角色列表
  public function getrolelists($user_id)
  {
    $rolelist = $this->getRole();     //获取最顶级的角色的下级角色数组
    //根据user_id获得该用户所在的角色的下属角色id
    $user_role = $this->_v1_user_role->getRoleidByuserid($user_id);
    $roleapplist = array();
    foreach ($this->_app_role as $k => $v) {
      foreach ($user_role as $key => $value) {
        if (isset($rolelist[$k][$value]) || ($value == $k)) {
          $rs = $this->_v1_role->getDetail($value);
          $v['haschild'] = $rs['has_child'];
          $v['title'] = $rs['title'];
          $roleapplist[] = $v;
        }
      }
    }

    return $roleapplist;
  }

  public function detailrole($id) 
  {
    $detail = $this->_v1_role->getDetail($id);
    if (empty($detail)) {
        return false;
    } else {
        return new obj_role($detail);
    }
  }

  public function detailuser($user_id, $fields = false) 
  {
    $detail = $this->_anjie_users->getInfoById($user_id);
    if (!empty($detail)) {
      $detail['status'] = $this->get_user_status($detail['is_valid']);
        return new obj_user($detail);
    } else {
        return false;
    }
  }

  public function get_user_status($key = false) 
  {
      $data = array(
          1 => '正常',
          2 => '关闭',
      );
      return isset($data[$key]) ? array('id' => $key, 'text' => $data[$key]) : array('id' => 'USER_ERROR', 'text' => '用户状态错误');
  }

  public function lists_children($id) 
  {
      $r = array();
      $rows = $this->listsrole(array('parent_id' => $id), false, false, false);
      foreach ($rows as $k => $v) {
          if (!in_array($v->role_id, $r)) {
              $r[] = $v->role_id;
              if ($v->has_child == 1) {
                  $rows2 = $this->lists_children($v->role_id);
                  foreach ($rows2 as $k2 => $v2) {
                      if (!in_array($v2, $r)) {
                          $r[] = $v2;
                      }
                  }
              }
          }
      }
      return $r;
  }

  public function listsrole($condition, $page, $size, $order) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      $where = $this->_condition($condition);
      $rows = $this->_v1_role->getrole($where, $orderout, $limitout);
      foreach ($rows as $k => $v) {
          $rows[$k] = new obj_role($v);
      }
      return $rows;
  }

  private function _condition($condition) 
  {
        $where = "where 1=1 ";
        if (isset($condition['parent_id'])) {
          $where = $where . (is_array($condition['parent_id']) ? "and parent_id in ('".implode("','", $condition['parent_id'])."')" : "and parent_id = '". $condition['parent_id'] . "'");
        }
        if (isset($condition['role_id'])) {
          $where = $where . (is_array($condition['role_id']) ? "and id in ('".implode("','", $condition['role_id'])."')"  : "and id = '". $condition['role_id'] . "'");
        }
        if (isset($condition['q'])) {
          $where = $where . ("and title like '% ". trim($condition['q']) ."%'");
        }
        return $where;
  }

  public function count_user($condition) 
  {
      $where = $this->_condition_user($condition);
      $rs = $this->_v1_user_role->getCount($where);
      return $rs['count'];
  }

  private function _condition_user($condition) 
  {
    $whereuserid = '';
    $whererole = '';
      if (isset($condition['user_id'])) {
        $whereuserid = is_array($condition['user_id']) ? "user_id in ('".implode("','", $condition['user_id'])."')" : "user_id = '". $condition['user_id'] . "'";
      }
      if (isset($condition['role_id'])) {
        $whererole = is_array($condition['role_id']) ? "role_id in ('".implode("','", $condition['role_id'])."')"  : "role_id = '". $condition['role_id'] . "'";
      }
      if(isset($condition['user_id']) && isset($condition['role_id'])) {
        $where = 'where '. $whereuserid . 'and ' . $whererole;
      } elseif (!isset($condition['user_id']) && !isset($condition['role_id'])) {
        $where = '';
      } else{
        $where = 'where '. $whererole . $whereuserid;
      }
      return $where;
  }

  public function count($condition) 
  {
    $where = $this->_condition($condition);
    $rs = $this->_v1_role->getCount($where);
    return $rs['count'];
  }

  public function lists_user_modules($condition, $page = false, $size = false, $order = false) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      if (empty($condition['role_id'])) {
          return false;
      }
      if (in_array(1, $condition['role_id']) || $condition['role_id'] == 1) {
          $rows = $this->lists_modules($condition);
          foreach ($rows as $k => $v) {
              $rows[$k] = new obj_role_module(array('module_id' => $v->module_id));
          }
      } else {
          $groupby = "group by module_id";
          $where = $this->_condition_user_modules($condition);
          $rows = $this->_v1_role_privilege->getprivilege($where, $groupby);
          foreach ($rows as $k => $v) {
              $rows[$k] = new obj_role_module($v);
          }
      }
      return $rows;
  }

  private function _condition_user_modules($condition) 
  {
      $where = 'where 1=1 ';
      if (isset($condition['role_id'])) {
        $whererole = is_array($condition['role_id']) ? "and role_id in ('".implode("','", $condition['role_id'])."')" : "and role_id = '". $condition['role_id'] . "'";
      }
      $where = $where . $whererole;
      return $where;
  }

  public function lists_modules($condition, $page = false, $size = false, $order = false) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      $where = $this->_condition_modules($condition);
      $rows = $this->_anjie_category->getDetail($where, $orderout, $limitout);
      foreach ($rows as $k => $v) {
          $rows[$k] = new obj_permission_module($v);
      }
      return $rows;
  }

  private function _condition_modules($condition) 
  {
    $where = "where status =1";
    return $where;
  }

  public function lists_user_method($condition, $page = false, $size = false, $order = false) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      if (empty($condition['role_id'])) {
          return false;
      }
      if (in_array(1, $condition['role_id']) || $condition['role_id'] == 1) {
          $rows = $this->lists_method($condition);
          foreach ($rows as $k => $v) {
              $rows[$k] = new obj_role_method(array('method_id' => $v->method_id,'method_name_cn'=>$v->title));
          }
      } else {
          $groupby = " group by method_id";
          $where = $this->_condition_user_method($condition);
          $rows = $this->_v1_role_privilege->getprivilege($where, $groupby);
          foreach ($rows as $k => $v) {
              $rows[$k] = new obj_role_method($v);
          }
      }
      return $rows;
  }

  public function lists_user_privilege($condition, $page = false, $size = false, $order = false) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      if (empty($condition['role_id'])) {
          return false;
      }
      if (in_array(1, $condition['role_id']) || $condition['role_id'] == 1) {
          $rows = $this->lists_privilege($condition);
          foreach ($rows as $k => $v) {
              $rows[$k] = new obj_role_privilege(array('privilege_id' => $v->privilege_id,'requestname'=>$v->title));
          }
      } else {
          $groupby = " group by privilege_id";
          $where = $this->_condition_user_privilege($condition);
          $rows = $this->_v1_role_privilege->getprivilege($where, $groupby);
          foreach ($rows as $k => $v) {
              $rows[$k] = new obj_role_privilege($v);
          }
      }
      return $rows;
  }

  private function _condition_user_method($condition) 
  {
      $where = "where 1=1 ";
      $wheremodule = '';
      $whererole = '';
      if (isset($condition['module_id'])) {
          $wheremodule = is_array($condition['module_id']) ? " and module_id in ('".implode("','", $condition['module_id'])."')" : "and module_id = '". $condition['module_id'] . "'";
      }
      if (isset($condition['role_id'])) {
          $whererole = is_array($condition['role_id']) ? " and role_id in ('".implode("','", $condition['role_id'])."')" : "and role_id = '". $condition['role_id'] . "'";
      }
      $where = $where . $wheremodule . $whererole;
      return $where;
  }
  private function _condition_user_privilege($condition) 
  {
      $where = "where 1=1 ";
      $wheremodule = '';
      $whererole = '';
      if (isset($condition['method_id'])) {
          $wheremodule = is_array($condition['method_id']) ? " and method_id in ('".implode("','", $condition['method_id'])."')" : "and method_id = '". $condition['method_id'] . "'";
      }
      if (isset($condition['role_id'])) {
          $whererole = is_array($condition['role_id']) ? " and role_id in ('".implode("','", $condition['role_id'])."')" : "and role_id = '". $condition['role_id'] . "'";
      }
      $where = $where . $wheremodule . $whererole;
      return $where;
  }

  public function lists_method($condition, $page = false, $size = false, $order = false) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      $where = $this->_condition_method($condition);
      $rows = $this->_anjie_method->getDetail($where, $orderout, $limitout);
      foreach ($rows as $k => $v) {
          $rows[$k] = new obj_permission_method($v);
      }
      return $rows;
  }

  public function lists_privilege($condition, $page = false, $size = false, $order = false) 
  {
      $limitout = '';
      $orderout = '';
      if ($page && $size) {
        $limitout = " limit ". intval(($page - 1) * $size). ', '. intval($size);
      }
      if ($order) {
        $orderout = "order by ". $order;
      }
      $where = $this->_condition_privilege($condition);
      $rows = $this->_anjie_privilege->getDetail($where, $orderout, $limitout);
      foreach ($rows as $k => $v) {
          $rows[$k] = new obj_permission_privilege($v);
      }
      return $rows;
  }

  private function _condition_method($condition) 
  {
      $where = "where 1=1 and status =1 ";
      if (isset($condition['module_id'])) {
        $where =  $where . (is_array($condition['module_id']) ? "and cid in ('".implode("','", $condition['module_id'])."')"  : "and cid = '". $condition['module_id'] . "'");
      }
      return $where;
  }
  private function _condition_privilege($condition) 
  {
      $where = "where 1=1 status =1 ";
      if (isset($condition['method_id'])) {
        $where =  $where . (is_array($condition['method_id']) ? "and method_id in ('".implode("','", $condition['privilege_id'])."')"  : "and method_id = '". $condition['privilege_id'] . "'");
      }
      return $where;
  }

  public function find($string) 
  {
      $users = array();
      $info = $this->_anjie_users->getInfoById(intval($string));
      $user_id = $info['user_id'];
      if ($user_id) {
          $users[] = $user_id;
      }
      $getInfoByAccount = $this->_anjie_users->getInfoByAccount(intval($string));
      $user_id = $getInfoByAccount['user_id'];
      if ($user_id) {
          $users[] = $user_id;
      }
      $user_ids = $this->_anjie_users->getInfoByName(trim($string));
      foreach ($user_ids as $k => $v) {
          $users[] = $v['user_id'];
      }
      return $users;
  }

  public function detail_method($id) 
  {
      $detail = $this->_detail_method($id);
      if (empty($detail)) {
          return false;
      } else {
          return new obj_permission_method($detail);
      }
  }
  public function detail_privilege($id) 
  {
      $detail = $this->_detail_privilege($id);
      if (empty($detail)) {
          return false;
      } else {
          return new obj_permission_privilege($detail);
      }
  }
  //add
  public function method_detail($id) 
  {
      $detail = $this->_method_detail($id);
      if (empty($detail)) {
          return false;
      } else {
          return new obj_method_permission($detail);
      }
  }
  //add
  public function privilege_detail($id) 
  {
    $privilege = $this->_anjie_privilege->getPrivilegeByMethodId($id);
    if (empty($privilege)) {
          return array();
      } else {
          foreach ($privilege as $key => $value) {
           $return[] = new obj_permission_privilege($value);
          }
          return $return;
      }
    
  }
  //add
  private function _method_detail($id) 
  {
    $detail = $this->_anjie_method->getInfoByMethodidd($id);
    return empty($detail) ? false : $detail;
  }

  private function _detail_method($id) 
  {
    $detail = $this->_anjie_method->getInfoByMethodid($id);
    return empty($detail) ? false : $detail;
  }
  private function _detail_privilege($id) 
  {
    $detail = $this->_anjie_privilege->getPrivilegeById($id);
    return empty($detail) ? false : $detail;
  }

  public function detail_module($id) 
  {
      $detail = $this->_detail_module($id);
      if (empty($detail)) {
          return false;
      } else {
          return new obj_permission_module($detail);
      }
  }
  //add
  public function module_detail($id) 
  {
      $detail = $this->_module_detail($id);
      if (empty($detail)) {
          return false;
      } else {
          return new obj_module_permission($detail);
      }
  }
  //add
  private function _module_detail($id) 
  {
    $detail = $this->_anjie_category->getInfoByIdd($id);
    return empty($detail) ? false : $detail;
  }
  
  private function _detail_module($id) 
  {
    $detail = $this->_anjie_category->getInfoById($id);
    return empty($detail) ? false : $detail;
  }

  public function is_parent($parent_id, $children_id) 
  {
      if ($children_id == 0) {
          return false;
      }
      $detail = $this->_v1_role->getDetail($children_id);
      if ($detail) {
          if ($detail['parent_id'] == $parent_id) {
              return true;
          } else {
              //往上递归查找
              return $this->is_parent($parent_id, $detail['parent_id']);
          }
      } else {
          return false;
      }
  }

  public function updateRole($id, $data) 
  {
      $param = array();
      $param['title'] = trim($data['title']);
      $param['desc'] = trim($data['desc']);
      $updatev1role = $this->_v1_role->updateByID($param['title'], $param['desc'], $id);
      $updateviroleprivilege = $this->_v1_role_privilege->deleteByRoleid($id);
      $privilege = array();
      foreach ($data['permission']['modules'] as $k => $v) {
          $privilege[] = array(
              'role_id' => $id,
              'module_id' => intval($v),
              'module' => '',
              'method_id' => 0,
              'method' => '',
              'status' => 1,
              'create_time' => time(),
          );
      }
      foreach ($data['permission']['methods'] as $k => $v) {
          $method = $this->detail_method(intval($v));
          $privilege[] = array(
              'role_id' => $id,
              'module_id' => $method->module_id,
              'module' => '',
              'method_id' => $method->method_id,
              'method' => '',
              'status' => 1,
              'create_time' => time(),
          );
      }
      foreach ($data['permission']['privilege'] as $k => $v) {
          $privileges = $this->detail_privilege(intval($v));
          $method = $this->detail_method(intval($privileges->method_id));
          $privilege[] = array(
              'role_id' => $id,
              'module_id' => $method->module_id,
              'module' => '',
              'method_id' => $method->method_id,
              'privilege_id' =>$privileges->privilege_id,
              'method' => '',
              'status' => 1,
              'create_time' => time(),
          );
      }
      if (!empty($privilege)) {
          $insettoprivilege = $this->_v1_role_privilege->insetToPrivilege($privilege);
      }
      return $id;
  }

  public function addrole($data) 
  {
      $param = array();
      $param['title'] = trim($data['title']);
      $param['parent_id'] = intval($data['parent_id']);
      $param['desc'] = trim($data['desc']);
      $param['has_child'] = 2;
      $param['create_time'] = time();
      $parent_role = $this->_v1_role->getDetail($data['parent_id']);
      $param['depth'] = intval($parent_role['depth']) + 1;
      $id = $this->_v1_role->inserttov1role($param);
      $updaterole = $this->_v1_role->setHaschild($param['parent_id'], 2, 1);
      $privilege = array();
      foreach ($data['permission']['modules'] as $k => $v) {
          $privilege[] = array(
              'role_id' => $id,
              'module_id' => intval($v),
              'module' => '',
              'method_id' => 0,
              'privilege_id' =>0,
              'method' => '',
              'status' => 1,
              'create_time' => time(),
          );
      }
      foreach ($data['permission']['methods'] as $k => $v) {
          $method = $this->detail_method(intval($v));
          $privilege[] = array(
              'role_id' => $id,
              'module_id' => $method->module_id,
              'module' => '',
              'method_id' => $method->method_id,
              'privilege_id' =>0,
              'method' => '',
              'status' => 1,
              'create_time' => time(),
          );
      }
      foreach ($data['permission']['privilege'] as $k => $v) {
          $privileges = $this->detail_privilege(intval($v));
          $method = $this->detail_method(intval($privileges->method_id));
          $privilege[] = array(
              'role_id' => $id,
              'module_id' => $method->module_id,
              'module' => '',
              'method_id' => $method->method_id,
              'privilege_id' =>$privileges->privilege_id,
              'method' => '',
              'status' => 1,
              'create_time' => time(),
          );
      }
      if (!empty($privilege)) {
          $insettoprivilege = $this->_v1_role_privilege->insetToPrivilege($privilege);
      }
      return $id;
  }

  public function find_by_loginname($loginname) 
  {
      if (!trim($loginname)) {
          return false;
      }
      $detail = $this->_anjie_users->getDetailByUsername($loginname);
      if (!empty($detail)) {
        $detail['status'] = $this->get_user_status($detail['is_valid']);
        return new obj_user($detail);
      } else {
          return false;
      }
  }

  public function is_exists_user($condition) 
  {
      $rs = $this->_v1_user_role->getInfoByUseridAndRoleid($condition['user_id'], $condition['role_id']);
      return $rs;
  }

  public function adduserrole($data)
  {
      $param = array();
      $param['user_id'] = intval($data['user_id']);
      $param['role_id'] = intval($data['role_id']);
      $param['create_time'] = time();
      $role_type = '3';
      $rolelist = $this->getRole();     //获取最顶级的角色的下级角色数组
      if (isset($rolelist[env('VISIT_ROLE_ID')][$param['role_id']]) || ($param['role_id'] == env('VISIT_ROLE_ID'))) {
        $role_type = '1';  //家访组的
        $where = ' where role_id = '. $param['role_id'];
        $order = ' order by create_time asc limit 1';
        $role_user = $this->_v1_user_role->getuUserrole($where, $order);
        if (!empty($role_user)) {
          $where = " where to_user_id = ". $role_user[0]['user_id'] . " and has_pickup=2 and has_assign=2 and visit_status=2";    //查询未认领且未分配
          $order = ' order by id asc';
          $visit_works = $this->_anjie_visit->getDetail($where, $order);
          if (!empty($visit_works)) {
            $adduservisit = $this->_anjie_visit->adduservisit($param['user_id'], $visit_works);
            if ($adduservisit == false) {
              return false;
            }
          }
        } 
      }
      $id = $this->_v1_user_role->addUserRole($param);
      return $id;
  }
//同一账号不可同时为三级及以下的角色
  public function checkuserrole($data)
  {
    //取所有role角色
    $allrole = $this->_v1_role->getrole('','','');   //所有角色
    $roleids = $this->_v1_user_role->getRoleidByuserid($data['user_id']);   //该用户的所有角色
    $where = ' where parent_id = 3 and depth = 3';  //林润审批下面的所有角色
    $rolelists = $this->_v1_role->getrole($where, '', '');
    $role_id3 = 0;   //第三级的role_id
    foreach ($rolelists as $key => $value) {
      $visitlist = $this->_v1_role->test($value['id'], $allrole,1);
      if (($value['id'] == $data['role_id']) || (isset($visitlist[$data['role_id']]))) {
        $role_id3 = $value['id'];
      }
    }
    if ($role_id3 !== 0) {
      $visitlist = $this->_v1_role->test($role_id3, $allrole,1);
      foreach ($roleids as $k => $v) {
        if (($role_id3 == $v) || (isset($visitlist[$v]))) {
          return false;
        }
      }
    }
    return true;
  }

  public function deleterole($id)
  {
      $deletev1role = $this->_v1_role->deleteById($id);
      $deletev1privilege = $this->_v1_role_privilege->deleteByRoleid($id);
      if ($deletev1role && $deletev1privilege) {
        return true;
      } else {
        return false;
      }
  }

  public function new_obj_user($user)
  {
    return new obj_user($user);
  }

  public function getworkflow($user_id)
  {
    $this->_workflow = new Workflow();
    $param['user_id'] = $user_id;
    $param['role_id'] = '';
    $param['has_locked'] = 1;
    $param['has_completed'] = 2;
    $param['has_stoped'] = 2;
    $param['page'] = '1';
    $param['size'] = '1';
    $condition = array();
    $getworkflow = $this->_workflow->getworkflowlist($param, $condition);
    return $getworkflow;
  }

  public function delete_user($user_id, $role_id)
  {
     $role_type = '3';
     $rolelist = $this->getRole();     //获取最顶级的角色的下级角色数组
     if (isset($rolelist[env('VISIT_ROLE_ID')][$role_id]) || ($role_id == env('VISIT_ROLE_ID'))) {  //如果是家访
      $param['user_id'] = $user_id;
      $deleteByuserid = $this->_anjie_visit->deleteByuserid($param);
     }
    $delete = $this->_v1_user_role->deleteByUseridAndRoleid($user_id, $role_id);
    return $delete > 0;
  }

  public function getProvince()
  {
    $rs = $this->_anjie_province->getAllProvinceInfo();
    return $rs;
  }

  public function getCityByProvinceid($provinceid)
  {
    $rs = $this->_anjie_city->getInfoByProvinceid($provinceid);
    return $rs;
  }

  public function makeEmployeeNumber()
  {
    
  }
//添加app角色
  public function addapprole($param)
  {
    $check = $this->_auth->checkPasswordByAccount($param['account'], $param['password']);
    if (isset($check['app_role']) && $check['app_role'] !== '') {
      return false;            //该用户已经设置过app的角色
    }
    if ($check['name'] !== '' && $check['name'] !== null) {
      $param['name'] = $check['name'];
    } elseif ($param['app_role'] == '1') {
      $param['name'] = '销售人员';
    } elseif ($param['app_role'] == '2') {
      $param['name'] = '家访人员';
    } else{
      $param['name'] = '';
    }
    $rs = $this->_anjie_users->addapprole($check['id'], $param);
    return $rs;
  }
}



class obj_role_user 
{
    public $role = array();
    public $user = array();
    public $create_time = 0;
    
    public function __construct($role_user) {
        $this->role = $role_user['role'];
        $this->user = $role_user['user'];
        $this->create_time = $role_user['create_time'];
    }
}

class obj_role 
{
    public $role_id = 0;
    public $title = '';
    public $desc = '';
    public $parent_id = 0;
    public $has_child = 0;
    
    public function __construct($role) {
        $this->role_id = $role['id'];
        $this->title = $role['title'];
        $this->desc = $role['desc'];
        $this->parent_id = $role['parent_id'];
        $this->has_child = $role['has_child'];
    }
}
class obj_user 
{

    public $user_id = 0;
    public $loginname = '';
    public $credit = 0;
    public $realname = '';
    public $mobile = '';
    public $id_card = '';
    public $verify_time = 0;
    public $register_time = 0;
    public $status = array();

    public function __construct($user) {
        $this->user_id = isset($user['id']) ? $user['id'] : '';
        $this->loginname = isset($user['name']) ? $user['name'] : '';
        $this->credit = isset($user['credit']) ? $user['credit'] : '';
        $this->realname = isset($user['realname']) ? $user['realname'] : '';
        $this->mobile = isset($user['account']) ? $user['account'] : '';
        $status = isset($user['status']) ? $user['status'] : '';
        $this->status = new obj_item($status);
        $this->id_card = isset($user['id_card']) ? $user['id_card'] : '';
        $this->register_time = isset($user['creat_time']) ? $user['creat_time'] : '';
        $this->verify_time = isset($user['modify_time']) ? $user['modify_time'] : '';
        $this->business_area = isset($user['business_area']) ? $user['business_area'] : '';
        $this->employee_number = isset($user['employee_number']) ? $user['employee_number'] : '';
        $this->province = isset($user['province']) ? $user['province'] : '';
        $this->city = isset($user['city']) ? $user['city'] : '';
        $this->town = isset($user['town']) ? $user['town'] : '';
        $this->area_add = isset($user['area_add']) ? $user['area_add'] : '';

    }
}

class obj_item 
{
    public $id;
    public $text;

    public function __construct($item) {
        if (is_array($item)) {
            $this->id = $item['id'];
            $this->text = $item['text'];
        } else if (is_object($item)) {
            $this->id = $item->id;
            $this->text = $item->text;
        } else {
            //todo throw error
            $this->id = 0;
            $this->text = '未知';
        }
    }

}

class obj_permission_module 
{
    public $module_id = 0;
    public $title = 0;
    
    public function __construct($module) {
        $this->module_id = $module['id'];
        $this->title = $module['menu_title'];
    }
}
class obj_permission_privilege
{
    public $method_id = 0;
    public $privilege_id = 0;
    public $title = 0;
    
    public function __construct($module) {
        $this->method_id = $module['method_id'];
        $this->privilege_id = $module['id'];
        $this->id = 'privilege_' . $module['id'];
        $this->cid = 'methods_' . $module['method_id'];
        $this->title = $module['requestname'];
    }
}
//add
class obj_module_permission
{
    public $module_id = 0;
    public $title = 0;
    
    public function __construct($module) {
        $this->id = 'modules_' . $module['id'];
        $this->cid= 'catagory_' . 0;
        $this->title = $module['menu_title'];
    }
}
class obj_role_module 
{
    public $module_id = 0;
    
    public function __construct($role_module) {
        $this->module_id = $role_module['module_id'];
    }
}

class obj_permission_method 
{
    public $method_id = 0;
    public $module_id = 0;
    public $title = 0;
    
    public function __construct($method) {
        $this->method_id = $method['method_id'];
        $this->module_id = $method['cid'];
        $this->title = $method['method_name_cn'];
    }
}
//add
class obj_method_permission
{
    public $method_id = 0;
    public $module_id = 0;
    public $title = 0;
    
    public function __construct($method) {
        $this->id = 'methods_' . $method['method_id'];
        $this->cid = 'modules_' . $method['cid'];
        $this->title = $method['method_name_cn'];
    }
}

class obj_role_method 
{
    public $method_id = 0;
    
    public function __construct($role_method) {
        $this->method_id = $role_method['method_id'];
    }
}
class obj_role_privilege
{
    public $privilege_id = 0;
    
    public function __construct($role_privilege) {
        $this->privilege_id = $role_privilege['privilege_id'];
    }
}
