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
    Route::post('jcr/login','JcrController@login');       //左边菜单栏
    Route::get('jcr/login','JcrController@login');       //左边菜单栏
    Route::post('jcr/regmobilecode','JcrController@regmobilecode');       //注册时发送验证码
    Route::get('jcr/regmobilecode','JcrController@regmobilecode');       //注册时发送验证码
    Route::post('jcr/reg','JcrController@reg');       //注册
    Route::get('jcr/reg','JcrController@reg');       //注册
    Route::post('jcr/mobilecode','JcrController@mobilecode');       //给已有用户发送手机验证码
    Route::get('jcr/mobilecode','JcrController@mobilecode');       //给已有用户发送手机验证码
    Route::post('jcr/baaccountopen','JcrController@baaccountopen');       //银行存管开户
    Route::get('jcr/baaccountopen','JcrController@baaccountopen');       //银行存管开户
    Route::post('jcr/basmscodeapply','JcrController@basmscodeapply');       //银行存管发送验证码
    Route::get('jcr/basmscodeapply','JcrController@basmscodeapply');       //银行存管发送验证码
    Route::post('jcr/userinfo','JcrController@userinfo');       //获取用户信息
    Route::get('jcr/userinfo','JcrController@userinfo');       //获取用户信息
    Route::post('jcr/setheadportrait','JcrController@setheadportrait');       //设置用户头像
    Route::get('jcr/setheadportrait','JcrController@setheadportrait');       //设置用户头像
    Route::post('jcr/jcrverify','JcrController@jcrverify');       //车商认证申请
    Route::get('jcr/jcrverify','JcrController@jcrverify');       //车商认证申请
    Route::post('jcr/sendmessage','JcrController@sendmessage');       //重新认证时发送验证码
    Route::get('jcr/sendmessage','JcrController@sendmessage');       //重新认证时发送验证码
    Route::post('jcr/getverifyinfo','JcrController@getverifyinfo');       //获取认证信息
    Route::get('jcr/getverifyinfo','JcrController@getverifyinfo');       //获取认证信息
    Route::post('jcr/reverify','JcrController@reverify');       //重新认证
    Route::get('jcr/reverify','JcrController@reverify');       //重新认证
    Route::post('jcr/suggestionssubmit','JcrController@suggestionssubmit');       //意见反馈
    Route::get('jcr/suggestionssubmit','JcrController@suggestionssubmit');       //意见反馈
    Route::post('jcr/csrrequestrecord','JcrController@csrrequestrecord');       //融资申请记录
    Route::get('jcr/csrrequestrecord','JcrController@csrrequestrecord');       //融资申请记录
    Route::post('jcr/borrowplan','JcrController@borrowplan');       //还款计划
    Route::get('jcr/borrowplan','JcrController@borrowplan');       //还款计划
    Route::get('jcr/gettotalfinancingnumber','JcrController@gettotalfinancingnumber');       //获取总融资人数
    Route::post('jcr/gettotalfinancingnumber','JcrController@gettotalfinancingnumber');       //获取总融资人数
    Route::get('jcr/gethistorylist','JcrController@gethistorylist');       //估价历史列表
    Route::post('jcr/gethistorylist','JcrController@gethistorylist');       //估价历史列表
    Route::get('jcr/gethistorydetail','JcrController@gethistorydetail');       //估价历史详情
    Route::post('jcr/gethistorydetail','JcrController@gethistorydetail');       //估价历史详情
    Route::get('jcr/reglogin','JcrController@reglogin');       //注册登录接口
    Route::post('jcr/reglogin','JcrController@reglogin');       //注册登录接口
    //车300的接口
    Route::post('jcr/getallcity','JcrController@getallcity');       //获取所有的城市
    Route::get('jcr/getallcity','JcrController@getallcity');       //获取所有的城市
    Route::post('jcr/getcarbrandlist','JcrController@getcarbrandlist');       //获取所有的品牌列表
    Route::get('jcr/getcarbrandlist','JcrController@getcarbrandlist');       //获取所有的品牌列表
    Route::post('jcr/getcarserieslist','JcrController@getcarserieslist');       //获取车系列表
    Route::get('jcr/getcarserieslist','JcrController@getcarserieslist');       //获取车系列表
    Route::post('jcr/getcarmodellist','JcrController@getcarmodellist');       //获取车型列表
    Route::get('jcr/getcarmodellist','JcrController@getcarmodellist');       //获取车型列表
    Route::post('jcr/identifymodelbyvin','JcrController@identifymodelbyvin');       //基于VIN码获取车型
    Route::get('jcr/identifymodelbyvin','JcrController@identifymodelbyvin');       //基于VIN码获取车型
    Route::post('jcr/getusedcarprice','JcrController@getusedcarprice');       //车辆估值接口
    Route::get('jcr/getusedcarprice','JcrController@getusedcarprice');       //车辆估值接口
    //文件上传
    Route::post('jcr/file/jcrupload','JcrController@upload');       //车辆估值接口
});
Auth::routes();
