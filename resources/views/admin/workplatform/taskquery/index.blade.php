@include('common.title')
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
    <!-- Content Header (Page header) -->
                <!-- <section class="content-header">
                    <h1>
                        申请件查询
                        <small>task query</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/Admin/work/taskquery">申请件查询</a></li>
                        <li class="active">申请件查询</li>
                    </ol>
                </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                  <div class="box-body">
                  <div class="row margin-bottom">
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        申请编号：
                                    </span>
                                    <input type="text" name="request_no" class="form-control request_no" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        商户编号：
                                    </span>
                                    <input type="text" name="merchant_no" class="form-control merchant_no" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        客户姓名：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control customer_name" placeholder="">
                                </div>
                            </div>
                             <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        身份证号：
                                    </span>
                                    <input type="text" name="customer_certificate_number" class="form-control customer_certificate_number" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom">
                            
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        产品名称：
                                    </span>
                                    <select name="product_name" class="form-control product_name">
                                        <option value="">请选择</option>
                                        <option value="1">新车</option>
                                        <option value="2">二手车</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        贷款期数：
                                    </span>
                                    <select name="loan_date" class="form-control loan_date">
                                        <option value="">请选择</option>
                                        <option value="3个月">3个月</option>
                                        <option value="6个月">6个月</option>
                                        <option value="12个月">12个月</option>
                                        <option value="24个月">24个月</option>
                                        <option value="36个月">36个月</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        贷款申请号额度：
                                    </span>
                                    <input type="text" name="loan_prize" class="form-control loan_prize" placeholder="">
                                </div>
                            </div> -->
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        当前流程：
                                    </span>
                                    <select name="current_item_id" class="form-control current_item_id">
                                        <option value="">全部</option>
                                        <option value="|3|">银行征信查询</option>
                                        <option value="|7|">家访签约</option>
                                        <option value="|8|">申请录入</option>
                                        <option value="|9|">人工一审</option>
                                        <option value="|37|">人工二审</option>
                                        <option value="|47|">申请打款</option>
                                        <option value="|48|">打款审核</option>
                                        <option value="|39|">财务打款</option>
                                        <option value="|43|">GPS登记</option>
                                        <option value="|41|">寄件登记</option>
                                        <option value="|42|">抄单登记</option>
                                        <option value="|44|">抵押登记</option>
                                        <option value="|40|">回款确认</option>
                                        <option value="|45|">申请件补件</option>
                                    </select>
                                </div>
                            </div>
                             
                        </div>
                        <div class="row margin-bottom">
                            
                            <div class="col-xs-2">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        省份
                                    </span>
                                    <select name="province_name" id="province_name" class="form-control province_name">
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        市级
                                    </span>
                                    <input type="text" name="city_name" id="city_name" class="form-control city_name" placeholder="">
                                </div>
                            </div>
                             <div class="row margin-bottom">
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        状态：
                                    </span>
                                    <select name="item_status" class="form-control item_status">
                                        <option value="">请选择</option>
                                        <option value="1">未完成</option>
                                        <option value="2">已完成</option>
                                        <option value="3">已拒件</option>
                                    </select>
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
                    </div>
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <!-- <th>商户编号</th> -->
                            <th>申请编号</th>
                            <th>客户姓名</th>
                            <th>手机号码</th>
                            <th>产品名称</th>
                            <th>业务地区</th>
                            <th>所在省份</th>
                            <th>贷款金额</th>
                            <th>首付款金额</th>
                            <th>进件时间</th>
                            <th>当前流程</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <!-- <th>商户编号</th> -->
                            <th>申请编号</th>
                            <th>客户姓名</th>
                            <th>手机号码</th>
                            <th>产品名称</th>
                            <th>业务地区</th>
                            <th>所在省份</th>
                            <th>贷款金额</th>
                            <th>首付款金额</th>
                            <th>进件时间</th>
                            <th>当前流程</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('common.modal.normal')
@include('common/common')
<script src="/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="/bower_components/AdminLTE/plugins/moment/moment.min.js"></script>
<script src="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
 <script type="text/javascript">
    Fillprovince('province_name');    //填充省
    var token = USER.get_token();
    start_time ='';
    end_time ='';
    $('#reservationtime').on('apply.daterangepicker',function(ev, picker) {
         start_time =  Date.parse(picker.startDate) / 1000;
         end_time = Date.parse(picker.endDate) / 1000;;
    }); 
    $('#reservationtime').on('outside.daterangepicker',function(ev, picker) {
         start_time =  Date.parse(picker.startDate) / 1000;
         end_time = Date.parse(picker.endDate) / 1000;;
    });
    var task_deal = function (id) {
        window.location.href = '/Admin/work/detailtaskquery?id=' + id ;
    }
    $(document).ready(function(){
    	var oStartDate = new Date();var oEndDate = new Date();oStartDate.setDate(oStartDate.getDate() - 7);oStartDate.setHours(0,0,0);oEndDate.setHours(23,59,59);$('#reservationtime').daterangepicker({showDropdowns:true,timePicker24Hour:true,startDate:oStartDate,endDate:oEndDate,timePicker: true, timePickerIncrement: 1, format: 'MM/DD/YYYY h:mm A'});

        $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
            //重写ajax 分页参数
            json.page = Math.floor(json.start / json.length) + 1;
            json.size = json.length || 10;
            json.sort = '3';
        });
        $('input[name="search[keyword]"]').keyup(function (e) {
            if (e.which == 13) {
                $('#user_search').click();
            }
        });

        $('#user_search').click(function () {
            $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                //重写ajax 分页参数
                json.page = Math.floor(json.start / json.length) + 1;
                json.size = json.length || 10;
                //搜索条件
                json.request_no = $('.request_no').val();
                json.merchant_no = $('.merchant_no').val();
                json.customer_name = $('.customer_name').val();
                json.customer_certificate_number = $('.customer_certificate_number').val();
                json.product_name = $('.product_name').find("option:selected").val();
                json.province_name = $('.province_name option:selected').text(),         //省
                json.city_name = $('.city_name').val();
                json.loan_date = $('.loan_date').find("option:selected").val();
                // json.loan_prize = $('.loan_prize').val();
                json.current_item_id = $('.current_item_id').val();
                json.item_status = $('.item_status').val();
                json.start_time= start_time;
                json.end_time= end_time;
            });
            table.ajax.reload();
        });
        
        var table = $("#example1").DataTable({
            "processing": true,
            "serverSide": true,
            "paginate": true,
            'searching': false,
            "processing": true,
            "serverSide": true,
            "paginate": true,
            'searching': false,
            "oLanguage": DataTable_language,
            "ajax": {
                'url':"/Api/workplatform/taskquery",
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
                // {
                //     "orderable": false,
                //     "data": function (row) {
                //         return row.merchant_no;
                //     }
                // },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.request_no;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.customer_name;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.customer_telephone;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.product_name;
                    },
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.credit_city;
                    },
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.credit_province;
                    },
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.loan_prize;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.first_pay;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return time_to_str(row.create_time);
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.current_item_title;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        if (row.item_status == '1') {
                            return '未完成';
                        } else if(row.item_status =='2') {
                            return '已完成';
                        } else if(row.item_status =='3') {
                            return '已拒件';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return  '<button type="button" class="btn btn-default" onclick="task_deal(' + row.id +');">查看</button>';
                    }
                },
            ],
//                    "order": [[1, 'asc']]
        });

    })
 </script>
@endsection
