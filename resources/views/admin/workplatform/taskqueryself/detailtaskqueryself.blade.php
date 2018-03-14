@include('common.title')
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/imgviewer/viewer.min.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload_ec8bd4e.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui_8be8b42.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-noscript_77b97d2.css"></noscript>
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui-noscript_f95c011.css"></noscript>
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css">
<link rel="stylesheet" href="/css/viewer.css">
<style>
    .pictures {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pictures > li {
        float: left;
        width: 33.3%;
        height: 33.3%;
        margin: 0 -1px -1px 0;
        border: 1px solid transparent;
        overflow: hidden;
    }

    .pictures > li > img {
        width: 100%;
        cursor: -webkit-zoom-in;
        cursor: zoom-in;
    }
    .checkbox {text-align: left;}
</style>
</head>
@extends('layouts.admin_template')
@section('content')
<div class="content-wrappers">
    <!-- Content Header (Page header) -->
              <!--   <section class="content-header">
                    <h1>
                        申请件查询
                        <small>inquire</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/Admin/work/detailtaskquery">申请件查询</a></li>
                        <li class="active">申请件查询</li>
                    </ol>
                </section> -->
</div><br/> 
<div class="container1">
    <div class="row">
        <div class="col-md-7">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">客户基本资料</h3>
                    <div class="pull-right" style="margin-right: 10px;">
                        <button type="submit" class="btn btn-default source_window pull-right" style="width: 140px;height: 40px;">
                                客户影像资料
                        </button>
                    </div>
                </div>
                <div class="box-body">
        <?php if($show['inquire'] == '1') {?>  @include('common.pannel.work_info_inquire') <?php }?>
        <?php if($show['ficodata'] == '1') {?>   @include('common.pannel.work_info_ficodata')   <?php }?>
        <?php if($show['from'] == '1') {?>  @include('common.pannel.work_info_from') <?php }?>
        <?php if($show['from'] == '3') {?>  @include('common.pannel.work_info_from3') <?php }?>
        <?php if($show['basic'] == '2') {?>  @include('common.pannel.work_info_basic2') <?php }?>
        <?php if($show['basic'] == '3') {?>  @include('common.pannel.work_info_basic3') <?php }?>
        <?php if($show['basic'] == '4') {?>  @include('common.pannel.work_info_basic4') <?php }?>
        <?php if($show['spouse'] == '1') {?>   @include('common.pannel.work_info_spouse') <?php }?>
        <?php if($show['contact'] == '1') {?>   @include('common.pannel.work_info_contact') <?php }?>
        <?php if($show['bondsman'] == '1') {?>   @include('common.pannel.work_info_bondsman') <?php }?>
        <?php if($show['goods'] == '1') {?>   @include('common.pannel.work_info_goods') <?php }?>
        <?php if($show['cost'] == '1') {?>   @include('common.pannel.work_info_cost') <?php }?>
        <?php if($show['input_des'] == '1') {?>   @include('common.pannel.work_info_input_des')   <?php }?>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            @include('common.pannel.work_audit_process')   
        </div>
    </div>
</div>
@include('common.modal.normal')
@include('common/common')
@include('common.box.source_window')
<script src="/js/viewer.js"></script>
@include('common/upload')
@include('common.modal.popbox')
<script>
    
    var token = USER.get_token();
    var work_id = <?php echo intval($detail['id']) ?>;
    var item_instance_id = <?php echo intval($item_instance_id) ?>;
    $(function () {
        $('.button_add_file').attr("disabled", true);
        $('.a_add_file').css({"color":"gray"});
        $('.source_window').click(function () {
            $('#source_window').modal('show');
        }); 
        loadSources(token, work_id, function (sources) {
            SOURCE.init({
                data: sources,
                onChange: function (Class) {
                    $('#source_length').text('共' + SOURCE.get_length(Class) + '张');
                },
                onRemove: function (Class, obj) {
                    if (!img_delete.hasOwnProperty(Class)) {
                        img_delete = {
                            'source_type': SOURCE._data[Class]['source_type'],
                            'source_lists': [],
                        }
                        img_delete.source_lists.push(obj);
                    }
                }
            });
            SOURCE.show();
            $('#source_type').find('a').click(function () {//图片类别发生变化时重新加载插件
                if ($(this).attr('data-type') != SOURCE.current_Class) {
					// 重置数量
					if ($(this).attr('data-st') == 'image') {
                        $('#source_length').html('共0张');
                    } else if($(this).attr('data-st') == 'video') {
                        $('#source_length').html('共0段');
                    }
                    if ($(this).attr('data-edit') == 1) {
                        $('#source_button').css('color', '#3c8dbc');
                        $('#source_button').find('input').prop('disabled', false);
                    } else {
                        $('#source_button').find('a').css('color', '#ccc');
                        $('#source_button').find('input').prop('disabled', true);
                    }
                   // 切换active样式
                    $('#source_type .list-group-item').removeClass('active');
            		$(this).addClass('active');
                    $('#source_type button span:eq(0)').text($(this).text());
                    SOURCE.current_ST = $(this).attr('data-st')
                    $(".galleryDiv>.viewer-container").remove();
                    SOURCE.show($(this).attr('data-type'));
                }
                $('.button_add_file').attr("disabled", true);
                $('.a_add_file').css({"color":"gray"});
            });
            $('#source_type').find('a:eq(0)').click();
        });
        task_list({token: token, work_id: work_id, callback: function (r) {
                if (r.error_no == '200') {
                    show_actions(r.result);
                    $(".pop_sendtask").on('click',function(){
                        $('#pop_sendtask').modal('show');
                    });
                    $(".pop_returnmoney").on('click',function(){
                        $('#pop_returnmoney').modal('show');
                    });
                    $(".pop_finance").on('click',function(){
                        $('#pop_finance').modal('show');
                    });
                    $(".pop_copytask").on('click',function(){
                        $('#pop_copytask').modal('show');
                    });
                    $(".pop_gps").on('click',function(){
                        $('#pop_gps').modal('show');
                    });
                    $(".pop_mortgage").on('click',function(){
                        $('#pop_mortgage').modal('show');
                    });
                    $(".pop_applyremittance").on('click',function(){
                        $('#pop_applyremittance').modal('show');
                    });
                } else {
                    var modal = $('#myModal');
                    modal.find('.modal-body').text('获取信息失败！');
                    modal.modal('show');
                }
            }
        });
    })
</script>
@endsection
