@include('common.title')
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/imgviewer/viewer.min.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload_ec8bd4e.css">
<link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui_8be8b42.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-noscript_77b97d2.css"></noscript>
<noscript><link rel="stylesheet" href="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/css/jquery.fileupload-ui-noscript_f95c011.css"></noscript>
<link rel="stylesheet" href="/css/zoom.css">
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
                    @include('common.pannel.work_info_inquire')
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
                                <textarea name="inputrequest_description" class="comments form-control" rows="3" placeholder=""><?php echo $detail['inputrequest_description']?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div id="submit" class="form-group">
                        <button type="submit" data-status="2" class="btn btn-default addbusiness_submit margin-r-5">
                            暂存
                        </button>
                        <button type="submit" data-status="1" class="btn btn-success addbusiness_submit margin-r-5">
                            提交
                        </button>
                        <!-- <button type="submit" data-status="3" class="btn btn-warning addbusiness_cancel margin-r-5">
                            退件
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
@include('common/upload')
@include('common.box.source_window') 
@include('common.modal.popbox')
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
        $('.source_window').click(function () {
            $('#source_window').modal('show');
             
        });  
        $(".customer_marital_status").change(function(){ 
            if ($(".customer_marital_status").val() == '1' &&　$('.spouse_div').hasClass('hide')) {
                $('.spouse_div').removeClass("hide");
            }
            if ($(".customer_marital_status").val() !== '1' &&　($('.spouse_div').hasClass('hide') == false)) {
                $('.spouse_div').addClass("hide");
            }
            if ($(".customer_marital_status").val() !== '1' && $(".customer_marital_status").val() !== '' &&　$('.contact_div').hasClass('hide')) {
                $('.contact_div').removeClass("hide");
            }
            if (($(".customer_marital_status").val() == '1' || $(".customer_marital_status").val() == '') &&　($('.contact_div').hasClass('hide') == false)) {
                $('.contact_div').addClass("hide");
            }
        });
        $(".customer_has_bondsman").change(function(){ 
            if ($(".customer_has_bondsman").val() == '1' &&　$('.bondsman_div').hasClass('hide')) {
                $('.bondsman_div').removeClass("hide");
            }
            if ($(".customer_has_bondsman").val() !== '1' &&　($('.bondsman_div').hasClass('hide') == false)) {
                $('.bondsman_div').addClass("hide");
            }
        });
        $(".has_insurance").change(function(){ 
            if ($(".has_insurance").val() == '1' &&　$('.insurance_div').hasClass('hide')) {
                $('.insurance_div').removeClass("hide");
            }
            if ($(".has_insurance").val() !== '1' &&　($('.insurance_div').hasClass('hide') == false)) {
                $('.insurance_div').addClass("hide");
            }
        });
        $('#first_pay, #car_price').change(function(){ 
            var first_pay = $('[name=first_pay]').val();
            var car_price = $('[name=car_price]').val();
            if (first_pay.length > 0 && car_price.length > 0) {
                var first_pay_ratio = toDecimal((first_pay/car_price) * 100) + '%';
                $("#first_pay_ratio").val(first_pay_ratio);
            }
        });
        $('.first_pay').change(function(){
            var car_price = $('[name=car_price]').val();
            var first_pay = $('[name=first_pay]').val();
            var loan_prize = $('[name=loan_prize]').val();
            if (car_price.length > 0 && toDecimal(car_price) >= toDecimal(first_pay)) {
                $(".loan_prize").val(toDecimal(car_price)-toDecimal(first_pay));
            }
        });
        $('.loan_prize').change(function(){
            var car_price = $('[name=car_price]').val();
            var first_pay = $('[name=first_pay]').val();
            var loan_prize = $('[name=loan_prize]').val();
            if (car_price.length > 0 && toDecimal(car_price) >= toDecimal(loan_prize)) {
                $("#first_pay").val(toDecimal(car_price)-toDecimal(loan_prize));
                var first_pay = $('[name=first_pay]').val();
                var first_pay_ratio = toDecimal((first_pay/car_price) * 100) + '%';
                $("#first_pay_ratio").val(first_pay_ratio);
            }
        });
        var oStartDate = new Date();var oEndDate = new Date();oStartDate.setDate(oStartDate.getDate() - 7);oStartDate.setHours(0,0,0);oEndDate.setHours(23,59,59);$('#reservationtime').daterangepicker({showDropdowns:true,timePicker24Hour:true,startDate:oStartDate,endDate:oEndDate,timePicker: true, timePickerIncrement: 1, format: 'MM/DD/YYYY h:mm A'});
        loadSources(token, work_id, function (sources) {
            SOURCE.init({
                data: sources,
                onChange: function (Class) {
                    $('#source_length').text('共' + SOURCE.get_length(Class) + '张');
                },
                onRemove: function (Class, obj) {
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
                            // img_add.source_lists.push(obj);
                        }
                        img_add[Class]['source_lists'].push(obj);
                        SOURCE.show(Class, SOURCE.get_length(Class) - 1);
                        $('#source_length').text('共' + SOURCE.get_length(Class) + '张');
                    } else {
                        return [];
                    }
                }
            });
        });
        $('#submit button').click(function () {
            if ($(this).attr('data-status') !== '2') {
                if (checkempty('customer_name,customer_certificate_number,customer_telephone,customer_address,hukou,customer_marital_status,customer_has_bondsman,loan_prize,loan_date,first_pay_ratio,loan_rate,car_brand,car_type,car_vehicle_identification_number,car_price,has_insurance,customer_company_name,loan_bank') == false) {
                    var modal = $('#myModal');
                    modal.find('.modal-body').text('信息填写不完整');
                    modal.modal('show');
                    return false;
                }
                if (checktelephone('customer_telephone') == false) {
                    var modal = $('#myModal');
                    modal.find('.modal-body').text('手机号格式不正确');
                    modal.modal('show');
                    return false;
                }
                if (checkcardid('customer_certificate_number') == false) {
                    var modal = $('#myModal');
                    modal.find('.modal-body').text('身份证号码格式不正确');
                    modal.modal('show');
                    return false;
                }
                if ($('[name=customer_marital_status] option:selected').val() == '1') {
                    if (checkempty('spouse_name,spouse_certificate_number,spouse_telephone') == false) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('配偶信息填写不完整');
                        modal.modal('show');
                        return false;
                    }
                    if (checktelephone('spouse_telephone') == false) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('配偶手机号码格式不正确');
                        modal.modal('show');
                        return false;
                    }
                    if (checkcardid('spouse_certificate_number') == false) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('配偶身份证号码格式不正确');
                        modal.modal('show');
                        return false;
                    }
                }
                if ($('[name=customer_marital_status] option:selected').val() == '2') {
                    // if (checkempty('contacts_man_name,contacts_man_relationship,contacts_man_certificate_number,contacts_man_telephone') == false) {
                    //     var modal = $('#myModal');
                    //     modal.find('.modal-botdy').text('联系人信息填写不完整');
                    //     modal.modal('show');
                    //     return false;
                    // }
                    if ($('[name=contacts_man_telephone]').val() !== '' && checktelephone('contacts_man_telephone') == false) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('联系人手机号码格式不正确');
                        modal.modal('show');
                        return false;
                    }
                    if ($('[name=contacts_man_certificate_number]').val() !== '' && checkcardid('contacts_man_certificate_number') == false) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('联系人身份证号码格式不正确');
                        modal.modal('show');
                        return false;
                    }
                }
                if ($('[name=customer_has_bondsman] option:selected').val() == '1') {
                    // if (checkempty('bondsman_name,bondsman_certificate_number,bondsman_telephone') == false) {
                    //     var modal = $('#myModal');
                    //     modal.find('.modal-body').text('担保人信息填写不完整');
                    //     modal.modal('show');
                    //     return false;
                    // }
                    if ($('[name=bondsman_telephone]').val() !== '' && checktelephone('bondsman_telephone') == false) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('担保人手机号码格式不正确');
                        modal.modal('show');
                        return false;
                    }
                    if ($('[name=bondsman_certificate_number]').val() !== '' && checkcardid('bondsman_certificate_number') == false) {
                        var modal = $('#myModal');
                        modal.find('.modal-body').text('担保人身份证号码格式不正确');
                        modal.modal('show');
                        return false;
                    }
                }
                // if ($('[name=has_insurance] option:selected').val() == '1') {
                //     if (checkfloat('commercial_insurance,compulsory_insurance,vehicle_vessel_tax,gross_premium') == false) {
                //         var modal = $('#myModal');
                //         modal.find('.modal-body').text('数字类型不正确');
                //         modal.modal('show');
                //         return false;
                //     }
                // }
                if (checkfloat('car_price,loan_prize,first_pay,loan_rate') == false) {
                    var modal = $('#myModal');
                    modal.find('.modal-body').text('数字类型不正确');
                    modal.modal('show');
                    return false;
                }
                var car_vehicle_identification_number = $('.car_vehicle_identification_number').val();
                if(!(/^[a-zA-Z0-9]{17}$/.test(car_vehicle_identification_number))){ 
                    var modal = $('#myModal');
                    modal.find('.modal-body').text('车架号必须为17位的数字和英文');
                    modal.modal('show');
                    return false; 
                }
                var car_price=$('[name=car_price]').val();
                var first_pay=$('[name=first_pay]').val();
                var loan_prize=$('[name=loan_prize]').val();
                if ((parseFloat(first_pay) + parseFloat(loan_prize)) != parseFloat(car_price)) {
                    var modal = $('#myModal');
                    modal.find('.modal-body').text('车辆价格需要等于贷款额和首付金额的总和');
                    modal.modal('show');
                    return false;
                }
            }
            $('#submit button').prop('disabled', true);
            var status = $(this).attr('data-status');
            var data = {
                token: token,
                work_id: work_id,
                item_instance_id: item_instance_id,
                type: status,
                imgs:JSON.stringify({'add':img_add,'delete':img_delete}),
                //基本资料
                customer_marital_status:$('[name=customer_marital_status] option:selected').val(),
                customer_has_bondsman:$('[name=customer_has_bondsman] option:selected').val(),
                customer_name:$('[name=customer_name]').val(),
                customer_certificate_number:$('[name=customer_certificate_number]').val(),
                customer_telephone:$('[name=customer_telephone]').val(),
                customer_address:$('[name=customer_address]').val(),
                hukou:$('[name=hukou]').val(),
                customer_company_name:$('[name=customer_company_name]').val(),
                customer_company_phone_number:$('[name=customer_company_phone_number]').val(),
                company_address:$('[name=company_address]').val(),
                //配偶信息
                spouse_name:$('[name=spouse_name]').val(),
                spouse_certificate_number:$('[name=spouse_certificate_number]').val(),
                spouse_telephone:$('[name=spouse_telephone]').val(),
                spouse_company_name:$('[name=spouse_company_name]').val(),
                spouse_company_telephone:$('[name=spouse_company_telephone]').val(),
                spouse_company_address:$('[name=spouse_company_address]').val(),
                //联系人信息
                contacts_man_name:$('[name=contacts_man_name]').val(),
                contacts_man_relationship:$('[name=contacts_man_relationship] option:selected').val(),
                contacts_man_certificate_number:$('[name=contacts_man_certificate_number]').val(),
                contacts_man_telephone:$('[name=contacts_man_telephone]').val(),
                //担保人信息
                bondsman_name:$('[name=bondsman_name]').val(),
                bondsman_certificate_number:$('[name=bondsman_certificate_number]').val(),
                bondsman_telephone:$('[name=bondsman_telephone]').val(),
                bondsman_company_name:$('[name=bondsman_company_name]').val(),
                bondsman_company_telephone:$('[name=bondsman_company_telephone]').val(),
                bondsman_company_address:$('[name=bondsman_company_address]').val(),
                //商品信息
                constract_no:$('[name=constract_no]').val(),
                car_brand:$('[name=car_brand]').val(),
                car_type:$('[name=car_type]').val(),
                car_price:$('[name=car_price]').val(),
                car_vehicle_identification_number:$('[name=car_vehicle_identification_number]').val(),
                loan_prize:$('[name=loan_prize]').val(),
                loan_date:$('[name=loan_date] option:selected').text(),
                first_pay:$('[name=first_pay]').val(),
                first_pay_ratio:$('[name=first_pay_ratio]').val(),
                loan_rate:$('[name=loan_rate]').val(),
                has_insurance:$('[name=has_insurance] option:selected').val(),
                loan_bank:$('[name=loan_bank] option:selected').val(),
                insurance_company:$('[name=insurance_company]').val(),
                commercial_insurance:$('[name=commercial_insurance]').val(),
                compulsory_insurance:$('[name=compulsory_insurance]').val(),
                vehicle_vessel_tax:$('[name=vehicle_vessel_tax]').val(),
                gross_premium:$('[name=gross_premium]').val(),
                //费用信息
                total_expense:$('[name=total_expense]').val(),
                //备注信息
                inputrequest_description:$('[name=inputrequest_description]').val(),
            }
            $.ajax({
                url: '/Api/workplatform/inputrequest',
                type:'post',
                data: data,
                dataType: 'json',
                'success': function (r) {
                    $('#submit button').prop('disabled', false);
                    var modal = $('#myModal');
                    modal.find('.modal-body').text(r.error_msg);
                    modal.modal('show');
                    if (r.error_no == '200') {
                        $('.modal_close').click(function () {
                            window.location.href = '/Admin/work/inputrequest';
                        });
                    }
                },
                'error': function (r) {
                    $('#submit button').prop('disabled', false);
                }
            });
        });
    })
    $('#customer_certificate_valid_date').daterangepicker({singleDatePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
    $('#first_license_date').daterangepicker({singleDatePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
    $('#evaluate_date').daterangepicker({singleDatePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
    $(document).ready(function () {
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
        
    });

</script>
@endsection
