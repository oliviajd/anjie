    @include('common.title')
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrapper1">
                <!-- Content Header (Page header) -->
                <!-- <section class="content-header">
                    <h1>
                        用户角色
                        <small>user role lists</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="#">用户角色</a></li>
                        <li class="active">全部用户</li>
                    </ol>
                </section> -->

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div id="tree"></div>
                        </div>
                        <div class="col-md-8">
                            <div class="box">
                                <div class="box-body">
                                    <div class="row margin-bottom hide">
                                        <div class="col-xs-3">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    角色
                                                </span>
                                                <select name="search[role_id]" class="form-control"></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="input-group">
                                                <input type="text" name="search[q]" class="form-control" placeholder="关键词">
                                                <span class="input-group-btn">
                                                    <button type="button" id="role_search" class="btn btn-info btn-flat">搜索</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>用户名</th>
                                                <th>账号</th>
                                                <th>角色</th>
                                                <th>创建时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>

                                        <tfoot>
                                            <tr>
                                                <th>用户名</th>
                                                <th>账号</th>
                                                <th>角色</th>
                                                <th>创建时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">用户列表</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row margin-bottom">
                                        <div class="col-xs-3">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    角色
                                                </span>
                                                <select name="search2[role_id]" class="form-control"></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="input-group">
                                                <input type="text" name="search2[q]" class="form-control" placeholder="关键词">
                                                <span class="input-group-btn">
                                                    <button type="button" id="role_search2" class="btn btn-info btn-flat">搜索</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>用户名</th>
                                                <th>账号</th>
                                                <th>角色</th>
                                                <th>创建时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>

                                        <tfoot>
                                            <tr>
                                                <th>用户名</th>
                                                <th>账号</th>
                                                <th>角色</th>
                                                <th>创建时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </section>
                <!-- /.content -->
                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                            </div>
                            <div class="modal-body">
                                ...
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                            </div>
                            <div class="modal-body">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" name="role-delete-confirm">确认</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal3" tabindex="-3" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <div class="input-group">
                                    <span class="input-group-addon">用户名</span>
                                    <input type="text" class="form-control" name="role_user[loginname]" placeholder="Username">
                                </div>
                                <p class="margin hide"><code></code></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" name="role-add-confirm">添加</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @include('common/common')
    @include('common.modal.normal')
    <script src="/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- treeview -->
    <script src="/bower_components/AdminLTE/plugins/bootstrap-treeview/bootstrap-treeview.min.js"></script>
        
     <script>
            $(function () {
                var add = false;
                var methods = ROLE.get_method(); 
                for(var i in methods) {
                    if (methods[i]['method_id'] == '188') {
                        add = true;
                    }
                }
                ROLE.lists({
                    'token': USER.get_token(),
                    'callback': function (r) {
                        if (r.error_no == 200) {
                            var tree = [];
                            for (var i in r.result.rows) {
                                var row = r.result.rows[i];
                                r.result.rows[i]['text'] = row.title + '[' + row.nums + ']';
                                r.result.rows[i]['tags'] = [];
                                r.result.rows[i]['state'] = {expanded: r.result.rows.length > 100 ? false : true};
                                if ((r.result.rows[0]['role_id'] != 1 && r.result.rows[i]['parent_id'] !=1) || (r.result.rows[0]['role_id'] == 1 && r.result.rows[i]['role_id'] != 1)) {
                                    r.result.rows[i]['tags'].push('<span style="display:block;" onclick="add_role_user({role_id:' + r.result.rows[i]['role_id'] + ',title:\'' + r.result.rows[i]['title'] + '\'})">添加用户</span>');
                                }
                            }
                            tree = listToTree(r.result.rows, {idKey: 'role_id', parentKey: 'parent_id', childrenKey: 'nodes'});
                            $('#tree').treeview({data: tree, showTags: true, onNodeSelected: function (event, data) {
                                    search_role_user(data.role_id);
                                }});
                        }
                    }
                });
                $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                    // console.log(json);
                    //重写ajax 分页参数
                    json.page = Math.floor(json.start / json.length) + 1;
                    json.size = json.length || 10;
                });
                $('input[name="search[q]"]').keyup(function (e) {
                    if (e.which == 13) {
                        $('#role_search').click();
                    }
                });
                $('#role_search2').click(function () {
                    $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                        //重写ajax 分页参数
                        json.page = Math.floor(json.start / json.length) + 1;
                        json.size = json.length || 10;
                        //搜索条件
                        json.q = $('input[name="search[q]"]').val();
                        json.role_id = $('select[name="search[role_id]"]').val();
                    });
                    table.ajax.reload();
                });
                var table = $("#example1").DataTable({
                    "processing": true,
                    "serverSide": true,
                    "paginate": true,
                    'searching': false,
                    "ajax": {
                        'url': "/Admin/role/listsuser",
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
                                return row.user.loginname + '(' + row.user.realname + ')';
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.user.mobile;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return row.role.title;
                            }
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                return time_to_str(parseInt(row.create_time));
                            },
                        },
                        {
                            "orderable": false,
                            "data": function (row) {
                                if (row.role.role_id > 1) {
                                    return '<a href="javascript:;" onclick="delete_user(' + row.role.role_id + ',' + row.user.user_id + ',$(this).parent().parent())"><i class="fa fa-fw fa-remove"></i>移除</a>';
                                } else {
                                    return '';
                                }
                            }
                        }
                    ],
