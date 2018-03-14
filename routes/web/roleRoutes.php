<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//pc端路由
Route::group(['prefix' => 'Admin', 'middleware' => 'webApi'], function () {
    Route::get('role/index','RoleController@index');   //用户角色
    Route::get('role/roleset','RoleController@roleset');    //角色设置
    Route::get('role/adduser','RoleController@adduser');        //添加用户
    Route::post('role/adduserpost','RoleController@adduserpost');  //添加用户的post
    Route::post('role/lists','RoleController@lists');   //列出角色
    Route::get('role/lists','RoleController@lists');
    Route::post('role/listsusermodule','RoleController@listsUserModule');
    Route::get('role/listsusermodule','RoleController@listsUserModule');
    Route::post('role/listsusermethod','RoleController@listsUserMethod');
    Route::get('role/listsusermethod','RoleController@listsUserMethod');
    Route::post('role/deleteuser','RoleController@deleteuser');   //删除用户
    Route::get('role/deleteuser','RoleController@deleteuser');
    Route::post('role/listsuser','RoleController@listsuser');   //列出用户
    Route::get('role/listsuser','RoleController@listsuser');
    Route::get('role/roleedit','RoleController@roleedit');    //角色编辑和添加
    Route::get('role/editrole','RoleController@editrole');    //角色编辑和添加
    Route::get('role/roleadd','RoleController@roleadd');    //添加角色权限
    Route::post('role/roleadd','RoleController@roleadd');    //添加角色权限
    Route::get('role/updaterole','RoleController@updaterole');    //更新角色权限
    Route::post('role/updaterole','RoleController@updaterole');    //更新角色权限
    Route::get('role/get','RoleController@get');              //获取用户角色
    Route::post('role/get','RoleController@get');
    Route::get('role/permissiontree','RoleController@permissiontree');    //角色权限
    Route::post('role/permissiontree','RoleController@permissiontree');
    Route::post('role/treepermission','RoleController@treepermission');
    Route::get('role/treepermission','RoleController@treepermission');
    Route::get('role/listsmodule','RoleController@listsmodule');        //列出所有的module
    Route::post('role/listsmodule','RoleController@listsmodule');
    Route::get('role/listsmethod','RoleController@listsmethod');         //列出所有method
    Route::post('role/listsmethod','RoleController@listsmethod');
    Route::get('role/listsprivilege','RoleController@listsprivilege');         //列出所有privilege
    Route::post('role/listsprivilege','RoleController@listsprivilege');
    Route::get('role/update','RoleController@update');                   //更新角色
    Route::post('role/update','RoleController@update');
    Route::get('role/addrole','RoleController@addrole');                       //添加角色
    Route::post('role/addrole','RoleController@addrole');
    Route::get('role/finduser','RoleController@finduser');              //找用户
    Route::post('role/finduser','RoleController@finduser');
    Route::get('role/adduserrole','RoleController@adduserrole');       //添加用户角色
    Route::post('role/adduserrole','RoleController@adduserrole');
    Route::get('role/deleterole','RoleController@deleterole');         //删除角色
    Route::post('role/deleterole','RoleController@deleterole');
    // Route::post('role/test','RoleController@test');
    Route::post('role/listsalluser','RoleController@listsalluser');    //列出所有已添加的用户
    Route::get('role/listsalluser','RoleController@listsalluser');
    Route::get('role/edituser','RoleController@edituser');         //编辑用户
    Route::post('role/edituserpost','RoleController@edituserpost');   //编辑用户
    Route::post('role/deleteuserpost','RoleController@deleteuserpost');   //注销用户
    Route::get('role/deleteuserpost','RoleController@deleteuserpost');   //注销用户
});
//移动端接口
Route::group(['prefix' => 'Api', 'middleware' => 'phoneApi'], function () {
    //下属相关接口
    Route::get('role/listnoidentify','RoleController@listnoidentify');       //列出所有没有身份的人
    Route::post('role/listnoidentify','RoleController@listnoidentify');       //列出所有没有身份的人
    Route::get('role/listsubordinate','RoleController@listsubordinate');       //列出下属列表
    Route::post('role/listsubordinate','RoleController@listsubordinate');       //列出下属列表
    Route::get('role/getsubordinateinfo','RoleController@getsubordinateinfo');       //获取下属详情
    Route::post('role/getsubordinateinfo','RoleController@getsubordinateinfo');       //获取下属详情
    Route::get('role/listsubordinaterole','RoleController@listsubordinaterole');       //获取下级角色列表
    Route::post('role/listsubordinaterole','RoleController@listsubordinaterole');       //获取下级角色列表
    Route::post('role/addsubordinate','RoleController@addsubordinate');       //新增下属接口
    Route::post('role/addapprole','RoleController@addapprole');       //添加app角色
    Route::post('role/setUserRoleAreaPrivilege','RoleController@setUserRoleAreaPrivilege');       //设置用户地区权限
});
Auth::routes();
