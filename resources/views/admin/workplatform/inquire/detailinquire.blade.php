@include('common.title')
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/imgviewer/viewer.min.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload_ec8bd4e.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui_8be8b42.css">
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-noscript_77b97d2.css"></noscript>
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui-noscript_f95c011.css"></noscript>
<link rel="stylesheet" href="/css/zoom.css">
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
</style>
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
    <!-- Content Header (Page header) -->
    <!-- <section class="content-header">
        <h1>
            征信报告详情页
            <small>inquire</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
            <li><a href="/Admin/work/inquire">征信报告</a></li>
            <li class="active">征信报告详情页</li>
        </ol>
    </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">客户基本资料</h3>
                </div>
                <div class="box-body">
                    @include('common.pannel.work_info_from2')
                    @include('common.pannel.work_info_basic')
                    @include('common.pannel.work_info_inquire2')
                    
                    <div class="form-group" id="submit">
                        <!-- <button type="submit" data-status="3" class="btn btn-warning" style="width: 100px;height: 40px;margin-left: 10px;">
                            退件
                        </button>
                        <button type="submit" data-status="2" class="btn btn-default" style="width: 100px;height: 40px;margin-left: 10px;">
                            暂存
                        </button> -->
                        <button type="submit" data-status="1" class="btn btn-success" style="width: 100px;height: 40px;margin-left: 10px;">
                            提交
                        </button>                                               
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @include('common.box.source_window2')
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg text-center" id="imgModal"tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
	<div class="modal-dialog modal-lg" style="display: inline-block; width: auto;">
		<div class="modal-content">
			<img  id="imgInModalID" src="" >
		</div>
	</div>
