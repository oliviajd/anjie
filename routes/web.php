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
Route::get('/', function () {
    return view('auth/login');
});
Route::post('auth/login','AuthController@index');    //登录
Route::get('auth/login','AuthController@index');    //登录
Route::post('auth/logout','AuthController@logout');   //退出
Route::get('auth/changepassword','AuthController@changepassword');   //修改密码
Route::post('auth/changepasswordpost','AuthController@changepasswordpost');   //修改密码
//pc端路由
Route::group(['prefix' => 'Admin', 'middleware' => 'webApi'], function () {
    Route::get('/test', 'admin\UserController@index');//url中使用的时候需要加Admin前缀,类前面需要加admin目录
    Route::get('/index',function (){
        return view('admin.admin');
    });
    Route::get('/',function (){
        return view('admin.admin');
    });
});
//移动端接口
Route::group(['prefix' => 'Api', 'middleware' => 'phoneApi'], function () {    
});
//不需要限制的接口
Route::group(['prefix' => 'Jcr','middleware' => 'phoneApi'], function () {
});
//不需要限制的接口
Route::group(['prefix' => 'system'], function () {
    Route::post('system/getprovince','SystemController@getprovince');    //获取省份
    Route::post('system/getcity','SystemController@getcity');    //获取市
    Route::post('system/gettown','SystemController@gettown');    //获取区
    Route::post('system/getmerchantclass','SystemController@getmerchantclass');    //获取来源
    Route::post('system/getproductname','SystemController@getproductname');    //获取产品名称
    Route::get('workflow/test','WorkflowController@test');    //获取省份
    Route::get('system/mail','SystemController@mail');    //邮箱
    Route::get('migration/getdata','MigrationController@getdata');    //邮箱
    Route::post('migration/getdata','MigrationController@getdata');    //获取产品名称
    Route::get('system/versioncontrol','SystemController@versioncontrol');    //版本控制
    Route::post('system/versioncontrol','SystemController@versioncontrol');    //版本控制
    Route::get('system/anjieversion','SystemController@anjieversion');    //版本控制
    Route::post('system/anjieversion','SystemController@anjieversion');    //版本控制
    Route::get('file/bankimageconfirm','FileController@bankimageconfirm');
    Route::get('file/getdiviquery','FileController@getdiviquery');  //审批指令查询
    Route::get('workplatform/retranstobank','WorkplatformController@retranstobank');  //审批指令查询
    Route::get('workplatform/ficopost','WorkplatformController@ficopost');  //fico数据查询
    Route::post('workplatform/ficopost','WorkplatformController@ficopost');  //fico数据查询
    Route::get('file/thumbfile','FileController@thumbfile');  //图片压缩
    Route::post('file/thumbfile','FileController@thumbfile');  //图片压缩
    Route::get('workplatform/getservice','WorkplatformController@getservice');  //获取服务
    Route::post('workplatform/getservice','WorkplatformController@getservice');  //获取服务
    Route::get('workplatform/fixcurrentid','WorkplatformController@fixcurrentid');  //fico数据查询
    Route::post('workplatform/fixcurrentid','WorkplatformController@fixcurrentid');  //fico数据查询
    Route::get('workflow/getrolelist','WorkflowController@getrolelist');  //获取角色列表
    Route::post('workflow/getrolelist','WorkflowController@getrolelist');  //获取角色列表
    Route::get('excel/export','ExcelController@export');  //excel导出
    Route::post('excel/export','ExcelController@export');  //excel导出
    Route::get('file/wordcreate','FileController@wordcreate');  //word生成
    Route::post('file/wordcreate','FileController@wordcreate');  //word生成
    Route::get('migration/migrationdata','MigrationController@migrationdata');    //数据迁移
    Route::get('migration/imigratefile','MigrationController@imigratefile');    //图片迁移
    Route::post('migration/imigratefile','MigrationController@imigratefile');    //图片迁移
    Route::get('image/imagemigration','ImageController@imagemigration');    //
    Route::post('image/imagemigration','ImageController@imagemigration');    //
    Route::post('image/script','ImageController@script');    //jiaoben
    Route::get('image/script','ImageController@script');
    Route::get('file/unlinkpackage','FileController@unlinkpackage');  //删除打包下载的文件
    Route::post('file/unlinkpackage','FileController@unlinkpackage');  //删除打包下载的文件
    Route::get('file/unlinkvideoimage','FileController@unlinkvideoimage');  //删除影像资料
    Route::post('file/unlinkvideoimage','FileController@unlinkvideoimage');  //删除影像资料
});
Auth::routes();
Route::get('/home', 'HomeController@index');
