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
Route::group(['prefix' => 'ucenter', 'middleware' => 'visitor'], function () {
    //个人信息相关接口
    Route::get('user/gettransaction','UserController@gettransaction');//获取交易密码
    Route::post('user/settransaction','UserController@settransaction');//设置交易密码
});
Auth::routes();
