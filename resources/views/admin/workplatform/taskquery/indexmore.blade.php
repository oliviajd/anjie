@extends('layouts.admin_template')

@section('content')
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
<div class="content-wrappers">
                <!-- Content Header (Page header) -->
               <!--  <section class="content-header">
                    <h1>
                        申请件查询-进阶查询
                        <small>task query</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/Admin/work/taskquery">申请件查询</a></li>
                        <li class="active">申请件查询-进阶查询</li>
                    </ol>
                </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                <div  class="row">
                     <div class="col-md-3">
                        <button type="button" class="status btn btn-primary wait_handle_button" value= "1">待处理案件</button>
                        <button type="button" class="status btn btn-default handle_button" value= "2">待认领案件</button>
                    </div>
                  </div>
                  <div class="box-body">
                  <div class="row margin-bottom">
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        申请编号：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        商户编号：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        客户姓名：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                             <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        身份证号：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom"> 
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        手机号码：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        婚姻状况：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="0">请选择</option>
                                        <option value="1">未婚</option>
                                        <option value="2">已婚</option>
                                        <option value="3">其他</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        教育程度：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="0">请选择</option>
                                        <option value="1">硕士研究生及以上</option>
                                        <option value="2">大学本科</option>
                                        <option value="3">大学专科</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        客户户籍：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="0">请选择</option>
                                        <option value="1">北京</option>
                                        <option value="2">天津</option>
                                        <option value="3">上海</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom">
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        雇用类型：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">受薪人士</option>
                                        <option value="3">自雇人士</option>
                                        <option value="4">应届毕业生</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        单位名称：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        行业性质：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">制造业</option>
                                        <option value="3">批发业</option>
                                        <option value="4">金融业</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        经济类型：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">政府机关</option>
                                        <option value="3">国有企业</option>
                                        <option value="4">个体</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom">
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        进件模式：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">在线申请</option>
                                        <option value="3">线下网点</option>
                                        <option value="4">联盟商户</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        客户分类：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">普通客户</option>
                                        <option value="3">重点行业客户</option>
                                        <option value="4">重点客户</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        产品名称：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="0">请选择</option>
                                        <option value="1">新车A</option>
                                        <option value="2">新车B</option>
                                        <option value="3">旧车A</option>
                                        <option value="4">旧车B</option>
                                    </select>
                                </div>
                            </div>
                           <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        贷款期数：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">3个月</option>
                                        <option value="3">6个月</option>
                                        <option value="4">12个月</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom"> 
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        贷款申请额度：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        当前流程：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">资质预审</option>
                                        <option value="3">申请录入</option>
                                        <option value="4">面签</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        状态：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">资质预审</option>
                                        <option value="3">申请录入</option>
                                        <option value="4">面签</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        优先级：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="0">请选择</option>
                                        <option value="1">高</option>
                                        <option value="2">中</option>
                                        <option value="3">低</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom"> 
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        客户经理：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        当前执行人：
                                    </span>
                                    <input type="text" name="customer_name" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        商户：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="1">请选择</option>
                                        <option value="2">新易贷信用贷款</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        网点：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="0">请选择</option>
                                        <option value="1">自营网点</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom"> 
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                       区域：
                                    </span>
                                    <select name="search[status]" class="form-control">
                                        <option value="0">请选择</option>
                                        <option value="1">北京</option>
                                        <option value="2">天津</option>
                                        <option value="3">上海</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <span class="input-group-addon">
                                        进件时间：
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
                                <th>商户编号</th>
                                <th>客户姓名</th>
                                <th>产品名称</th>
                                <th>贷款金额</th>
                                <th>贷款期限</th>
                                <th>客户经理</th>
                                <th>当前执行人</th>
                                <th>当前流程</th>
                                <th>状态</th>
                                <th>进件时间</th>
                                <th>状态</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>商户编号</th>
                                <th>客户姓名</th>
                                <th>产品名称</th>
                                <th>贷款金额</th>
                                <th>贷款期限</th>
                                <th>客户经理</th>
                                <th>当前执行人</th>
                                <th>当前流程</th>
                                <th>状态</th>
                                <th>进件时间</th>
                                <th>状态</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
        <script src="/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/moment/moment.min.js"></script>
        <script src="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
 <script type="text/javascript">
    var token = USER.get_token();
    var task_deal = function (obj) {
        window.location.href = '/Admin/work/detailtaskquery';
    }
    $(document).ready(function(){
    	var oStartDate = new Date();var oEndDate = new Date();oStartDate.setDate(oStartDate.getDate() - 7);oStartDate.setHours(0,0,0);oEndDate.setHours(23,59,59);$('#reservationtime').daterangepicker({showDropdowns:true,timePicker24Hour:true,startDate:oStartDate,endDate:oEndDate,timePicker: true, timePickerIncrement: 1, format: 'MM/DD/YYYY h:mm A'});
        $('.status').click(function(){   //点击效果
            $('.status').removeClass("btn-primary");
            $('.status').removeClass("btn_status");
            $('.status').addClass("btn-default");
            $(this).removeClass("btn-default");
            $(this).addClass("btn-primary");
            $(this).addClass("btn_status");
            var status = $(this).val();
            $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                //重写ajax 分页参数
                json.page = Math.floor(json.start / json.length) + 1;
                json.size = json.length || 10;
                //搜索条件
                json.visit_status = status;
                json.sort = $('.th_sort').attr('value');
                json.keyword = $('input[name="search[keyword]"]').val();
                json.check = '0';
            });
            table.ajax.reload();
        });
        $('.handle_button').click(function(){ 
            $('.th_change').text('家访员');
        });
        $('.wait_handle_button').click(function(){ 
            $('.th_change').text('征信报告');
        });
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
                json.visit_status = $('select[name="search[status]"]').val();
                json.sort = '3';
                json.keyword = $('input[name="search[keyword]"]').val();
                json.check = '0';
            });
            table.ajax.reload();
        });
        
        var table = $("#example1").DataTable({
            "processing": true,
            "serverSide": true,
            "paginate": true,
            'searching': false,
            "ajax": {
                'url': "/Admin/business/listsallbusiness",
                'data': {
                    'token': USER.get_token()
                },
                'dataSrc': function (r) {
                    if (r.error_no !== 200) {
                        return [];
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
                        return row.customer_address;
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
                        return row.customer_name;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.time;
                    },
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
                        return row.customer_name;
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
                        return row.customer_name;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return '<button type="button" class="btn btn-default" onclick="task_deal($(this).parent().parent());">处理</button>';
                    }
                },
            ],
//                    "order": [[1, 'asc']]
        });

    })
 </script>
@endsection
