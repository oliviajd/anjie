    @include('common.title')
    <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
                <!-- Content Header (Page header) -->
                <!-- <section class="content-header">
                    <h1>
                        操作日志查询
                        <small>action logs</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/Admin">系统管理</a></li>
                        <li class="/Admin/system/actionlog">操作日志查询</li>
                    </ol>
                </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-4 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">工行信用卡查询</div>
                <div class="panel-body form-horizontal">
                        <div class="form-group">
                            <label for="cutomer_name" class="col-md-4 control-label">姓名</label>
                            <div class="col-md-6">
                                <input id="cutomer_name" type="text" class="form-control cutomer_name" name="cutomer_name" placeholder="请输入用户姓名" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="certificate_number" class="col-md-4 control-label">身份证号</label>
                            <div class="col-md-6">
                                <input id="certificate_number" type="text" class="form-control certificate_number" name="certificate_number"  placeholder="请输入用户身份证号码">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary bankdataquery" style="width:120px;height: 35px;">
                                    查询
                                </button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">工行信用卡数据查询结果</div>
                <div class="panel-body form-horizontal">
                        <table id="example1 bankdataquery_table" class="table table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <td class="col-md-3">姓名</td>
                                    <td class="col-md-8" id="name"></td>
                                </tr>
                                <tr>
                                    <td>身份证号</td>
                                    <td id="id_card"></td>
                                </tr>
                                <tr>
                                    <td>汽车专用卡卡号</td>
                                    <td id="car_no"></td>
                                </tr>
                                <tr>
                                    <td>本期最优还款额</td>
                                    <td id="huankuan"></td>
                                </tr>
                                <tr>
                                    <td>当前逾期期限段</td>
                                    <td id="yuqi_status"></td>
                                </tr>
                                <tr>
                                    <td>逾期天数</td>
                                    <td id="yuqi_days"></td>
                                </tr>
                                <tr>
                                    <td>逾期金额</td>
                                    <td id="yuqi_money"></td>
                                </tr>
                                <tr>
                                    <td>分期付款授权累计</td>
                                    <td id="shouquan_money"></td>
                                </tr>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
        @include('common/common')
        <script src="/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/moment/moment.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.bankdataquery').click(function(){
                    $.ajax({
                       type:'post',
                       url:'/Admin/system/listbankdataquery',
                       dataType: 'json',
                       data:{
                        'token': USER.get_token(),
                        'name' : $('#cutomer_name').val(),      //姓名
                        'id_card' : $('#certificate_number').val(),      //身份证号码
                       },
                       success:function(data){
                        if (data.error_no !== 200) {
                            alert(data.error_msg);
                        } else {
                           $('#name').text(data.result.name);
                           $('#id_card').text(data.result.id_card);
                           $('#car_no').text(data.result.car_no);
                           $('#huankuan').text(data.result.huankuan);
                           $('#yuqi_status').text(data.result.yuqi_status);
                           $('#yuqi_days').text(data.result.yuqi_days);
                           $('#yuqi_money').text(data.result.yuqi_money);
                           $('#shouquan_money').text(data.result.shouquan_money);
                        }
                       }
                    });
                });
             })
         </script>
@endsection