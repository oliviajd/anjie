    @include('common.title')
    <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
                <!-- Content Header (Page header) -->
                <!-- <section class="content-header">
                    <h1>
                        人工审核配置
                        <small>login logs</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/Admin">系统管理</a></li>
                        <li class="/Admin/system/loginlog">人工审核配置</li>
                    </ol>
                </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                <div class="row margin-bottom">
                            <div class="col-xs-2">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        类型：
                                    </span>
                                    <select name="search[status]" id="audit_type" class="form-control">
                                    </select>
                                </div>
                            </div>

                        </div>
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>序号</th>
                                <th>类型</th>
                                <th>产品名称</th>
                                <th>额度审批一级权限</th>
                                <th>额度审批二级权限</th>
                                <th>额度审批三级权限</th>
                                <th>额度审批四级权限</th>
                                <th>操作</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>序号</th>
                                <th>类型</th>
                                <th>产品名称</th>
                                <th>额度审批一级权限</th>
                                <th>额度审批二级权限</th>
                                <th>额度审批三级权限</th>
                                <th>额度审批四级权限</th>
                                <th>操作</th>
                            </tr>
                        </tfoot>
                    </table>
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
                <h4 class="modal-title" id="myModalLabel">修改人工审核配置</h4>  
            </div>  
            <div class="modal-body">  
                
                <div class="input-group">
                    <span class="input-group-addon">
                        审批类型：
                    </span>
                    <input type="text" name="type" id="type"  class="form-control mod" placeholder="">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">
                        产品名称：
                    </span>
                    <input type="text" name="product_name" id="product_name" class="form-control mod" placeholder="">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">
                        额度审批一级权限：
                    </span>
                    <input type="text" name="first_authority" id="first_authority" class="form-control mod" value="" placeholder="">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">
                        额度审批二级权限：
                    </span>
                    <input type="text" name="second_authority" id="second_authority" class="form-control mod" value="" placeholder="">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">
                        额度审批三级权限：
                    </span>
                    <input type="text" name="third_authority" id="third_authority" class="form-control mod" value="" placeholder="">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">
                        额度审批四级权限：
                    </span>
                    <input type="text" name="fourth_authority" id="fourth_authority" class="form-control mod" value="" placeholder="">
                </div>
            </div>  
            <div class="modal-footer">  
                <button type="button" id="assign_confirm" class="btn btn-primary assign_confirm">确认</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>    
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
            var token = USER.get_token();
            $.ajax({
                   type:'post',
                   url:'/Admin/system/listmanaudit',
                   dataType: 'json',
                   success:function(data){
                      if (data.error_no !== 200) {
                            alert(data.error_msg);
                        } else {
                            var selDom = $("#audit_type");
                        	for (i in data.result.rows){
							    selDom.append("<option value='"+data.result.rows[i].audit_type+"'>"+data.result.rows[i].type+"</option>");
							}
                        }
                   }
             }); 
		    var update_manaudit = function (obj) {
		            var tds = obj.find('td');
		            var modal3 = $('#manaudit');
		            var account = tds.eq(1).text();
		            $("#type").attr("value",tds.eq(1).text());
		            $("#product_name").attr("value",tds.eq(2).text());
		            $('#type').attr("disabled",true);
		            $('#product_name').attr("disabled",true);
		            $("#first_authority").val(tds.eq(3).text());
		            $("#second_authority").val(tds.eq(4).text());
		            $("#third_authority").val(tds.eq(5).text());
		            $("#fourth_authority").val(tds.eq(6).text());
		            modal3.modal('show');
		            modal3.on('hide.bs.modal', function () {
		                $('.assign_confirm').unbind('click');
		            });
		            $('.assign_confirm').click(function () {
		                $.ajax({
		                       type:'post',
		                       url:'/Admin/system/updatemanaudit',
		                       dataType: 'json',
		                       data:{'audit_type' : tds.eq(0).text(), 'first_authority':$("#first_authority").val(), 'second_authority':$("#second_authority").val(), 'third_authority':$("#third_authority").val(), 'fourth_authority':$("#fourth_authority").val()},
		                       success:function(data){
		                          if (data.error_no !== 200) {
		                                alert(data.error_msg);
		                            } else {
		                                modal3.modal('hide');
		                                tds.eq(3).text(data.result.first_authority);
		                                tds.eq(4).text(data.result.second_authority);
		                                tds.eq(5).text(data.result.third_authority);
		                                tds.eq(6).text(data.result.fourth_authority);
		                            }
		                       }
		                 });       
		            });
		    }
            $(document).ready(function(){
            	$("select#audit_type").change(function(){
            		$("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                        var audit_type = $("#audit_type").find("option:selected").val();
                        //重写ajax 分页参数
                        json.page = Math.floor(json.start / json.length) + 1;
                        json.size = json.length || 10;
                        //搜索条件
                        json.audit_type = audit_type;
                    });
                    table.ajax.reload();
            		
				});
                $('#user_search').click(function () {
                    $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                        var date_range = $('#reservationtime').val().split(' - ');
                        //重写ajax 分页参数
                        json.page = Math.floor(json.start / json.length) + 1;
                        json.size = json.length || 10;
                        //搜索条件
                        json.keyword = $('input[name="account"]').val();
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
                        'url': "/Admin/system/listmanaudit",
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
                                return row.audit_type;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.type;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.product_name;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.first_authority;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.second_authority;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.third_authority;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.fourth_authority;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return '<a href="javascript:;"  onclick="update_manaudit($(this).parent().parent());">修改</a>'                            }
                        },
                    ],
                });
            })
         </script>
@endsection