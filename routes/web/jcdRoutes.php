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
//app端路由
Route::group(['prefix' => 'Jcr','middleware' => 'phoneApi'], function () {
    Route::post('jcd/listsverify','JcdController@listsverify');       //认证申请列表
    Route::get('jcd/listsverify','JcdController@listsverify');       //认证申请列表
    Route::post('jcd/handleverify','JcdController@handleverify');       //认证申请处理
    Route::get('jcd/handleverify','JcdController@handleverify');       //认证申请处理
    Route::post('jcd/getverifyinfo','JcdController@getverifyinfo');       //web上获取认证信息
    Route::get('jcd/getverifyinfo','JcdController@getverifyinfo');       //web上获取认证信息
    Route::post('jcr/csrrequest','JcrController@csrrequest');       //车商融申请
    Route::get('jcr/csrrequest','JcrController@csrrequest');       //车商融申请
    Route::post('jcd/getjcrlist','JcdController@getjcrlist');       //工作台列出所有件
    Route::get('jcd/getjcrlist','JcdController@getjcrlist');       //工作台列出所有件
    Route::post('jcd/pickup','JcdController@pickup');       //认领操作
    Route::get('jcd/pickup','JcdController@pickup');       //认领操作
    Route::post('jcd/workflowpickup','JcdController@workflowpickup');       //工作流认领
    Route::get('jcd/workflowpickup','JcdController@workflowpickup');       //工作流认领
    Route::post('jcd/csrrequestverify','JcdController@csrrequestverify');       //融资申请
    Route::get('jcd/csrrequestverify','JcdController@csrrequestverify');       //融资申请
    Route::post('jcd/listsverifyrecord','JcdController@listsverifyrecord');       //认证申请记录列表
    Route::get('jcd/listsverifyrecord','JcdController@listsverifyrecord');       //认证申请记录列表
    Route::post('jcd/listscsrrequestrecord','JcdController@listscsrrequestrecord');       //融资申请记录列表
    Route::get('jcd/listscsrrequestrecord','JcdController@listscsrrequestrecord');       //融资申请记录列表
    Route::post('jcd/billrequestverify','JcdController@billrequestverify');       //上标申请
    Route::get('jcd/billrequestverify','JcdController@billrequestverify');       //上标申请
    Route::post('jcd/listsbillrecord','JcdController@listsbillrecord');       //标的申请记录列表
    Route::get('jcd/listsbillrecord','JcdController@listsbillrecord');       //标的申请记录列表
    Route::post('jcd/getcsrinfo','JcdController@getcsrinfo');       //获取车商信息
    Route::get('jcd/getcsrinfo','JcdController@getcsrinfo');       //获取车商信息
    Route::post('jcd/billnotify','JcdController@billnotify');       //聚车金融标的状态推送
    Route::get('jcd/billnotify','JcdController@billnotify');       //聚车金融标的状态推送
    Route::post('jcd/borrowplan','JcdController@borrowplan');       //还款计划
    Route::get('jcd/borrowplan','JcdController@borrowplan');       //还款计划
    Route::post('jcd/transferlist','JcdController@transferlist');       //还款计划
    Route::get('jcd/transferlist','JcdController@transferlist');       //还款计划
    Route::post('jcd/transferhandle','JcdController@transferhandle');       //处理过户
    Route::get('jcd/transferhandle','JcdController@transferhandle');       //处理过户
    Route::get('jcd/updatecredit','JcdController@updateCredit');       //修改增信资料
    Route::post('jcd/updatecredit','JcdController@updateCredit');       //修改增信资料
    Route::get('jcd/carevaluaterecord','JcdController@carevaluaterecord');       //车商的估价列表
    Route::post('jcd/carevaluaterecord','JcdController@carevaluaterecord');       //车商的估价列表
    Route::get('jcd/cardealerrequestrecord','JcdController@cardealerrequestrecord');       //车商融资单
    Route::post('jcd/cardealerrequestrecord','JcdController@cardealerrequestrecord');       //车商融资单
    Route::get('jcd/listfunddetail','JcdController@listfunddetail');       //车商融资单
    Route::post('jcd/listfunddetail','JcdController@listfunddetail');       //车商融资单
    Route::get('jcd/getdealerinfo','JcdController@getdealerinfo');       //车商详情
    Route::post('jcd/getdealerinfo','JcdController@getdealerinfo');       //车商详情
    Route::post('jcd/updateimage','JcdController@updateImage');       //修改车商资料
    Route::get('jcd/updateimage','JcdController@updateImage');       //修改车商资料
});
Auth::routes();
