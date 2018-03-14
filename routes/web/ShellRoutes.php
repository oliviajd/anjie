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
Route::group(['prefix' => 'shell', 'middleware' => 'shell'], function () {
    //获取没有传送成功到银行的资料继续上传
    Route::get('bank/uploadfile','ShellController@shelltranstobank');
});
Auth::routes();