</div>
@include('common.modal.normal')
@include('common/common')
@include('common/upload')
<script src="/js/viewer.js"></script>
<script src="/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="/bower_components/AdminLTE/plugins/moment/moment.min.js"></script>
<script src="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
    var token = USER.get_token();
    var work_id = <?php echo intval($detail['id']) ?>;
    var item_instance_id = <?php echo intval($item_instance_id) ?>;
    var img_add = {};
    var img_delete = {};
    $(function () {
        var oStartDate = new Date();var oEndDate = new Date();oStartDate.setDate(oStartDate.getDate() - 7);oStartDate.setHours(0,0,0);oEndDate.setHours(23,59,59);$('#reservationtime').daterangepicker({showDropdowns:true,timePicker24Hour:true,startDate:oStartDate,endDate:oEndDate,timePicker: true, timePickerIncrement: 1, format: 'MM/DD/YYYY h:mm A'});
        loadSources(token, work_id, function (sources) {
        	// 先填入征信图片
        	if(sources.hasOwnProperty('3')){
        		var creditSource = sources['3']['source_lists'];
	        	for(var s in creditSource){
	        		$("#creditImgList").html($("#creditImgList").html() + "<div class='col-xs-6 col-md-3'><div class='thumbnail'><div style='height: 100px;overflow: hidden;'><img src='" + creditSource[s].src + "' alt='" + creditSource[s].src + "' style='width: 100%; height: 100%;' onclick='imgModal(this.src)'></div><div class='caption'><p><div class='btn btn-primary delThisBtn' data-src='" + creditSource[s].src + "' data-id='" + creditSource[s].alt + "' role='button'>删除</div></p></div></div></div>");
	        	}
        	} else {
        	}
        	
            SOURCE.init({
                data: sources,
                onChange: function (Class) {
                    $('#source_length').text('共' + SOURCE.get_length(Class) + '张');
                },
                onRemove: function (Class, obj) {
                	console.log(JSON.stringify(obj))
                    if (!img_delete.hasOwnProperty(Class)) {
                        img_delete[Class] = {
                            'source_type': SOURCE._data[Class]['source_type'],
                            'source_lists': [],
                        }
                        // img_delete.data_append(Class, SOURCE.current_ST, obj);
                    }
                    img_delete[Class]['source_lists'].push(obj);
                }
            });
            $("#creditImgList").on('click','.delThisBtn',function(){
            	var Class = '3';
            	$(this).parent().parent().parent().remove();
            	var obj = {
            		org: $(this).attr('data-src'),
                    src: $(this).attr('data-src'),
                    alt: $(this).attr('data-id'),
            	}
            	if (!img_delete.hasOwnProperty(Class)) {
                    img_delete[Class] = {
                        'source_type': SOURCE._data[Class]['source_type'],
                        'source_lists': [],
                    }
                    // img_delete.data_append(Class, SOURCE.current_ST, obj);
                }
                img_delete[Class]['source_lists'].push(obj);
                $('#source_length2').text('共' + $('#creditImgList>div').length + '张');
            })
            SOURCE.show();
            $('#source_type').find('a').click(function () {//图片类别发生变化时重新加载插件
                if ($(this).attr('data-type') != SOURCE.current_Class) {
                    if ($(this).attr('data-edit') == 1) {
                        $('#source_button').css('color', '#3c8dbc');
                    } else {
                        $('#source_button').find('a').css('color', '#ccc');
                    }
                    $('#source_type button span:eq(0)').text($(this).text());
                    SOURCE.current_ST = $(this).attr('data-st')
                    $(".galleryDiv>.viewer-container").remove();
                    SOURCE.show($(this).attr('data-type'));
                }
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
                            img_add[Class] = {
                                'source_type': SOURCE.current_ST,
                                'source_lists': []
                            };
                            // img_add = {
                            //     'source_type': SOURCE.current_ST,
                            //     'source_lists': [],
                            // }
                            img_add[Class].source_lists.push(obj);
                        }
//                      img_add[Class]['source_lists'].push(obj);
                        SOURCE.show(Class, SOURCE.get_length(Class) - 1);
                        $('#source_length').text('共' + SOURCE.get_length(Class) + '张');
                    } else {
                        return [];
                    }
                }
            });
            // 征信照片上传
            $('#source2').fileupload({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: INIT.API_HOST + '/Admin/file/upload/',
                formData: [{'name': 'token', 'value': token}, {'name': 'type', 'value': SOURCE.current_ST}, {'name': 'sync_to_upyun', 'value': 1}],
                autoUpload: true,
                getFilesFromResponse: function (r) {
                    if (r.result.error_no == 200) {
                        var Class = '3';//SOURCE.current_Class; 类别直接为征信报告
                        var obj = {
                            org: r.result.result.url,
                            src: r.result.result.url,
                            alt: r.result.result.fid,
                        };
                        SOURCE.data_append(Class, SOURCE.current_ST, obj);
                        if (!img_add.hasOwnProperty(Class)) {
                            img_add[Class] = {
                                'source_type': SOURCE.current_ST,
                                'source_lists': []
                            };
                            // img_add = {
                            //     'source_type': SOURCE.current_ST,
                            //     'source_lists': [],
                            // }
                            // img_add.source_lists.push(obj);
                        }
                        img_add[Class]['source_lists'].push(obj);
                        // 添加新征信图片
				        $("#creditImgList").html($("#creditImgList").html() + "<div class='col-xs-6 col-md-3'><div class='thumbnail'><div style='height: 100px;overflow: hidden;'><img src='" + obj.src + "' alt='" + obj.src + "' style='width: 100%; height: 100%;' onclick='imgModal(this.src)'></div><div class='caption'><p><div class='btn btn-primary delThisBtn' data-src='" + obj.src + "' data-id='" + obj.alt + "' role='button'>删除</div></p></div></div></div>");
//				        
//                      SOURCE.show(Class, SOURCE.get_length(Class) - 1);
                        $('#source_length2').text('共' + $('#creditImgList>div').length + '张');
                    } else {
                        return [];
                    }
                }
            });
        });
        $('#submit button').click(function () {
            if (checkempty('inquire_result,inquire_description') == false) {
                var modal = $('#myModal');
                modal.find('.modal-body').text('请填写完整');
                modal.modal('show');
                return false;
            }
            $('#submit button').prop('disabled', true);
            var status = $(this).attr('data-status');
            var data = {
                token: token,
                work_id: work_id,
                item_instance_id: item_instance_id,
                type: status,
                inquire_result: $('select[name=inquire_result]').val(),
                inquire_description: $('textarea[name=inquire_description]').val(),
                imgs:JSON.stringify({'add':img_add,'delete':img_delete}),
            }
            $.ajax({
                url:  '/Api/workplatform/inquire',
                data: data,
                dataType: 'json',
                'success': function (r) {
                    $('#submit button').prop('disabled', false);
                    var modal = $('#myModal');
                    modal.find('.modal-body').text(r.error_msg);
                    modal.modal('show');
                    if (r.error_no == '200') {
                        $('.modal_close').click(function () {
                            window.location.href = '/Admin/work/inquire';
                        });
                    }
                },
                'error': function (r) {
                    $('#submit button').prop('disabled', false);
                }
            });
        });
    })
    function imgModal(src){
    	var modal = $('#imgModal');
        modal.find('#imgInModalID').prop('src',src)
        modal.modal('show');
    }
</script>
@endsection
