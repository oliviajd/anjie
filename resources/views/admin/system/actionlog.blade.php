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
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                <div class="row margin-bottom">
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        进件编号：
                                    </span>
                                    <input type="text" name="merchant_no" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        身份证号码：
                                    </span>
                                    <input type="text" name="customer_certificate_number" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <span class="input-group-addon">
                                        时间范围：
                                    </span>
                                    <input type="text" class="form-control pull-right" id="reservationtime">
                                    <span class="input-group-btn">
                                        <button type="button" id="user_search" class="btn btn-info btn-flat">查询</button>
                                    </span>
                                </div>
                            </div>

                        </div>
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>进件编号</th>
                                <th>身份证号码</th>
                                <th>操作人</th>
                                <th>操作类型</th>
                                <th>客户端IP</th>
                                <th>备注</th>
                                <th>操作时间</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>进件编号</th>
                                <th>身份证号码</th>
                                <th>操作人</th>
                                <th>操作类型</th>
                                <th>客户端IP</th>
                                <th>备注</th>
                                <th>操作时间</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
        @include('common/common')
        @include('common.modal.normal')
        <script src="/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/moment/moment.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                var oStartDate = new Date();var oEndDate = new Date();oStartDate.setDate(oStartDate.getDate() - 7);oStartDate.setHours(0,0,0);oEndDate.setHours(23,59,59);$('#reservationtime').daterangepicker({showDropdowns:true,timePicker24Hour:true,startDate:oStartDate,endDate:oEndDate,timePicker: true, timePickerIncrement: 1, format: 'MM/DD/YYYY h:mm A'});
                $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                    // console.log(json);
                    //重写ajax 分页参数
                    json.page = Math.floor(json.start / json.length) + 1;
                    json.size = json.length || 10;
                });
                $('#user_search').click(function () {
                    $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                        var date_range = $('#reservationtime').val().split(' - ');
                        //重写ajax 分页参数
                        json.page = Math.floor(json.start / json.length) + 1;
                        json.size = json.length || 10;
                        //搜索条件
                        json.merchant_no = $('input[name="merchant_no"]').val();
                        json.customer_certificate_number = $('input[name="customer_certificate_number"]').val();
                        json.start_time = new Date(date_range[0]).getTime()/1000;
                        json.end_time = new Date(date_range[1]).getTime()/1000+3600*24-1;
                    });
                    table.ajax.reload();
                });
                var table = $("#example1").DataTable({
                    "processing": true,
                    "serverSide": true,
                    "paginate": true,
                    'searching': false,
                    "ajax": {
                        'url': "/Admin/system/getactionlog",
                        'data': {
                            'token': USER.get_token()
                        },
                        'dataSrc': function (r) {
                            if (r.error_no !== 200) {
                                error_skip(r);
                            } else {
                                //重写ajax 返回的总数等参数
                                r.recordsTotal = r.result.total;
                                r.recordsFiltered = r.result.total;
                                return r.result.rows;
                            }
                        },
                    },
                    "columns": [
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.merchant_no;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.customer_certificate_number;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.username;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.requestname;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.ip;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return '';
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return time_to_str(row.create_time);
                            }
                        },
                    ],
                });
            })
         </script>
@endsection