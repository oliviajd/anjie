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
    Route::get('work/creditrequest','WorkController@creditrequest');       //listbusiness    
    Route::get('work/inquire','WorkController@inquire');       //人工审核
    Route::get('work/detailinquire','WorkController@detailinquire');       //人工审核
    Route::get('work/artificialone','WorkController@artificialone');       //人工审核
    Route::get('work/artificialtwo','WorkController@artificialtwo');       //人工审核
    Route::get('work/detailartificialone','WorkController@detailartificialone');       //人工审核
    Route::get('work/detailartificialtwo','WorkController@detailartificialtwo');       //人工审核
    Route::get('work/applyremittance','WorkController@applyremittance');       //申请打款
    Route::get('work/moneyaudit','WorkController@moneyaudit');       //打款审核
    Route::get('work/finance','WorkController@finance');       //人工审核
    Route::get('work/returnmoney','WorkController@returnmoney');       //人工审核
    Route::get('work/inputrequest','WorkController@inputrequest');       //人工审核
    Route::get('work/detailinputrequest','WorkController@detailinputrequest');       //人工审核
    Route::get('work/taskback','WorkController@taskback');       //人工审核
    Route::get('work/detailtaskback','WorkController@detailtaskback');       //人工审核
    Route::get('work/sendtask','WorkController@sendtask');       //寄件登记
    Route::get('work/copytask','WorkController@copytask');       //抄单登记
    Route::get('work/gps','WorkController@gps');       //GPS登记
    Route::get('work/mortgage','WorkController@mortgage');       //抵押登记
    Route::get('work/taskbackmanage','WorkController@taskbackmanage');       //捞件管理
    Route::get('work/detailtaskbackmanage','WorkController@detailtaskbackmanage');       //捞件管理
    Route::get('work/taskget','WorkController@taskget');       //捞件管理
    Route::get('work/taskquery','WorkController@taskquery');       //申请件查询
    Route::get('work/taskquerypartition','WorkController@taskquerypartition');       //申请件查询（分区）
    Route::get('work/detailtaskquerypartition','WorkController@detailtaskquerypartition');       //申请件查询（分区）
    Route::get('work/taskquerymore','WorkController@taskquerymore');       //申请件查询
    Route::get('work/detailtaskquery','WorkController@detailtaskquery');       //申请件查询-进阶
    Route::get('work/taskqueryself','WorkController@taskqueryself');       //申请件查询,只查自己的
    Route::get('work/detailtaskqueryself','WorkController@detailtaskqueryself');       //申请件查询,只查自己的
    Route::get('work/salessupplement','WorkController@salessupplement');       //销售补件列表
    Route::get('work/detailsalessupplement','WorkController@detailsalessupplement');       //销售补件详情 
    Route::get('work/beforedata','WorkController@beforedata');       //老数据查询
    Route::post('work/beforedata','WorkController@beforedata');       //老数据查询
    Route::get('work/detailbeforedata','WorkController@detailbeforedata');       //老数据查询
    Route::post('work/detailbeforedata','WorkController@detailbeforedata');       //老数据查询
    Route::get('work/getshow','WorkController@getshow');       //获取展示的列表
    Route::post('work/getshow','WorkController@getshow');       //获取展示的列表
    Route::get('work/taskqueryprovince','WorkController@taskqueryprovince');       //申请件查询（省级）
    Route::get('work/detailtaskqueryprovince','WorkController@detailtaskqueryprovince');       //申请件查询（省级）
});
Auth::routes();
