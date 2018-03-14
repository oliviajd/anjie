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
    //个人信息相关接口
    Route::post('auth/login','AuthController@index');    //登录
    Route::get('auth/login','AuthController@index');    //登录
    Route::post('auth/applogin','AuthController@applogin');    //登录
    Route::get('auth/applogin','AuthController@applogin');    //登录
    Route::post('auth/changepasswordpost','AuthController@changepasswordpost');    //修改密码
    Route::post('auth/register','AuthController@register');    //注册
    Route::get('auth/register','AuthController@register');    //注册
    Route::get('auth/sendmessage','AuthController@sendmessage');    //注册时发送验证码
    Route::post('auth/sendmessage','AuthController@sendmessage');    //注册时发送验证码
    Route::get('auth/checkmessage','AuthController@checkmessage');    //验证验证码是否正确
    Route::post('auth/checkmessage','AuthController@checkmessage');    //验证验证码是否正确
    Route::get('auth/resetpasswd','AuthController@resetpasswd');    //重置密码
    Route::post('auth/resetpasswd','AuthController@resetpasswd');    //重置密码
    Route::get('auth/userinfo','AuthController@userinfo');    //获取用户信息
    Route::post('auth/userinfo','AuthController@userinfo');    //获取用户信息
    Route::get('auth/setheadportrait','AuthController@setheadportrait');    //修改头像
    Route::post('auth/setheadportrait','AuthController@setheadportrait');    //修改头像
    Route::get('auth/setaddress','AuthController@setaddress');    //修改地址
    Route::post('auth/setaddress','AuthController@setaddress');    //修改地址
    Route::get('auth/checktoken','AuthController@checktoken');    //验证token有没有过期
    Route::post('auth/checktoken','AuthController@checktoken');    //验证token有没有过期
});
Auth::routes();
