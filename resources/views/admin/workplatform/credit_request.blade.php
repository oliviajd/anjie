@include('common.title')
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload_ec8bd4e.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui_8be8b42.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-noscript_77b97d2.css"></noscript>
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui-noscript_f95c011.css"></noscript>
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
               <!--  <section class="content-header">
                    <h1>
                        新增业务申请
                        <small>add users</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/Admin"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="/Admin/business/index">业务申请</a></li>
                        <li class="active">新增业务申请</li>
                    </ol>
                </section> -->
</div>
<div class="container1">
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">客户基本资料</h3>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            个人信息
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row margin-bottom">
                            <div class="col-md-11">
                                <div class="input-group">
                                    <span class="input-group-addon">★客户姓名</span>
                                    <input type="text" class="form-control customer_name" placeholder="请输入客户姓名">
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom">
                            <div class="col-md-11">
                                <div class="input-group">
                                    <span class="input-group-addon">★客户电话</span>
                                    <input type="text" name="customer_telephone" class="form-control customer_telephone" maxlength="11" placeholder="请输入客户手机号" onKeyUp="value=value.replace(/[^\d]/g,'')">
                                </div>
                            </div>
                        </div>
                        <div class="row margin-bottom">
                            <div class="col-md-11">
                                <div class="input-group">
                                    <span class="input-group-addon">★身份证号</span>
                                    <input type="text" class="form-control customer_certificate_number" maxlength="18" placeholder="请输入客户身份证号" maxlength="18" onKeyUp="value=value.replace(/[^\d|xX]/g,'')">
                                </div>
                            </div>
                        </div>
                         <div class="row margin-bottom">
                            <div class="col-md-11">
                                <div class="input-group">
                                    <span class="input-group-addon">★贷款银行</span>
                                    <select name="loan_bank" class="form-control loan_bank">
                                        <option value="01" >济南市中工行</option>
                                        <option value="02" >济南乐源支行</option>
                                        <option value="03" >临沂经开行</option>
                                        <option value="04" >杭州朝晖支行</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            来源信息
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row margin-bottom">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">★客户来源</span>
                                    <select  id="merchant_name" class="form-control merchant_name">
                                        <option value="1">经销商</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">★产品名称</span>
                                    <select name="product_name" id="product_name" class="form-control product_name">
                                        <option value="">请选择</option>
                                        <option value="1">新车</option>
                                        <option value="2">二手车</option>
                                    </select>  
                                </div>
                            </div> 
                        </div>
                    </div>
                <div class="box-body">
                    <div class="form-group" id="submit">
                        <button type="submit" data-status="2" class="btn btn-default credit_request_reset" style="width: 100px;height: 40px;">
                            重置
                        </button>
                        <button type="submit" data-status="1" class="btn btn-success credit_request_submit" style="width: 100px;height: 40px;margin-left: 10px;">
                            提交申请
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
        </div>
        <div class="col-md-6">
            <div style="position: fixed;margin-right: 15px;" id="source_window">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">客户征信资料</h3>
                    </div>
                    <div class="box-body">
                        <div class="input-group">
                                <span id="source_type1" class="input-group-addon" data-class="1">★身份证</span>
                                <input type="text" class="form-control" placeholder="" disabled=disabled>
                                <span class="input-group-addon">
                                    <form id="source" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
                                        <a style="position:relative; overflow:hidden;display: inline-block;" class="a_add_file"><i class="fa  fa-cloud-upload"></i> 添加
                                            <input type="file" name="file" multiple="" accept="image/gif,image/jpg,image/jpeg,image/png" style="position:absolute; right:0; top:0; font-size:100px; opacity:0; filter:alpha(opacity=0);" class="button_add_file">
                                        </a>
                                    </form>
                                </span>
                                <span class="input-group-addon source_length" data-class="1">共0张</span>
                            </div>
                        <div class="input-group">
                                <span id="source_type2" class="input-group-addon" data-class="2">★授权书</span>
                                <input type="text" class="form-control" placeholder="" disabled=disabled>
                                <span class="input-group-addon">
                                    <form id="source2" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
                                        <a style="position:relative; overflow:hidden;display: inline-block;"><i class="fa  fa-cloud-upload"></i> 添加
                                            <input type="file" name="file" multiple="" accept="image/gif,image/jpg,image/jpeg,image/png" style="position:absolute; right:0; top:0; font-size:100px; opacity:0; filter:alpha(opacity=0);">
                                        </a>
                                    </form>
                                </span>
                                <span class="input-group-addon source_length" data-class="2">共0张</span>
                            </div>
                        </div>
                        <div style="background: #ddd;">
                            <div id="galley">
                                <ul class="pictures"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('common.modal.normal')
