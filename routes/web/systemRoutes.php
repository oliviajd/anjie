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
    Route::get('system/loginlog','SystemController@loginlog');       //listbusiness
    Route::get('system/getloginlog','SystemController@getloginlog');       //listbusiness
    Route::get('system/actionlog','SystemController@actionlog');       //listbusiness
    Route::get('system/getactionlog','SystemController@getactionlog');       //listbusiness
    Route::get('system/manaudit','SystemController@manaudit');       //人工审核
    Route::get('system/listmanaudit','SystemController@listmanaudit');       //人工审核
    Route::post('system/listmanaudit','SystemController@listmanaudit');       //人工审核
    Route::post('system/updatemanaudit','SystemController@updatemanaudit');       //人工审核
    Route::get('system/bankdataquery','SystemController@bankdataquery');       //工行数据查询
    Route::get('system/listbankdataquery','SystemController@listbankdataquery');       //查询工行数据接口
    Route::post('system/listbankdataquery','SystemController@listbankdataquery');       //查询工行数据接口
});
//移动端接口
Route::group(['prefix' => 'Api', 'middleware' => 'phoneApi'], function () {
    //地区相关接口
    Route::post('system/getprovince','SystemController@getprovince');    //获取省份
    Route::post('system/getcity','SystemController@getcity');    //获取市
    Route::post('system/gettown','SystemController@gettown');    //获取区
    Route::post('system/getcitybyname','SystemController@getcitybyname');    //通过省的名称获取市
    Route::post('system/gettownbyname','SystemController@gettownbyname');    //通过市的名称获取区
   
    Route::get('privilege/sidebar','PrivilegeController@sidebar');       //左边菜单栏
    Route::post('privilege/sidebar','PrivilegeController@sidebar');       //左边菜单栏
});
Auth::routes();
