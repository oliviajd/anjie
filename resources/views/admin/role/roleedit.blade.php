@extends('layouts.admin_template')

@section('content')
        <link rel="stylesheet" href="/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="//cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- daterange picker -->
        <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/datetimepicker/bootstrap-datetimepicker.min.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="/bower_components/AdminLTE/dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/iCheck/all.css">
        <div class="content-wrapper1">
                <!-- Content Header (Page header) -->
                <!-- <section class="content-header">
                    <h1>
                        角色编辑
                        <small>role edit</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/index.html"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/pages/role/role_lists.html">角色管理</a></li>
                        <li class="active">角色编辑</li>
                    </ol>
                </section> -->

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">角色信息</h3>
                                    <div class="box-tools pull-right fade">
                                        <button name="role-delete" type="button" class="btn btn-block btn-danger">删除</button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <form role="form">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>名称</label>
                                            <input type="text" name="role[title]" class="form-control" placeholder="角色名称">
                                        </div>
                                        <div class="form-group" id="permission">
                                            <label class="margin-bottom">权限设置</label>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">保存</button>
                                    <a class="btn btn-default" href="/Admin/role/roleset">取消</a>
                                </div>
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
                                <a href="/Admin/role/roleset" class="btn btn-default">返回列表</a>
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
                                是否删除这个角色？
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" name="role-delete-confirm">确认</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- iCheck 1.0.1 -->
        <script src="/bower_components/AdminLTE/plugins/iCheck/icheck.min.js"></script>

        <script>
            $(function () {
                var token = USER.get_token();
                var role = {};
                var role_id = 0;
                var parent_id = 0;
                var set_value = function (role) {
                    $('input[name="role[title]"]').val(role.title);
                }
                if (getUrlParam('role_id')) {//编辑模式
                    var role_id = getUrlParam('role_id');
                    ROLE.get({'role_id': role_id, token: token, callback: function (r) {
                            if (r.error_no != 200) {
                                var modal = $('#myModal');
                                modal.find('.modal-body').text(r.error_msg);
                                modal.modal('show');
                                return;
                            }
                            role = r.result;
                            set_value(role);
                            $('.box-tools').removeClass('fade');
                        }});
                } else {
                    parent_id = getUrlParam('parent_id');
                    if (!parent_id) {
                        $('button[type="submit"]').remove();
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('非法请求！');
                        modal.modal('show');
                    }
                }
                ROLE.permission_tree({token: token, callback: function (r) {
                        if (r.error_no != 200) {
                            var modal = $('#myModal');
                            modal.find('.modal-body').text(r.error_msg);
                            modal.modal('show');
                            return;
                        }
                        var tree = r.result.rows;
                        var d = $('#permission');
                        for (var i in tree) {
                            var module = tree[i]['module'];
                            var children = tree[i]['children'];
                            var div = '<div class="row"><div class="col-lg-12"><label><input type="checkbox" name="module[]" value="' + module.module_id + '" class="flat-red"> ' + module.title + '</label></div></div>'
                            d.append(div);
                            var div2 = $('<div class="row margin"></div>')
                            d.append(div2);
                            for (var j in children) {
                                var div3 = '<div class="col-lg-3"><label style="font-weight:normal;"><input type="checkbox" data-mid="' + module.module_id + '" name="method[]" value="' + children[j].method_id + '" class="flat-red"> ' + children[j].title + '</label></div>';
                                div2.append(div3);
                            }
                        }
                        //Flat red color scheme for iCheck
                        $('input[type="checkbox"].flat-red').iCheck({
                            checkboxClass: 'icheckbox_flat-green',
                            radioClass: 'iradio_flat-green'
                        });
                        ROLE.list_module({'role_id': role_id, token: token, callback: function (r) {
                                if (r.error_no != 200) {
                                    var modal = $('#myModal');
                                    modal.find('.modal-body').text(r.error_msg);
                                    modal.modal('show');
                                    return;
                                }
                                for (var i in r.result.rows) {
                                    var row = r.result.rows[i];
                                    $('input[name="module[]"]').each(function () {
                                        if (parseInt($(this).val()) == parseInt(row['module_id'])) {
                                            $(this).iCheck('check');
                                        }
                                    });
                                }
                            }});
                        ROLE.list_method({'role_id': role_id, token: token, callback: function (r) {
                                if (r.error_no != 200) {
                                    var modal = $('#myModal');
                                    modal.find('.modal-body').text(r.error_msg);
                                    modal.modal('show');
                                    return;
                                }
                                for (var i in r.result.rows) {
                                    var row = r.result.rows[i];
                                    $('input[name="method[]"]').each(function () {
                                        if (parseInt($(this).val()) == parseInt(row['method_id'])) {
                                            $(this).iCheck('check');
                                        }
                                    });
                                }
                            }});
                    }});

                $('button[type="submit"]').click(function () {
                    var role = {
                        'title': $('input[name="role[title]"]').val(),
                    }
                    var permission = {modules: [], methods: []};
                    $('input[name="module[]"]:checked').each(function () {
                        var mid = $(this).val();
                        permission['modules'].push(mid);
                    });
                    $('input[name="method[]"]:checked').each(function () {
                        var mid = $(this).val();
                        permission['methods'].push(mid);
                    });
                    role['permission'] = JSON.stringify(permission);
                    if (!role_id) {
                        role['parent_id'] = parent_id;
                        ROLE.add({'role': role, token: token, callback: function (r) {
                                var modal = $('#myModal');
                                if (r.error_no == 200) {
                                    modal.find('.modal-body').text('操作成功！');
                                } else {
                                    modal.find('.modal-body').text(r.error_msg);
                                }
                                modal.modal('show');
                            }});
                    } else {
                        ROLE.update({'role': role, token: token, 'role_id': role_id, callback: function (r) {
                                var modal = $('#myModal');
                                if (r.error_no == 200) {
                                    modal.find('.modal-body').text('操作成功！');
                                } else {
                                    modal.find('.modal-body').text(r.error_msg);
                                }
                                modal.modal('show');
                            }});
                    }
                });
                $('button[name="role-delete"]').click(function () {
                    var modal2 = $('#myModal2').modal('show');
                });
                $('button[name="role-delete-confirm"]').click(function () {
                    $('#myModal2').on('hidden.bs.modal', function (e) {
                        ROLE.delete({
                            'role_id': role_id,
                            'token': token,
                            'callback': function (r) {
                                if (r.error_no == 200) {

                                } else {
                                }
                                var modal = $('#myModal');
                                modal.find('.modal-body').text(r.error_msg);
                                modal.modal('show');
                            }
                        });
                    })
                    $('#myModal2').modal('hide');
                });
            });
        </script>
@endsection