@include('common/common')
<script src="/js/viewer.js"></script>
@include('common/upload')
<script type="text/javascript">
    var token = USER.get_token();   //用户登录的token  
    $(document).ready(function () {
        var token = USER.get_token();   //用户登录的token
        //保存身份证信息的点击事件
        $('.credit_request_reset').click(function () {
            $("input").val(""); 
            $("select").find("option[value='']").attr("selected",true);
            SOURCE.empty();
            SOURCE.show();

        });
        //提交申请
        $('.credit_request_submit').click(function () {
            $(".customer_sex").val(discriCard($('.customer_certificate_number').val()));
            //验证是否为空
            if (checkempty('customer_name, customer_certificate_number, customer_sex, customer_telephone, merchant_name, product_name,customer_address_add') == false) {
                var modal = $('#myModal');
                modal.find('.modal-body').text('请填写完整');
                modal.modal('show');
                return false;
            }
            $('.credit_request_submit').prop('disabled', true);
            //表单需要提交的参数
            var param = {
                'customer_name': $('.customer_name').val(), //客户姓名
                'customer_certificate_number': $('.customer_certificate_number').val(), //客户身份证号码
                'customer_sex': $('.customer_sex').val(), //客户性别
                'customer_telephone': $('.customer_telephone').val(), //客户手机号码
                // 'receiver_name': $('.receiver_name').val(), //受理人姓名
                // 'receiver_telephone': $('.receiver_telephone').val(), //受理人号码
                'merchant_id': $('.merchant_name').val(), //来源id
                'product_id': $('.product_name').val(), //产品id 
                'merchant_name': $(".merchant_name").find("option:selected").text(), //来源名称
                'product_name': $(".product_name").find("option:selected").text(), //产品名称
                'imgs': SOURCE._data || {},
                'token': token,
                'loan_bank' : $('.loan_bank').val(),
                // 'customer_province':$('[name=customer_province] option:selected').text(),
                // 'customer_city':$('[name=customer_city] option:selected').text(),
                // 'customer_town':$('[name=customer_town] option:selected').text(),
                // 'customer_address_add':$('[name=customer_address_add]').val(),
            }
            $.ajax({
                url:  "/Api/workplatform/creditrequestsubmit",
                data: param,
                type: "POST",
                dataType: 'json',
                success: function (r) {
                    $('.credit_request_submit').prop('disabled', false);
                    var modal = $('#myModal');
                    modal.find('.modal-body').text(r.error_msg);
                    modal.modal('show');
                    if (r.error_no == '200') {
                        $("input").val(""); 
                        $("select").find("option[value='']").attr("selected",true);
                        SOURCE.empty();
                        SOURCE.show();
                        $('.modal_close').click(function () {
                            window.location.href =window.location.href;
                        });
                    }
                },
                error:function(r){
                    $('.credit_request_submit').prop('disabled', false);
                }
            });

        });
        SOURCE.init({onRemove: function (Class) {
                $('[data-class="' + Class + '"]').next().next().next().text('共' + SOURCE.get_length(Class) + '张');
            }});
//        SOURCE.data_append();
//        SOURCE.show('idcard');
        var source_select = function (id) {
            var n = $('#' + id);
            n.parent().parent().children().each(function () {
                $(this).children().last().removeClass('text-green');
            });
            n.parent().children().last().addClass('text-green');
        }
        $('#source_type1,#source_type2,.source_length').click(function () {//图片类别发生变化时重新加载插件
            if ($(this).attr('data-class') != SOURCE.current_Class) {
                SOURCE.show($(this).attr('data-class'));
                source_select($(this).attr('id'));
            }
        });
        $('#source').fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: '/Admin/file/upload/',
            formData: [{'name': 'token', 'value': token}, {'name': 'type', 'value': 'image'}, {'name': 'sync_to_upyun', 'value': 1}],
            autoUpload: true,
            getFilesFromResponse: function (r) {
                if (r.result.error_no == 200) {
                    var Class = '1';
                    SOURCE.data_append(Class, 'image', {
                        org: r.result.result.url,
                        src: r.result.result.url,
                        alt: r.result.result.fid,
                    });
                    SOURCE.show(Class, SOURCE.get_length(Class) - 1);
                    $('#source_type1').next().next().next().text('共' + SOURCE.get_length(Class) + '张');
                    source_select('source_type1');
                } else {
                    return array();
                }
            }
        });
        $('#source2').fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: '/Admin/file/upload/',
            formData: [{'name': 'token', 'value': token}, {'name': 'type', 'value': 'image'}, {'name': 'sync_to_upyun', 'value': 1}],
            autoUpload: true,
            getFilesFromResponse: function (r) {
                if (r.result.error_no == 200) {
                    var Class = '2';
                    SOURCE.data_append(Class, 'image', {
                        org: r.result.result.url,
                        src: r.result.result.url,
                        alt: r.result.result.fid,
                    });
                    SOURCE.show(Class, SOURCE.get_length(Class) - 1);
                    $('#source_type2').next().next().next().text('共' + SOURCE.get_length(Class) + '张');
                    source_select('source_type2');
                } else {
                    return array();
                }
            }
        });
    });
</script>
@endsection