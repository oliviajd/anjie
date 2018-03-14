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
                        人工审核（一审）
                        <small>inquire</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/Admin/work/artificialone">人工审核（一审）</a></li>
                        <li class="active">人工审核（一审）</li>
                    </ol>
                </section> -->
</div><br/> 
<div class="container1">
    <div class="row">
        <div class="col-md-7">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">新车</h3>
                    <div class="pull-right" style="margin-right: 10px;">
                        <button type="submit" class="btn btn-default source_window pull-right" style="width: 140px;height: 40px;">
                                客户影像资料
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    @include('common.pannel.work_info_inquire')
                    @include('common.pannel.work_info_ficodata')
                    @include('common.pannel.work_info_from')
                    @include('common.pannel.work_info_basic2')
                    @include('common.pannel.work_info_spouse')
                    @include('common.pannel.work_info_contact')
                    @include('common.pannel.work_info_bondsman')
                    @include('common.pannel.work_info_goods')
                    @include('common.pannel.work_info_cost')
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title" style="color:#F00">
                                备注信息
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea name="inputrequest_description" class="comments form-control" rows="3" placeholder="" disabled="disabled"><?php echo $detail['inputrequest_description']?></textarea>
                            </div>
                        </div>
                    </div>
                    @include('common.pannel.work_info_artificialone_opinion')
                    <div class="form-group" id="submit">
                        <button type="" class="btn cancel">
                            取消
                        </button>
                        <!-- <div id="submit"> -->
                        <button type="submit" data-type="1" class="btn btn-success margin-r-5 submit">
                             提交
                        </button>
                        <!-- </div> -->
                        
                       <!--  <button type="submit" data-type="2" class="btn btn-default margin-r-5">
                            暂存
                        </button>
                        <button type="submit" data-type="4" class="btn btn-default margin-r-5">
                            取消认领
                        </button> -->
                    </div>
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
        $(".artificial_status").change(function(){ 
            if ($(".artificial_status").val() == '2' &&　$('.refuse_div').hasClass('hide')) {
                $('.refuse_div').removeClass("hide");
            }
            if ($(".artificial_status").val() !== '2' &&　($('.refuse_div').hasClass('hide') == false)) {
                $('.refuse_div').addClass("hide");
            }
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
            $('#source').fileupload({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: INIT.API_HOST + '/Admin/file/upload/',
                formData: [{'name': 'token', 'value': token}, {'name': 'type', 'value': SOURCE.current_ST}, {'name': 'sync_to_upyun', 'value': 1}],
                autoUpload: true,
                getFilesFromResponse: function (r) {
                    if (r.result.error_no == 200) {
                        var Class = SOURCE.current_Class;
                        var obj = {
                            org: r.result.result.url,
                            src: r.result.result.url,
                            alt: r.result.result.fid,
                        };
                        SOURCE.data_append(Class, SOURCE.current_ST, obj);
                        if (!img_add.hasOwnProperty(Class)) {
                            img_add = {
                                'source_type': SOURCE.current_ST,
                                'source_lists': [],
                            }
                            img_add.source_lists.push(obj);
                        }
                        SOURCE.show(Class, SOURCE.get_length(Class) - 1);
                        $('#source_length').text('共' + SOURCE.get_length(Class) + '张');
                    } else {
                        return [];
                    }
                }
            });
        });
        $('.cancel').click(function(){
            window.location.href = '/Admin/work/artificialone';
        });
        $('#submit button.submit').click(function () {
            $('#submit button').prop('disabled', true);
            var type = $(this).attr('data-type');
            var artificial_refuse_reason = [];
            $('[name=artificial_refuse_reason]:checked').each(function(){
                artificial_refuse_reason.push($(this).val());
            });
            var artificial_status = $('[name=artificial_status]').val();
            if (artificial_status == '2' && artificial_refuse_reason.length == 0) {
                var modal = $('#myModal');
                modal.find('.modal-body').text('拒绝理由不能为空');
                modal.modal('show');
                $('#submit button').prop('disabled', false);
                return false;
            }
            if (artificial_status == '3' && $('[name=artificial_description]').val() == '') {
                var modal = $('#myModal');
                modal.find('.modal-body').text('待补件备注不能为空');
                modal.modal('show');
                $('#submit button').prop('disabled', false);
                return false;
            }
            var data = {
                token: token,
                work_id: work_id,
                item_instance_id: item_instance_id,
                type: type,
                artificial:1,//一审
                artificial_status:$('[name=artificial_status]').val(),
                artificial_refuse_reason:artificial_refuse_reason.join(','),
                artificial_description:$('[name=artificial_description]').val()
            }
            $.ajax({
                url: '/Api/workplatform/artificial',
                type: 'post',
                data: data,
                dataType: 'json',
                'success': function (r) {
                    $('#submit button').prop('disabled', false);
                    var modal = $('#myModal');
                    modal.find('.modal-body').text(r.error_msg);
                    modal.modal('show');
                    if (r.error_no == '200') {
                        $('.modal_close').click(function () {
                            window.location.href = '/Admin/work/artificialone';
                        });
                    }
                },
                'error': function (r) {
                    $('#submit button').prop('disabled', false);
                }
            });
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
        })
    })
</script>
@endsection
