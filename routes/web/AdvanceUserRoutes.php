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
Route::group(['prefix' => 'advance', 'middleware' => 'userLogin'], function () {
     //用户提交申请
    Route::post('user/applysubmit','AdvanceController@applysubmit');
    //用户获取用户垫资车辆列表
    Route::get('user/getborrowplan','AdvanceController@getborrowplan');
    //用户获取审批列表
    Route::get('user/applylists','AdvanceController@applylists');
    //用户认证之后重新提交申请
    Route::post('user/submitagain','AdvanceController@submitagain');
    //车商根据workid获取工作流程
    Route::get('user/getworkflow','AdvanceController@getworkflow');
});
Auth::routes();