//                    "order": [[1, 'asc']]
                });
            });
            var delete_user = function (role_id, user_id, row) {
                var table = $("#example1").DataTable();
                var data = table.row(row).data();
                var modal2 = $('#myModal2');
                modal2.find('.modal-body').text('用户[' + data.user.loginname + ']将不再拥有[' + data.role.title + ']的权限');
                modal2.modal('show');
                modal2.on('hide.bs.modal', function () {
                    $('button[name="role-delete-confirm"]').unbind('click');
                });
                $('button[name="role-delete-confirm"]').click(function () {
                    ROLE.delete_user({
                        'role_id': role_id,
                        'user_id': user_id,
                        'token': USER.get_token(),
                        'callback': function (r) {
                            if (r.error_no == 200) {
                                table.row(row).remove().draw(false)
                            } else {
                                var modal = $('#myModal');
                                modal.find('.modal-body').text(r.error_msg);
                                modal.modal('show');
                            }
                        }
                    });
                    $('#myModal2').modal('hide');
                    $('button[name="role-delete-confirm"]').unbind('click');
                });
            }
            var search_role_user = function (role_id) {
                var table = $("#example1").DataTable();
                $("#example1").on('preXhr.dt', function (e, setting, json, xhr) {
                    //重写ajax 分页参数
                    json.page = Math.floor(json.start / json.length) + 1;
                    json.size = json.length || 10;
                    //搜索条件
                    json.role_id = role_id;
                });
                table.ajax.reload();
            }
            var add_role_user = function (role) {
                var modal3 = $('#myModal3');
                modal3.find('.modal-title').text('添加用户至[' + role.title + ']');
                modal3.modal('show');
                modal3.on('hide.bs.modal', function () {
                    modal3.find('code').text('').parent().addClass('hide');
                    $('button[name="role-add-confirm"]').unbind('click');
                });
                $('button[name="role-add-confirm"]').click(function () {
                    USER.find({loginnames: JSON.stringify([$('input[name="role_user[loginname]"]').val()]), token: USER.get_token(), callback: function (r) {
                            if (r.error_no != 200 || r.result.rows.length == 0) {
                                modal3.find('code').text('未找到该用户！').parent().removeClass('hide');
                            } else {
                                var user_id = r.result.rows[0]['user_id'];
                                //window.location.href = '/pages/role/role_user_edit.html?user_id=' + user_id;
                                ROLE.add_user({token: USER.get_token(), 'user_id': user_id, 'role_id': role.role_id, callback: function (r, option) {
                                        if (r.error_no == 200) {
                                            modal3.find('code').text('操作成功！').parent().removeClass('hide');
                                            search_role_user(role.role_id);
                                        } else {
                                            modal3.find('code').text(r.error_msg).parent().removeClass('hide');
                                        }
                                    }});
                            }
                        }});
                });
            }
            
            
        </script>
@endsection