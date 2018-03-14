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

//移动端接口
Route::group(['prefix' => 'Api', 'middleware' => 'phoneApi'], function () {
    Route::get('workflow/pickupvisit','WorkflowController@pickupvisit');       //家访中的认领
    Route::post('workflow/pickupvisit','WorkflowController@pickupvisit');       //家访中的认领
    Route::get('workflow/giveup','WorkflowController@giveup');       //丢弃
    Route::post('workflow/giveup','WorkflowController@giveup');       //丢弃
    Route::get('workflow/workflowvisit','WorkflowController@workflowvisit');       //引擎中家访接口
    Route::post('workflow/workflowvisit','WorkflowController@workflowvisit');       //引擎中家访接口
    Route::get('workflow/workflowitem','WorkflowController@workflowitem');       //引擎平台修改某个件的任务状态
    Route::post('workflow/workflowitem','WorkflowController@workflowitem');       //引擎平台修改某个件的任务状态
    Route::get('workflow/lists','WorkflowController@lists');       //家访列表
    Route::post('workflow/lists','WorkflowController@lists');       //家访列表
    Route::get('workflow/getworkinfo','WorkflowController@getworkinfo');       //获取工作信息
    Route::post('workflow/getworkinfo','WorkflowController@getworkinfo');       //获取工作信息
    Route::get('workflow/getaccountlist','WorkflowController@getaccountlist');       //查询工作对应日期数量
    Route::post('workflow/getaccountlist','WorkflowController@getaccountlist');       //查询工作对应日期数量
    Route::get('workflow/backvisit','WorkflowController@backvisit');       //退回任务
    Route::post('workflow/backvisit','WorkflowController@backvisit');       //退回任务
    Route::get('workflow/assignvisit','WorkflowController@assignvisit');       //分派任务
    Route::post('workflow/assignvisit','WorkflowController@assignvisit');       //分派任务
    Route::get('workflow/refusevisit','WorkflowController@refusevisit');       //拒件任务
    Route::post('workflow/refusevisit','WorkflowController@refusevisit');       //拒件任务
    Route::get('workflow/completesupplement','WorkflowController@completesupplement');       //完成补件
    Route::post('workflow/completesupplement','WorkflowController@completesupplement');       //完成补件
    Route::get('workflow/listmessage','WorkflowController@listmessage');       //根据user_id获取家访的消息列表
    Route::post('workflow/listmessage','WorkflowController@listmessage');
    Route::get('workflow/visitgiveup','WorkflowController@visitgiveup');       //放弃家访任务
    Route::post('workflow/visitgiveup','WorkflowController@visitgiveup');
    //根据user_id获取家访的消息列表
    Route::get('workflow/listinquire','WorkflowController@listinquire');       //列出征信报告的件
    Route::post('workflow/listinquire','WorkflowController@listinquire');       //列出征信报告的件
    //添加面签视频到工作流中
    Route::post('workflow/facevideo','WorkflowController@facevideo');
    //销售信息列表
    Route::get('workflow/sellermessage','WorkflowController@sellermessage');
    //家访信息列表
    ROute::get('workflow/homemessage','WorkflowController@homemessage');
});
Auth::routes();
