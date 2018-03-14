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
    //车源
    Route::get('car/getbrands','CarController@getBrands');       //获取汽车品牌信息
    Route::get('car/getcarlists','CarController@getCarLists');       //获取汽车列表
    Route::get('car/getcardetail','CarController@getCarDetail');       //获取汽车详情
    Route::post('car/getbrands','CarController@getBrands');       //获取汽车品牌信息
    Route::post('car/getcarlists','CarController@getCarLists');       //获取汽车列表
    Route::post('car/getcardetail','CarController@getCarDetail');       //获取汽车详情
    Route::get('car/getrecommend','CarController@getCarRecommend');       //获取汽车推荐
});
Auth::routes();
