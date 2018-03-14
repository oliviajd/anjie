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

//移动端垫资相关接口
Route::group(['prefix' => 'advance', 'middleware' =>'managerLogin'], function () {
    //业务员获取垫资申请列表
    Route::get('manager/lists','AdvanceController@lists');
    //业务员获取申请详情
    Route::get('manager/getinfo','AdvanceController@getinfo');
    //业务员提交征信
    Route::post('manager/creditsubmit','AdvanceController@creditsubmit');
    //业务员获取用户信息
    Route::get('manager/getuserinfo','AdvanceController@getuserinfo');
    //业务员根据workid获取工作流程
    Route::get('manager/getworkflow','AdvanceController@getworkflow');
});
Auth::routes();
