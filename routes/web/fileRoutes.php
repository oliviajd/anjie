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
    //文件相关的服务
    Route::get('file/index','FileController@index');
    Route::get('file/upload','FileController@upload');      //图片文件上传接口
    Route::post('file/upload','FileController@upload');
    Route::get('file/uploadfile','FileController@uploadfile');      //文件上传接口
    Route::post('file/uploadfile','FileController@uploadfile');
    Route::post('file/downloadfile','FileController@downloadfile');
    Route::post('file/getuploadfile','FileController@getUploadFile');   //文件断点上传
    Route::get('file/getuploadfile','FileController@getUploadFile');
    Route::post('file/make','FileController@make');    //产生文件id和token
    Route::get('file/make','FileController@make');
    Route::post('file/upyunpost','FileController@upyunpost');
    Route::post('file/bankpost','FileController@bankpost');
    Route::get('file/bankpost','FileController@bankpost');
    Route::post('file/bankimageconfirm','FileController@bankimageconfirm');
});
//移动端接口
Route::group(['prefix' => 'Api', 'middleware' => 'phoneApi'], function () {
    Route::post('file/getuploadfile','FileController@getUploadFile');             //文件断点上传
    Route::get('file/getuploadfile','FileController@getUploadFile');
    Route::post('file/make','FileController@make');   //产生文件id和token
    Route::get('file/make','FileController@make'); 
    Route::post('file/upload','FileController@upload');   //文件上传
    Route::post('file/hbuilderupload','FileController@hbuilderupload');   //hbuilderupload文件上传
    Route::get('file/hbuilderupload','FileController@hbuilderupload');   //hbuilderupload文件上传
     //图像相关接口
    Route::post('file/listimagetype','FileController@listimagetype');    //列出所有图片的类别
    Route::post('file/addimage','FileController@addimage');    //添加图像
    Route::post('file/addworkimages','FileController@addworkimages');    //申请件查询添加图片
    Route::get('file/addworkimages','FileController@addworkimages');    //申请件查询添加图片
    Route::post('file/deleteimage','FileController@deleteimage');    //删除图像、视频
    Route::post('file/listimages','FileController@listimages');    //列出图像、视频
    Route::get('file/listimages','FileController@listimages');    //列出图像、视频
    Route::get('file/packagefile','FileController@packagefile');   //文件打包下载
    Route::post('file/packagefile','FileController@packagefile');  //文件打包下载
});
Auth::routes();
