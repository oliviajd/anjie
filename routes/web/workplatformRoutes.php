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
     //家访相关接口
    Route::post('workplatform/setconstractno','WorkplatformController@setconstractno');    //写入合同编号
    Route::post('workplatform/setvisitdescrip','WorkplatformController@setvisitdescrip');    //写入家访总结
    Route::post('workplatform/setcustomeraddress','WorkplatformController@setcustomeraddress');    //写入客户地址
    Route::post('workplatform/setvisitlocation','WorkplatformController@setvisitlocation');    //写入家访员位置
    Route::post('workplatform/beginendvisit','WorkplatformController@beginendvisit');    //开始结束家访
    Route::post('workplatform/creditrequestsubmit','WorkplatformController@creditrequestsubmit');       //征信申请提交
    Route::get('workplatform/creditrequestsubmit','WorkplatformController@creditrequestsubmit');       //征信申请提交
    Route::post('workplatform/listworkplatform','WorkplatformController@listworkplatform');       //列出征信查询的所有件
    Route::get('workplatform/listworkplatform','WorkplatformController@listworkplatform');       //列出征信查询的所有件
    Route::post('workplatform/taskquery','WorkplatformController@taskquery');       //列出申请件查询的所有件
    Route::get('workplatform/taskquery','WorkplatformController@taskquery');       //列出申请件查询的所有件
    Route::post('workplatform/taskquerypartition','WorkplatformController@taskquerypartition');       //列出申请件查询的所有件（分区）
    Route::get('workplatform/taskquerypartition','WorkplatformController@taskquerypartition');       //列出申请件查询的所有件（分区）
    Route::post('workplatform/getworkinfo','WorkplatformController@getWorkinfo');       //获取申请件信息
    Route::get('workplatform/getworkinfo','WorkplatformController@getWorkinfo');       //获取申请件信息
    Route::post('workplatform/pickup','WorkplatformController@pickup');       //获取申请件信息
    Route::get('workplatform/pickup','WorkplatformController@pickup');       //获取申请件信息
    Route::post('workplatform/inquire','WorkplatformController@inquire');       //获取申请件信息
    Route::get('workplatform/inquire','WorkplatformController@inquire');       //获取申请件信息
    Route::post('workplatform/inputrequest','WorkplatformController@inputrequest');       //申请录入
    Route::get('workplatform/inputrequest','WorkplatformController@inputrequest');       //申请录入
    Route::post('workplatform/artificial','WorkplatformController@artificial');       //人工审批
    Route::get('workplatform/artificial','WorkplatformController@artificial');       //人工审批
    Route::post('workplatform/applyremittance','WorkplatformController@applyremittance');       //申请打款
    Route::get('workplatform/applyremittance','WorkplatformController@applyremittance');       //申请打款
    Route::post('workplatform/moneyaudit','WorkplatformController@moneyaudit');       //打款审核
    Route::get('workplatform/moneyaudit','WorkplatformController@moneyaudit');       //打款审核
    Route::post('workplatform/finance','WorkplatformController@finance');       //财务打款
    Route::get('workplatform/finance','WorkplatformController@finance');       //财务打款
    Route::post('workplatform/returnmoney','WorkplatformController@returnmoney');       //回款确认
    Route::get('workplatform/returnmoney','WorkplatformController@returnmoney');       //回款确认
    Route::post('workplatform/courier','WorkplatformController@courier');       //寄件登记
    Route::get('workplatform/courier','WorkplatformController@courier');       //寄件登记
    Route::post('workplatform/copytask','WorkplatformController@copytask');       //抄单登记
    Route::get('workplatform/copytask','WorkplatformController@copytask');       //抄单登记
    Route::post('workplatform/gps','WorkplatformController@gps');       //车辆gps登记
    Route::get('workplatform/gps','WorkplatformController@gps');       //车辆gps登记
    Route::post('workplatform/mortgage','WorkplatformController@mortgage');       //抵押登记
    Route::get('workplatform/mortgage','WorkplatformController@mortgage');       //抵押登记
    Route::post('workplatform/listtasks','WorkplatformController@listtasks');       //列举任务流
    Route::get('workplatform/listtasks','WorkplatformController@listtasks');       //列举任务流
    Route::post('workplatform/taskqueryself','WorkplatformController@taskqueryself');       //列出申请件查询的自己的件
    Route::get('workplatform/taskqueryself','WorkplatformController@taskqueryself');       //列出申请件查询的自己的件
    Route::post('workplatform/salessupplement','WorkplatformController@salessupplement');       //销售补件
    Route::get('workplatform/salessupplement','WorkplatformController@salessupplement');       //销售补件
    Route::post('workplatform/workflowpickup','WorkplatformController@workflowpickup');       //工作流引擎认领
    Route::get('workplatform/workflowpickup','WorkplatformController@workflowpickup');       //工作流引擎认领
    Route::post('workplatform/bankpickupandpush','WorkplatformController@bankpickupandpush');       //征信申请推送银行
    Route::get('workplatform/bankpickupandpush','WorkplatformController@bankpickupandpush');       //征信申请推送银行
    Route::post('workplatform/creditquerylog','WorkplatformController@creditquerylog');       //征信查询的日志查询
    Route::get('workplatform/creditquerylog','WorkplatformController@creditquerylog');       //征信查询的日志查询
    Route::post('workplatform/transtobank','WorkplatformController@transtobank');       //人工审批后将信息传至银行
    Route::get('workplatform/transtobank','WorkplatformController@transtobank');       //人工审批后将信息传至银行
    Route::post('workplatform/suppletobank','WorkplatformController@suppletobank');       //补录到银行的接口
    Route::get('workplatform/suppletobank','WorkplatformController@suppletobank');       //补录到银行的接口
    Route::post('workplatform/listsubordinatetask','WorkplatformController@listsubordinatetask');       //人工审批后将信息传至银行
    Route::get('workplatform/listsubordinatetask','WorkplatformController@listsubordinatetask');       //人工审批后将信息传至银行
    Route::post('workplatform/endwork','WorkplatformController@endwork');       //结束审批流
    Route::get('workplatform/endwork','WorkplatformController@endwork');       //结束审批流
    Route::post('workplatform/editwork','WorkplatformController@editwork');       //修改申请件信息
    Route::get('workplatform/editwork','WorkplatformController@editwork');       //修改申请件信息
    Route::post('workplatform/beforedata','WorkplatformController@beforedata');       //老系统数据
    Route::get('workplatform/beforedata','WorkplatformController@beforedata');        //老系统数据
    Route::post('workplatform/getyuqi','WorkplatformController@getyuqi');       //获取逾期列表
    Route::get('workplatform/getyuqi','WorkplatformController@getyuqi');        //获取逾期列表
    Route::post('workplatform/gethuankuan','WorkplatformController@gethuankuan');       //获取还款列表
    Route::get('workplatform/gethuankuan','WorkplatformController@gethuankuan');        //获取还款列表
    Route::post('workplatform/bairongcredit','WorkplatformController@bairongcredit');       //百融失信接口
    Route::get('workplatform/bairongcredit','WorkplatformController@bairongcredit');        //百融失信接口
    Route::post('workplatform/taskqueryprovince','WorkplatformController@taskqueryprovince');       //列出申请件查询（省级）
    Route::get('workplatform/taskqueryprovince','WorkplatformController@taskqueryprovince');       //列出申请件查询（省级）
    Route::post('workplatform/transtozhaohuibank','WorkplatformController@transtozhaohuibank');       //传输到浙江工行
    Route::get('workplatform/transtozhaohuibank','WorkplatformController@transtozhaohuibank');       //传输到浙江工行
    Route::post('workplatform/suppletozhaohuibank','WorkplatformController@suppletozhaohuibank');       //补录朝晖支行
    Route::get('workplatform/suppletozhaohuibank','WorkplatformController@suppletozhaohuibank');       //补录朝晖支行
});
Auth::routes();
