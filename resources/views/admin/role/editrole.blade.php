    @include('common.title')
    <link rel="stylesheet" href="/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="//cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/datepicker/datepicker3.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/bower_components/AdminLTE/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css">
    <!-- treeview -->
    <link rel="stylesheet" href="/bower_components/AdminLTE/plugins/bootstrap-treeview/bootstrap-treeview.min_16bd11d.css">
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
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
                                    </form>
                                </div>
                                <div id="tree">
                       
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
                    
                </section>
</div>
@include('common/common')
<script src="/bower_components/AdminLTE/plugins/bootstrap-treeview/bootstrap-treeview.min.js"></script>
<script>         
    $(function () {
        var nodeCheckedSilent = false;  
        function nodeChecked (event, node){  
            if(nodeCheckedSilent){  
                return;  
            }  
            nodeCheckedSilent = true;  
            checkAllParent(node);  
            checkAllSon(node);  
            nodeCheckedSilent = false;  
        }  
          
        var nodeUncheckedSilent = false;  
        function nodeUnchecked  (event, node){  
            if(nodeUncheckedSilent)  
                return;  
            nodeUncheckedSilent = true;  
            uncheckAllParent(node);  
            uncheckAllSon(node);  
            nodeUncheckedSilent = false;  
        }  
          
        //选中全部父节点  
        function checkAllParent(node){  
            $('#tree').treeview('checkNode',node.nodeId,{silent:true});  
            var parentNode = $('#tree').treeview('getParent',node.nodeId);  
            if(!("nodeId" in parentNode)){  
                return;  
            }else{  
                checkAllParent(parentNode);  
            }  
        }  
        //取消全部父节点  
        function uncheckAllParent(node){  
            $('#tree').treeview('uncheckNode',node.nodeId,{silent:true});  
            var siblings = $('#tree').treeview('getSiblings', node.nodeId);  
            var parentNode = $('#tree').treeview('getParent',node.nodeId);  
            if(!("nodeId" in parentNode)) {  
                return;  
            }  
            var isAllUnchecked = true;  //是否全部没选中  
            for(var i in siblings){  
                if(siblings[i].state.checked){  
                    isAllUnchecked=false;  
                    break;  
                }  
            }  
            if(isAllUnchecked){  
                uncheckAllParent(parentNode);  
            }  
          
        }  
          
        //级联选中所有子节点  
        function checkAllSon(node){  
            $('#tree').treeview('checkNode',node.nodeId,{silent:true});  
            if(node.nodes!=null&&node.nodes.length>0){  
                for(var i in node.nodes){  
                    checkAllSon(node.nodes[i]);  
                }  
            }  
        }  
        //级联取消所有子节点  
        function uncheckAllSon(node){  
            $('#tree').treeview('uncheckNode',node.nodeId,{silent:true});  
            if(node.nodes!=null&&node.nodes.length>0){  
                for(var i in node.nodes){  
                    uncheckAllSon(node.nodes[i]);  
                }  
            }  
        }  
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
        ROLE.tree_permission({
            'token': USER.get_token(),
            'callback': function (r) {
                if (r.error_no != 200) {
                    var modal = $('#myModal');
                    modal.find('.modal-body').text(r.error_msg);
                    modal.modal('show');
                    return;
                }
                if (r.error_no == 200) {
                    var tree = [];
                    for (var i in r.result.rows) {
                        var row = r.result.rows[i];
                        r.result.rows[i]['text'] = row.title;
                        r.result.rows[i]['state'] = {expanded: true};
                    }
                    tree2 = listToTree(r.result.rows, {idKey: 'id', parentKey: 'cid', childrenKey: 'nodes'});
                    $('#tree').treeview({data: tree2, showTags: true, showCheckbox: true, onNodeChecked:nodeChecked, onNodeUnchecked:nodeUnchecked});
                }
                ROLE.list_module({'role_id': role_id, token: token, callback: function (r) {
                    if (r.error_no != 200) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text(r.error_msg);
                        modal.modal('show');
                        return;
                    }
                    for (var i in r.result.rows) {
                        var row = r.result.rows[i];
                        for (var k in tree2) {
                            if (tree2[k]['id'] == 'modules_' + row['module_id']) {
                                tree2[k].state.checked = true;
                            }
                        }
                    }
                    $('#tree').treeview({data: tree2, showTags: true, showCheckbox: true, onNodeChecked:nodeChecked, onNodeUnchecked:nodeUnchecked});
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
                        for (var k in tree2) {
                            for (var j in tree2[k]['nodes']) {
                                if (tree2[k]['nodes'][j]['id'] == 'methods_' + row['method_id']) {
                                    tree2[k]['nodes'][j].state.checked = true;
                                }
                            }
                        }
                    }
                    $('#tree').treeview({data: tree2, showTags: true, showCheckbox: true, onNodeChecked:nodeChecked, onNodeUnchecked:nodeUnchecked});
                }});
                ROLE.list_privilege({'role_id': role_id, token: token, callback: function (r) {
                    if (r.error_no != 200) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text(r.error_msg);
                        modal.modal('show');
                        return;
                    }
                    for (var i in r.result.rows) {
                        var row = r.result.rows[i];
                        for (var k in tree2) {
                            for (var j in tree2[k]['nodes']) {
                                for (var h in tree2[k]['nodes'][j]['nodes']) {
                                    if (tree2[k]['nodes'][j]['nodes'][h]['id'] == 'privilege_' + row['privilege_id']) {
                                        tree2[k]['nodes'][j]['nodes'][h].state.checked = true;
                                    }
                                }
                            }
                        }
                    }
                    $('#tree').treeview({data: tree2, showTags: true, showCheckbox: true, onNodeChecked:nodeChecked, onNodeUnchecked:nodeUnchecked});
                }});
            }
        });
        $('button[type="submit"]').click(function () {
            var role = {
                'title': $('input[name="role[title]"]').val(),
            }
            var arr = $('#tree').treeview('getChecked');   //获取选中的对象
            var permission = {modules: [], methods: [], privilege :[]};
            for (var k in arr) {
                var test = arr[k]['id'].split('_');
                if (test[0] == 'methods') {
                    permission['methods'].push(test[1]);
                }
                if (test[0] == 'modules') {
                    permission['modules'].push(test[1]);
                }
                if (test[0] == 'privilege') {
                    permission['privilege'].push(test[1]);
                }
            }
            role['permission'] = JSON.stringify(permission);
            if (!role_id) {
                role['parent_id'] = parent_id;
                ROLE.addrole({'role': role, token: token, callback: function (r) {
                        var modal = $('#myModal');
                        if (r.error_no == 200) {
                            modal.find('.modal-body').text('操作成功！');
                        } else {
                            modal.find('.modal-body').text(r.error_msg);
                        }
                        modal.modal('show');
                    }});
            } else {
                ROLE.updaterole({'role': role, token: token, 'role_id': role_id, callback: function (r) {
                        var modal = $('#myModal');
                        if (r.error_no == 200) {
                            modal.find('.modal-body').text('操作成功！');
                        } else {
                            modal.find('.modal-body').text(r.error_msg);
                        }
                        modal.modal('show');
                    }});
            }
            var rs = [];
             // var arr = $('#tree').treeview('getChecked');   //获取选中的对象
            // $('#tree').treeview('checkNode', [ 2, { silent: true } ]);  //选中节点，2为节点ID
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