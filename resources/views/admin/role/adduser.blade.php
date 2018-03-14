    @include('common.title')
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
                <!-- Content Header (Page header) -->
              <!--   <section class="content-header">
                    <h1>
                        添加用户
                        <small>add users</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="#">角色管理</a></li>
                        <li class="active">添加用户</li>
                    </ol>
                </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-6 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">添加用户</div>
                <div class="panel-body form-horizontal">
                    <!-- <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}"> -->
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('account') ? ' has-error' : '' }}">
                            <label for="account" class="col-md-4 control-label">账号(TEL)</label>

                            <div class="col-md-6">
                                <input id="account" type="text" class="form-control account" name="account" value="{{ old('account') }}" required autofocus>

                                @if ($errors->has('account'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('account') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">姓名</label>

                            <div class="col-md-6">
                                <input id="name" type="name" class="form-control name" name="name" value="{{ old('name') }}" required>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('area') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">业务区域</label>

                            <div class="col-md-6">
                                 <select name="search[status]" id='sheng' class="form-control sheng" >
                                 </select>
                                 <select name="search[status]" id='shi' class="form-control shi" >
                                 </select>
                                 <select name="search[status]"  id='qu' class="form-control qu" >
                                 </select>
                                <input id="area_add" type="text" class="form-control" name="area_add" value="{{ old('area_add') }}" required>
                            </div>
                        </div>
                        

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control password" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">确认密码</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control password-confirm" name="password_confirmation" required>
                            </div>
                        </div>
                        <?php if($user_type =='1') {?>
                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">用户类型</label>

                            <div class="col-md-6">
                                <select name="search[condition]" class="form-control" id="user_type">
                                    <option value= "2" >林润审批</option>
                                    <option value= "3" >聚车贷</option>
                                </select>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary addusersubmit">
                                    添加
                                </button>
                            </div>
                        </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <div class="row margin-bottom">
                        <div class="col-xs-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                查询
                                </span>
                                <select name="search[condition]" class="form-control">
                                    <option value= "0" >按姓名查询</option>
                                    <option value= "1" >按业务区域查询</option>
                                    <option value= "2" >按账号查询</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="input-group">
                                <input type="text" name="search[keyword]" class="form-control" placeholder="关键词">
                                <span class="input-group-btn">
                                    <button type="button" id="user_search" class="btn btn-info btn-flat">搜索</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>姓名</th>
                                <th>业务区域</th>
                                <th>账号</th>
                                <th>员工编号</th>
                                <th>操作</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>姓名</th>
                                <th>业务区域</th>
                                <th>账号</th>
                                <th>员工编号</th>
                                <th>操作</th>
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
 <script type="text/javascript">
    $(document).ready(function(){
        FillSheng('sheng', '', '');    //填充省
        FillShi('sheng', 'shi', '0', '');  //填充市
        FillQu('shi', 'qu', '0');//填充区
        //选中项变化    
        $("#sheng").change(function(){            //当省的内容改变的时候，触发市和区的内容改变     
            FillShi('sheng', 'shi', '', '');  //填充市
            FillQu('shi', 'qu', '');//填充区
        })
        $("#shi").change(function(){               //当市的内容改变的时候，触发区的内容改变
            FillQu('shi', 'qu', '');//填充区
        })  
        $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
            //重写ajax 分页参数
            json.page = Math.floor(json.start / json.length) + 1;
            json.size = json.length || 10;
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
                json.keyword = $('input[name="search[keyword]"]').val();
                json.condition = $('select[name="search[condition]"]').val();
            });
            table.ajax.reload();
        });
        var table = $("#example1").DataTable({
            "processing": true,
            "serverSide": true,
            "paginate": true,
            'searching': false,
            "ajax": {
                'url': "/Admin/role/listsalluser",
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
                        return row.name;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.business_area;
                    }
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.account;
                    },
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        return row.employee_number;
                    },
                },
                {
                    "orderable": false,
                    "data": function (row) {
                        if (row.account > 1) {
                            return '<a href="/Admin/role/edituser?account='+row.account+'&name='+row.name+'&province='+row.province+'&city='+row.city+'&town='+row.town+'&area_add='+row.area_add+'" onclick="">编辑</a>';
                        } else {
                            return '';
                        }
                    },
                }
            ],
//                    "order": [[1, 'asc']]
        });
      $('.addusersubmit').click(function(){
        if(checkempty('account, sheng, shi, qu, name, password, password-confirm') == false) {
            return false;
        }
        if ($('#password').val().length < 6) {
            alert('密码长度必须在6位以上');
            return false;
        }
        $.ajax({
               type:'post',
               url:'/Admin/role/adduserpost',
               dataType: 'json',
               data:{
                'token' : USER.get_token(),
                'account' : $('#account').val(),   //账号
                'name'    : $('#name').val(),      //姓名
                'password' : $('#password').val(), //密码
                'password_confirm' : $('#password-confirm').val(),   //确认密码
                'province' : $('.sheng option:selected').text(),         //省
                'city'     : $('.shi option:selected').text(),         //市
                'town'     : $('.qu option:selected').text(),           //区
                'area_add' :  $('#area_add').val(),                  //三级以外的地址
                'type'     :  $('#user_type').val(),
               },
               success:function(data){
                if (data.error_no !== 200) {
                    alert(data.error_msg);
                } else {
                   alert(data.error_msg);
                   window.location.href = '/Admin/role/adduser';
                }
               }
         });
      });
    })
 </script>
@endsection
