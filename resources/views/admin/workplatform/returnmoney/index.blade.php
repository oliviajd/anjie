@include('common.title')
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
    <!-- Content Header (Page header) -->
   <!--  <section class="content-header">
        <h1>
            申请录入
            <small>input request</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
            <li><a href="/Admin/work/inputrequest">申请录入</a></li>
            <li class="active">申请录入</li>
        </ol>
    </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
            <div class="box-body">
                <div class="box-header">
                    <button type="button" class="status btn btn-primary handle_button" value= "1">待认领案件</button>
                    <button type="button" class="status btn btn-default wait_handle_button" value= "2">待处理案件</button> 
                </div>
                <div class="box-body">
                    <div class="row margin-bottom">
                        <div class="col-xs-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    申请编号：
                                </span>
                                <input type="text" name="request_no" class="form-control request_no" placeholder="">
                            </div>
                        </div>
                        <div class="col-xs-4">
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
                                    产品名称：
                                </span>
                                <select name="product_name" class="form-control product_name">
                                    <option value="">请选择</option>
                                    <option value="1">新车</option>
                                    <option value="2">二手车</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-bottom">
                        <!-- <div class="col-xs-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    状态：
                                </span>
                                <select name="search[status]" class="form-control">
                                    <option value="1">请选择</option>
                                    <option value="2">未处理</option>
                                    <option value="3">暂存</option>
                                    <option value="4">补件</option>
                                </select>
                            </div>
                        </div> -->
                        <div class="col-xs-7">
                            <div class="input-group">

                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                <span class="input-group-addon">
                                    时间范围：
                                </span>
                                <input type="text" class="form-control pull-right" id="reservationtime">
                                <span class="input-group-btn">
                                    <button type="button" id="search" class="btn btn-info btn-flat">查询</button>
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
                            <th>产品类别</th>
                            <th>产品名称</th>
                            <th>商户名称</th>
                            <th>贷款金额</th>
                            <th>首付款金额</th>
                            <th>进件时间</th>
                            <th>到件时间</th>
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
                            <th>产品类别</th>
                            <th>产品名称</th>
                            <th>商户名称</th>
                            <th>贷款金额</th>
                            <th>首付款金额</th>
                            <th>进件时间</th>
                            <th>到件时间</th>
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
</div>
<div class="modal fade" id="manaudit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">  
    <div class="modal-dialog" role="document">  
        <div class="modal-content">  
            <div class="modal-header">  
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">  
                    <span aria-hidden="true">×</span>  
                </button>  
                <h4 class="modal-title" id="myModalLabel">回款时间</h4>  
            </div>  
            <div class="modal-body">  
                <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
                    <tbody>
                        <tr>
                            <td class="col-md-2">客户姓名</td>
                            <td class="col-md-4"><input type="text" name="customer_name" class="form-control" disabled="disabled"></td>
                            <td class="col-md-2">身份证号</td>
                            <td class="col-md-4"><input type="text" name="customer_certificate_number" class="form-control" disabled="disabled"></td>
                        </tr>
                        <tr>
                            <td>手机号码</td>
                            <td><input type="text" name="customer_telephone" class="form-control" disabled="disabled"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>申请编号</td>
                            <td><input type="text" name="request_no" class="form-control" disabled="disabled"></td>
                            <td>合同编号</td>
                            <td><input type="text" name="constract_no" class="form-control" disabled="disabled"></td>
                        </tr>
                        <tr>
                             <td>★回款账户</td>
                            <td><input type="text" name="return_card" class="form-control return_card" placeholder=""></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>★回款金额</td>
                            <td><input type="text" name="return_prize" class="form-control return_prize" placeholder=""></td>
                            <td>★回款时间</td>
                            <td><input type="text" id="return_time" name="return_time" class="form-control return_time" placeholder=""></td>
                        </tr>
                        <tr>
                            <td>备注</td>
                            <td colspan="3"><textarea name="return_description" class="comments" cols="75" rows="4" placeholder=""></textarea></td>
                        </tr>
                    </tbody>   
                </table> 
            </div>  
            <div class="modal-footer">  
                <button type="button" id="assign_confirm" class="btn btn-primary assign_confirm">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>    
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
    var rows = [];
    var task_deal = function (id, item_instance_id) {
        var row = {};
        for (var i in rows) {
            if (rows[i]['id'] == id) {
                row = rows[i];
            }
        }
        if (!row['id']) {
            alert('请刷新重试！');
            return false;
        }
        $('[name=customer_name]').val(row['customer_name'])
        $('[name=customer_telephone]').val(row['customer_telephone'])
        $('[name=customer_certificate_number]').val(row['customer_certificate_number'])
        $('[name=request_no]').val(row['request_no'])
        $('[name=constract_no]').val(row['constract_no'])
        $('[name=return_card]').val(row['return_card'])
        $('[name=return_prize]').val(row['return_prize'])
        $('[name=return_time]').val(row['return_time'])
        $('[name=return_description]').val(row['return_description'])
        $('[name=constract_prize]').val(row['constract_prize'])
        $('[name=remittance_prize]').val(row['remittance_prize'])
        $('#assign_confirm').unbind('click');
        $('#assign_confirm').click(function () {
            if (checkempty('return_card,return_prize,return_time') == false) {
                var modal = $('#myModal');
                modal.find('.modal-body').text('信息填写不完整');
                modal.modal('show');
                return false;
            }
            var _this = this;
            $(_this).prop('disabled', true);
            var data = {
                'work_id': id,
                'item_instance_id': item_instance_id,
                'token': token,
                'return_prize': $("[name=return_prize]").val(),
                'return_time': $("[name=return_time]").val(),
                'return_card': $("[name=return_card]").val(),
                // 'return_confirm_time': $("[name=return_confirm_time]").val(),
                'return_description': $("[name=return_description]").val(),
            };
            $.ajax({
                url:  '/Api/workplatform/returnmoney',
                type: 'post',
                data: data,
                dataType: 'json',
                'success': function (r) {
                    $(_this).prop('disabled', false);
                    var modal = $('#myModal');
                    modal.find('.modal-body').text(r.error_msg);
                    modal.modal('show');
                    if (r.error_no == '200') {
                        $('.modal_close').click(function () {
                            window.location.href =window.location.href;
                        });
                    }
                },
                'error': function (r) {
                    $(_this).prop('disabled', false);
                }
            });
        })
        $('#manaudit').modal('show');
    }

    $(function () {
        var oStartDate = new Date();var oEndDate = new Date();oStartDate.setDate(oStartDate.getDate() - 7);oStartDate.setHours(0,0,0);oEndDate.setHours(23,59,59);$('#reservationtime').daterangepicker({showDropdowns:true,timePicker24Hour:true,startDate:oStartDate,endDate:oEndDate,timePicker: true, timePickerIncrement: 1, format: 'MM/DD/YYYY h:mm A'});
        $('#return_time').daterangepicker({singleDatePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
        $('#return_confirm_time').daterangepicker({singleDatePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
        $('.status').click(function () {   //点击效果
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
                json.type = status;
            });
            table.ajax.reload();
        });
        $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
            //重写ajax 分页参数
            json.page = Math.floor(json.start / json.length) + 1;
            json.size = json.length || 10;
            json.sort = '3';
        });
        $('input[name="search[keyword]"]').keyup(function (e) {
            if (e.which == 13) {
                $('#search').click();
            }
        });
        $('#search').click(function () {
            $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                //重写ajax 分页参数
                json.page = Math.floor(json.start / json.length) + 1;
                json.size = json.length || 10;
                //搜索条件
                json.type = $('.status.btn-primary').val();
                json.request_no = $('.request_no').val();
                json.customer_name = $('.customer_name').val();
                json.product_name = $('.product_name').find("option:selected").val();
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
            "oLanguage": DataTable_language,
            "ajax": {
                'url': "/Api/workplatform/listworkplatform/",
                'data': {
                    'token': token,
                    'role_id': 87,
                    'type': 1
                },
                'dataSrc': function (r) {
                    if (r.error_no !== 200) {
                        error_skip(r);
                    } else {
                        //重写ajax 返回的总数等参数
                        r.recordsTotal = r.result.total;
                        r.recordsFiltered = r.result.total;
                        rows = r.result.rows;
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
                        if (row.product_class_number == 'XC') {
                            return '新车';
                        } else {
                            return '旧车';
                        }
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
                        return row.merchant_name;
                    }
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
                        return time_to_str(row.receive_time);
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
                        return '未处理';
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.type == 1 ? '<button type="button" class="btn btn-default" onclick="task_pickup(this,' + row.id + ',' + row.item_instance_id + ');">认领</button>' :
                                '<button type="button" class="btn btn-default" onclick="task_deal(' + row.id + ',' + row.item_instance_id + ');">处理</button>';
                    }
                },
            ],
//                    "order": [[1, 'asc']]
        });
    })
</script>
@endsection
