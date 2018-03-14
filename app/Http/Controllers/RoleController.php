<?php

namespace App\Http\Controllers;
use App\Http\Models\business\Role;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Auth;
use App\Http\Models\table\T_address_province;
use App\Http\Models\table\T_address_city;
use App\Http\Models\table\V1_user_role;
use App\Http\Models\table\Anjie_user_role_area_privilege;
use App\Http\Models\table\Anjie_users;

class RoleController extends Controller
{ 
    private $_role = null;
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_role = new Role();
        $this->_auth = new Auth();
    }
    /**
     * 用户角色页面
     */
    public function index()
    {
        return view('admin.role.index')->with('title', '用户角色');
    }
    /**
     * 角色设置页面
     */
    public function roleset()
    {
        return view('admin.role.roleset')->with('title', '角色设置');
    }
    /**
     * 添加用户页面
     */
    public function adduser()
    {
        return view('admin.role.adduser')->with('title', '添加用户');
    }
    /**
     * 修改用户页面
     */
    public function edituser()
    {
        $user['account'] = Request::input('account', '');
        $user['name'] = Request::input('name', '');
        $user['province'] = Request::input('province', '');
        $user['city'] = Request::input('city', '');
        $user['town'] = Request::input('town', '');
        $user['area_add'] = Request::input('area_add', '');
        $provincecode = $this->_role->getprovincecode($user['province']);  //省代码
        $citycode = $this->_role->getcitycode($user['city']);          //市代码
        return view('admin.role.edituser', ['provincecode'=>empty($provincecode) ? '' : $provincecode['code'], 'citycode'=>empty($citycode) ? '' : $citycode['code'], 'oldprovince'=>$user['province'], 'oldcity'=>$user['city'], 'oldtown'=>($user['town'] == 'null') ? '' :$user['town'], 'area_add'=>$user['area_add'], 'account'=>$user['account'], 'name'=>$user['name']])->with('title', '编辑用户');
    }
    /**
     * 角色编辑页面
     */
    public function roleedit()
    {
        return view('admin.role.roleedit')->with('title', '角色编辑');
    }
    /**
     * 角色编辑页面
     */
    public function editrole()
    {
        return view('admin.role.editrole')->with('title', '角色编辑');
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
    public function adduserpost()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $user = array();
        //传入参数
        $user['type'] = Request::input('type', ''); //1:林润审批，2：聚车贷
        $user['account'] = Request::input('account', '');//账号
        $user['name'] = Request::input('name', '');//姓名
        $user['province'] = Request::input('province', '');  //省
        $user['city'] = Request::input('city', '');//市
        $user['town'] = Request::input('town', ''); //区
        $user['area_add'] = Request::input('area_add', ''); //三级以外的地址
        $user['area'] = $user['province'] . $user['city'] . $user['town'] . $user['area_add']; //业务区域
        $user['password'] = md5(Request::input('password', ''));//密码
        $user['password_confirm'] = md5(Request::input('password_confirm', ''));//确认密码
        $this->_common->setlog();
        //验证不能为空的参数是否都有
        if ($user['account'] == '' || $user['name'] == '' || $user['password_confirm'] == ''  || $user['password'] == ''|| $user['province'] == ''  || $user['city'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);  
        }
        //验证密码的长度是否大于6
        if (strlen($user['password']) <6) {
            return $this->_common->output('', Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_NO, Constant::ERR_PASSWORD_LENGTH_NOT_ENOUGH_MSG);  
        }
        //验证传入的密码和确认密码是否相等
        if ($user['password'] !== $user['password_confirm']) {
            return $this->_common->output('', Constant::ERR_PASSWORD_INCONFORMITY_NO, Constant::ERR_PASSWORD_INCONFORMITY_MSG);   
        }
        //添加用户的逻辑
        return $this->_role->addUser($user, $user_id);
    }
    /**
     * 编辑用户
     * @param account          账号
     * @param name             姓名
     * @param province         省
     * @param city             城市
     * @param area_add         除了省和城市之外需要的区域
     * @param oldaccount       原账号
     */
    public function edituserpost()
    {
        //传入参数
        $user['account'] = Request::input('account', '');//账号
        $user['name'] = Request::input('name', '');//姓名
        $user['province'] = Request::input('province', '');  //省
        $user['city'] = Request::input('city', '');//市
        $user['town'] = Request::input('town', ''); //区
        $user['area_add'] = Request::input('area_add', ''); //三级以外的地址
        $user['area'] = $user['province'] . $user['city'] . $user['town'] . $user['area_add']; //业务区域
        $user['oldaccount'] = Request::input('oldaccount', '');
        $this->_common->setlog();
         //验证不能为空的参数是否都有
        if ($user['account'] == '' || $user['area'] == '' || $user['name'] == ''|| $user['oldaccount'] == ''|| $user['province'] == ''  || $user['city'] == '') {
            return $this->_common->output('', Constant::ERR_DATA_INCOMPLETE_NO, Constant::ERR_DATA_INCOMPLETE_MSG);
        }
        //编辑用户的逻辑
        return $this->_role->editUser($user);
    }
    //删除用户的接口
    public function deleteuserpost()
    {
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['account'] = Request::input('account', '');//账号
        $param['user_id'] = Request::input('user_id', '');//用户id
        //验证该用户是否有正在进行的申请件
        $getworkflow = $this->_role->getworkflow($param['user_id']);
        if ($getworkflow['total'] !== '0') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '该用户有正在进行中的申请件，不能被删除');
        }
        $deleteuserpost = $this->_role->deleteuserpost($param);
        return $deleteuserpost;
    }
    /**
     * 列出角色对应用户
     * @param page             页数
     * @param size             每一页展示多少数据
     */
    public function lists() 
    {
        $page =  intval(Request::input('page', '1'));
        $size =  intval(Request::input('size', '100'));
        $condition = Request::all();
        if (!isset($condition['order'])) {
            $order = 'parent_id asc,id asc';
        } else {
            $order = Request::input('order', '');
        }
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $role_ids = array();
        foreach ($roles as $k => $v) {
            $role_ids = array_merge($role_ids, $this->_role->lists_children(intval($v->role->role_id)), array($v->role->role_id));
        }
        $condition['role_id'] = $role_ids;
        $r['rows'] = $this->_role->listsrole($condition, $page, $size, $order);
        $r['total'] = $this->_role->count($condition);
        foreach ($r['rows'] as $k => $v) {
            $r['rows'][$k]->nums = $this->_role->count_user(array('role_id' => $v->role_id));
        }
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 列出用户的Module
     */
    public function listsUserModule() 
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $role_ids = array();
        foreach ($roles as $k => $v) {
            $role_ids[] = intval($v->role->role_id);
        }
        $r['rows'] = $this->_role->lists_user_modules(array('role_id' => $role_ids));
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 列出用户的Method
     */
    public function listsUserMethod() 
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $role_ids = array();
        foreach ($roles as $k => $v) {
            $role_ids[] = intval($v->role->role_id);
        }
        $r['rows'] = $this->_role->lists_user_method(array('role_id' => $role_ids));
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 删除用户
     * @param role_id             角色ID
     * @param user_id             用户ID
     */
    public function deleteuser()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $role_id = Request::input('role_id', '');
        $userid = Request::input('user_id', '');
        $this->_common->setlog();
        //检查是否拥有删除的权限，role_id在当前角色的role_id之下
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $has_permission = false;
        foreach ($roles as $k => $v) {
            if ($this->_role->is_parent($v->role->role_id, $role_id)) {
                $has_permission = true;
                break;
            }
        }
        if (!$has_permission) {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '没有权限');   
        }
        //验证该用户是否有正在进行的申请件
        $getworkflow = $this->_role->getworkflow($userid);
        if ($getworkflow['total'] !== '0') {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, '该用户有正在进行中的申请件，不能被删除');
        }
        $r = $this->_role->delete_user($userid, $role_id);
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 列出所有用户
     * @param page             页码
     * @param size             每页条数
     */
    public function listsuser() 
    {
        $page = intval(Request::input('page', '')) > 0 ? intval(Request::input('page', '')) : 1;
        $size = intval(Request::input('size', '')) > 0 ? intval(Request::input('size', '')) : 20;
        $condition = Request::all();
        // $order = Request::input('order', '');
        $q = Request::input('q', '');
        // if ($order == '') {
            $order = 'id asc';
        // }
        if ($q !== '') {
            $user_ids = $this->_role->find($q);
            if (count($user_ids) > 0) {
                $condition['user_id'] = $user_ids;
            } else {
                unset($condition['user_id']);
            }
        }
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $role_ids = array();
        foreach ($roles as $k => $v) {
            $role_ids = array_merge($role_ids, $this->_role->lists_children(intval($v->role->role_id)), array($v->role->role_id));
        }
        if (in_array(Request::input('role_id', ''), $role_ids)) {
            
        } else {
            $condition['role_id'] = $role_ids;
        }
        $r['rows'] = $this->_role->lists_user($condition, $page, $size, $order);
        $r['total'] = $this->_role->count_user($condition);
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 列出所有用户
     * @param page             页码
     * @param size             每页条数
     * @param keyword          查询的关键词
     * @param condition        查询的条件
     */
    public function listsalluser()
    {
        $page = intval(Request::input('page', '')) > 0 ? intval(Request::input('page', '')) : 1;
        $size = intval(Request::input('size', '')) > 0 ? intval(Request::input('size', '')) : 20;
        $keyword = Request::input('keyword', ''); //查询的关键词
        $condition = Request::input('condition', '');  //查询的条件
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $order = 'id desc';
        $r['rows'] = $this->_role->lists_user_all($page, $size, $order, $keyword, $condition, $user_id);
        $r['total'] = $this->_role->count_user_all($keyword, $condition, $user_id);
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 获取角色的详情
     * @param role_id             角色id
     */
    public function get() 
    {
        $r = $this->_role->detailrole(Request::input('role_id', ''));
        if ($r) {
            return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_ITEM_NOT_EXISTS_NO, Constant::ERR_ITEM_NOT_EXISTS_MSG);
        }
    }
    /**
     * 获得当前用户角色对应的可设置的权限
     */
    public function permissiontree() 
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $role_ids = array();
        foreach ($roles as $k => $v) {
            $role_ids[] = intval($v->role->role_id);
        }
        $modules = $this->_role->lists_user_modules(array('role_id' => $role_ids));
        $r = array();
        foreach ($modules as $k => $v) {
            $methods = $this->_role->lists_user_method(array('role_id' => $role_ids, 'module_id' => $v->module_id), false, false, 'method_id desc');
            foreach ($methods as $k2 => $v2) {
                if ($v2->method_id == 0) {
                    unset($methods[$k2]);
                } else {
                    $methods[$k2] = $this->_role->detail_method($v2->method_id);
                }
            }
            $module = $this->_role->detail_module($v->module_id);
            $r['rows'][] = array(
                'module' => $module,
                'children' => $methods,
            );
        }
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 获得当前用户角色对应的可设置的权限
     */
    //add
    public function treepermission() 
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $role_ids = array();
        foreach ($roles as $k => $v) {
            $role_ids[] = intval($v->role->role_id);
        }
        $modules = $this->_role->lists_user_modules(array('role_id' => $role_ids));
        $r = array();
        $r['rows'] = array();
        foreach ($modules as $k => $v) {
            $methods = $this->_role->lists_user_method(array('role_id' => $role_ids, 'module_id' => $v->module_id), false, false, 'method_id desc');
            foreach ($methods as $k2 => $v2) {
                if ($v2->method_id == 0) {
                    unset($methods[$k2]);
                } else {
                    $methods[$k2] = $this->_role->method_detail($v2->method_id);
                }
                $privilege = $this->_role->privilege_detail($v2->method_id);
                $r['rows'] = array_merge($r['rows'], $privilege);
            }
            $r['rows'] = array_merge($r['rows'], $methods);
            $module = array($this->_role->module_detail($v->module_id));
            $r['rows'] = array_merge($r['rows'], $module);
        }
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 获得当前用户角色对应的可设置的权限
     */
    public function listsmodule() 
    {
        $roleid = Request::input('role_id', '');
        $modules = $this->_role->lists_user_modules(array('role_id' => array($roleid)));
        foreach ($modules as $k => $v) {
            $modules[$k] = $this->_role->detail_module($v->module_id);
        }
        $r['rows'] = $modules;
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 获得当前用户角色对应的可设置的权限
     */
    public function listsmethod()
    {
        $roleid = Request::input('role_id', '');
        $methods = $this->_role->lists_user_method(array('role_id' => array($roleid)), false, false, 'method_id desc');
        foreach ($methods as $k2 => $v2) {
            $methods[$k2] = $this->_role->detail_method($v2->method_id);
        }
        $r['rows'] = $methods;
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    /**
     * 获得当前用户角色对应的可设置的权限
     */
    public function listsprivilege()
    {
        $roleid = Request::input('role_id', '');
        $privileges = $this->_role->lists_user_privilege(array('role_id' => array($roleid)), false, false, 'privilege_id desc');
        foreach ($privileges as $k2 => $v2) {
            $privileges[$k2] = $this->_role->detail_privilege($v2->privilege_id);
        }
        $r['rows'] = $privileges;
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function updaterole() 
    {
        $id = Request::input('role_id', '');
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $permissionid = Request::input('permission', '');
        $title = Request::input('title', '');
        $this->_common->setlog();
        if (intval($id) == 1) {
            return $this->_common->output(false, Constant::ERR_PERMISSION_DENIED_NO, Constant::ERR_PERMISSION_DENIED_MSG);
        }
        if (!$this->_role->detailrole($id)) {
            return $this->_common->output(false, Constant::ERR_ITEM_NOT_EXISTS_NO, Constant::ERR_ITEM_NOT_EXISTS_MSG);
        }
        //检查是否拥有添加的权限，parent_id为当前角色的role_id 或在它之下
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $has_permission = false;
        foreach ($roles as $k => $v) {
            if ($v->role->role_id == $id || $this->_role->is_parent($v->role->role_id, $id)) {
                $has_permission = true;
                break;
            }
        }
        if (!$has_permission) {
            return $this->_common->output(false, Constant::ERR_PERMISSION_DENIED_NO, Constant::ERR_PERMISSION_DENIED_MSG);
        }
        $this->_role->updateRole($id, array('title' => $title, 'permission' => json_decode($permissionid, true), 'desc'=>''));
        $r = $this->_role->detailrole($id);
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function roleadd()
    {
        $parentid = intval(Request::input('parent_id', ''));
        $title = Request::input('title', '');
        $desc = Request::input('desc', '');
        $permission = Request::input('permission', '');
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $this->_common->setlog();
        if ($parentid < 1) {
            return $this->_common->output(false, Constant::ERR_PERMISSION_DENIED_NO, Constant::ERR_PERMISSION_DENIED_MSG);
        }
        //检查是否拥有添加的权限，parent_id为当前角色的role_id 或在它之下
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $has_permission = false;
        foreach ($roles as $k => $v) {
            if ($v->role->role_id == $parentid || $this->_role->is_parent($v->role->role_id, $parentid)) {
                $has_permission = true;
                break;
            }
        }
        if (!$has_permission) {
            return $this->_common->output(false, Constant::ERR_PERMISSION_DENIED_NO, Constant::ERR_PERMISSION_DENIED_MSG);
        }
        $role_id = $this->_role->addrole(array('title' => $title, 'parent_id' => $parentid, 'desc' => $desc, 'permission' => json_decode($permission, true)));
        $r = $this->_role->detailrole($role_id);
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function finduser()
    {
        $loginamesin = Request::input('loginnames', '');
        $loginames = json_decode($loginamesin, true);
        $r = array(
            'not_found' => array(),
            'rows' => array(),
            'total' => 0
        );
        foreach ($loginames as $k => $v) {
            $u = $this->_role->find_by_loginname($v);
            if ($u !== false) {
                $r['rows'][] = $u;
            } else {
                $r['not_found'][] = $this->_role->new_obj_user(array('name' => $v));
            }
        }
        $r['total'] = count($r['rows']);
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function adduserrole()
    {
        $user_id = Request::input('user_id', '');
        $role_ids = Request::input('role_ids', '');
        $role_id = current(explode(',', $role_ids));
        $this->_common->setlog();
        $user = $this->_role->detailuser($user_id);
        if (empty($user)) {
            return $this->_common->output(false, Constant::ERR_ITEM_NOT_EXISTS_NO, Constant::ERR_ITEM_NOT_EXISTS_MSG);
        }
        $role = $this->_role->detailrole($role_id);
        if (empty($role)) {
            return $this->_common->output(false, Constant::ERR_ITEM_NOT_EXISTS_NO, Constant::ERR_ITEM_NOT_EXISTS_MSG);
        }
        if ($this->_role->is_exists_user(array('user_id' => $user_id, 'role_id' => $role_id))) {
            return $this->_common->output(false, Constant::ERR_ROLE_USER_REPEAT_NO, Constant::ERR_ROLE_USER_REPEAT_MSG . "[user_id={$user_id},role_id={$role_id}]");
        }
        $checkuserrole = $this->_role->checkuserrole(array(
            'role_id' => $role_id,
            'user_id' => $user_id
        ));
        if ($checkuserrole == false) {
            return $this->_common->output(false, Constant::ERR_ROLE_USER_REPEAT_NO, "同一个类别下只能添加一种角色" . "[user_id={$user_id},role_id={$role_id}]");
        }
        $this->_role->adduserrole(array(
            'role_id' => $role_id,
            'user_id' => $user_id
        ));
        return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

    public function deleterole()
    {
        //检查是否拥有删除的权限，role_id在当前角色的role_id之下
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $roleid = Request::input('role_id', '');
        $this->_common->setlog();
        $roles = $this->_role->lists_user(array('user_id' => $user_id));
        $has_permission = false;
        foreach ($roles as $k => $v) {
            if ($this->_role->is_parent($v->role->role_id, $roleid)) {
                $has_permission = true;
                break;
            }
        }
        if (!$has_permission) {
            return $this->_common->output(false, Constant::ERR_PERMISSION_DENIED_NO, Constant::ERR_PERMISSION_DENIED_MSG);
        }
        if (intval($roleid) == 1) {
            return $this->_common->output(false, Constant::ERR_PERMISSION_DENIED_NO, Constant::ERR_PERMISSION_DENIED_MSG);
        }
        $count = $this->_role->count_user(array('role_id' => $roleid));
        if ($count > 0) {
            return $this->_common->output(false, Constant::ERR_ROLE_MEMBER_NOT_EMPTY_NO, "[{$count}]" . constant::ERR_ROLE_MEMBER_NOT_EMPTY_MSG);
        }
        $r = $this->_role->detailrole($roleid);
        if ($r) {
            $r2 = $this->_role->deleterole($roleid);
            return $this->_common->output($r2, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
        } else {
            return $this->_common->output(false, Constant::ERR_ITEM_NOT_EXISTS_NO, Constant::ERR_ITEM_NOT_EXISTS_MSG);
        }
    }
//列出所有没有身份的人
    public function listnoidentify()
    {
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $page = intval(Request::input('page', '')) > 0 ? intval(Request::input('page', '')) : 1;
        $size = intval(Request::input('size', '')) > 0 ? intval(Request::input('size', '')) : 20;
        $order = 'id desc';
        $r['rows'] = $this->_role->list_no_identify($page, $size, $order); //列出所有没有身份的人
        $r['total'] = $this->_role->count_no_identify();                   //计算没有身份的人的数量
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
//列出下属列表
    public function listsubordinate()
    {
        //获取该用户的身份
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $condition['userid'] = $user_id;
        $page = intval(Request::input('page', '')) > 0 ? intval(Request::input('page', '')) : 1;
        $size = intval(Request::input('size', '')) > 0 ? intval(Request::input('size', '')) : 20;
        $condition['visit_lat'] =  Request::input('visit_lat', '');                 //纬度
        $condition['visit_lng'] =  Request::input('visit_lng', '');                 //纬度
        $condition['taskorder'] = Request::input('taskorder', '');   //任务量排序  1、按任务量倒序，2、按任务量顺序 不传则忽略这个条件
        $condition['distanceorder'] = Request::input('distanceorder', '');   //距离远近排序  1、按距离最近，2、按距离最远 不传则忽略这个条件
        $order = 'id desc';
        $r['rows'] = $this->_role->listsubordinate($condition, $page, $size, $order);       //列出该用户的所有下属
        $r['total'] = $this->_role->count_subordinate($condition);                          //计算该用户的所有下属的数量
        return $this->_common->output($r, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
//获取下属详情
    public function getsubordinateinfo()
    {
        //获取该用户的身份
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $subordinate_id = Request::input('user_id', '');  //下属的userid
        $rs = $this->_role->getsubordinateinfo($user_id, $subordinate_id); //获取该用户的下属详情
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
//列出下级角色
    public function listsubordinaterole()
    {
        //获取该用户的身份
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $rs = $this->_role->listsubordinaterole($user_id, env('VISIT_ROLE_ID'));//列出下级角色
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
//新增下属
    public function addsubordinate()
    {
        //获取该用户的身份
        $token =  Request::input('token', '');
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $condition['subordinate_id'] =  Request::input('user_id', ''); //下属的userid
        $condition['role_id'] =  Request::input('role_id', ''); //下属的userid
        $rs = $this->_role->addsubordinate($user_id, $condition); //新增下属
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
//添加app角色
    public function addapprole()
    {
        $param['account'] =  Request::input('account', ''); //账号
        $param['password'] =  Request::input('password', ''); //MD5后的密码
        $param['app_role'] =  Request::input('app_role', ''); //app端选择出来的角色，1为销售组，2为家访组
        $rs = $this->_role->addapprole($param);
        if ($rs !== false) {
            return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }    
    
    //修改用户角色的城市权限
    public function setUserRoleAreaPrivilege() {
        //获取该用户的身份
        $token = Request::input('token', '');
        $admin_user_id = $this->_auth->getUseridBytoken($token);
        if (!$admin_user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);   //用户不存在返回错误
        }
        $user_id = Request::input('user_id', ''); //用户ID
        $username = Request::input('username', ''); //用户名
        $role_id = Request::input('role_id', ''); //角色ID
        $province_names = explode(',', Request::input('province_names', '')); //省份，逗号分隔
        $city_names = explode(',', Request::input('city_names', '')); //城市，逗号分隔
        if (empty($user_id) && empty($username)) {
            return $this->_common->output('', Constant::ERR_ACCOUNT_NOT_EXISTS_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_NO);   //用户不存在返回错误
        }
        $tb_anjie_users = new Anjie_users();
        if (empty($user_id)) {//user_id和username优先user_id
            $userinfo = $tb_anjie_users->getInfoByAccount($username);
        } else {
            $userinfo = $tb_anjie_users->getInfoById($user_id);
        }
        if (empty($userinfo)) {
            return $this->_common->output('', Constant::ERR_ACCOUNT_NOT_EXISTS_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_NO);   //用户不存在返回错误
        } else {
            $user_id = $userinfo['id'];
        }
        if (empty($role_id)) {
            return $this->_common->output('', Constant::ERR_ACCOUNT_NOT_EXISTS_NO, Constant::ERR_ACCOUNT_NOT_EXISTS_NO);   //用户角色不存在返回错误
        }
        //检查用户是否有这个角色
        $tb_user_role = new V1_user_role();
        $user_roles = $tb_user_role->getRoleidByuserid($user_id);
        $role_ids = array();
        foreach ($user_roles as $k => $v) {
            $role_ids[$v] = 1;
        }
        if (!isset($role_ids[$role_id])) {
            foreach ($role_ids as $k => $v) {
                $user_roles2 = $this->_role->listsubordinaterole($user_id, $k);
                foreach ($user_roles2 as $k2 => $v2) {
                    $role_ids[$k2] = 1;
                }
            }
        }
        if (!isset($role_ids[$role_id])) {
            return $this->_common->output('', Constant::ERR_ACCOUNT_NOT_EXISTS_NO, '用户角色不存在');   //用户角色不存在返回错误
        }
        $work_role_ids = array();
        $maps = array(
            236 => '72', //人行征信申请
            237 => '80', //征信报告
            238 => '82', //人工审批（一审）
            240 => '86', //财务打款
            241 => '87', //回款确认
            242 => '88', //申请录入
            259 => '83', //人工审批（二审）
            244 => '92', //寄件登记
            245 => '93', //抄单登记
            248 => '94', //车辆GPS登记
            249 => '95', //抵押登记
            274 => '155', //申请件补件
            277 => '168', //申请打款
            279 => '174', //打款审核
        );
        $rolelist = $this->_role->getRole();
        if (isset($rolelist[env('VISIT_ROLE_ID')][$role_id]) || $role_id == env('VISIT_ROLE_ID')) {//判断是否是家访
            $work_role_ids = array(98);
        } else if (isset($rolelist[env('SALE_ROLE_ID')][$role_id]) || $role_id == env('SALE_ROLE_ID')) {//判断是否是销售
            $work_role_ids = array(72);
        } else {//其他角色
            $sql = "select * from v1_role_privilege where role_id = ?";
            $privileges = $this->_pdo->fetchAll($sql, array($role_id));
            foreach($privileges as $k=>$v) {
                if (isset($maps[$v['method_id']])) {
                    $work_role_ids[] = $maps[$v['method_id']];
                }
            }
        }
        $citys = array();
        $citys_failed = array();
        $tb_address_province = new T_address_province();
        $tb_address_city = new T_address_city();
        foreach ($province_names as $k => $v) {
            $code = $tb_address_province->getcodeByname(trim($v));
            if (!empty($code['code'])) {
                $p_citys = $tb_address_city->getcityBypcode($code['code']);
                foreach ($p_citys as $k2 => $v2) {
                    $citys[] = array(
                        'id' => $v2['id'],
                        'name' => $v2['name'],
                    );
                }
            } else {
                $citys_failed[] = $v;
            }
        }
        foreach ($city_names as $k => $v) {
            $code = $tb_address_city->getcodeByname(trim($v));
            if (!empty($code['code'])) {
                $citys[] = array(
                    'id' => $code['id'],
                    'name' => $code['name'],
                );
            } else {
                $citys_failed[] = $v;
            }
        }
        $tb_user_role_area_privilege = new Anjie_user_role_area_privilege();
        $work_role_ids = array_unique($work_role_ids);
        foreach($work_role_ids as $k=>$v) {
            $tb_user_role_area_privilege->setPrivilege($user_id, $v, $citys);
        }
        return $this->_common->output(true, Constant::ERR_SUCCESS_NO, "以下未成功：" . implode(',', $citys_failed));    //成功
    }

}