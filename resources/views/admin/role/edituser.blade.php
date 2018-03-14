    @include('common.title')
</head>
@extends('layouts.admin_template')
@section('content') 
<div class="content-wrappers">
                <!-- Content Header (Page header) -->
               <!--  <section class="content-header">
                    <h1>
                        编辑用户
                        <small>add users</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
                        <li><a href="#">角色管理</a></li>
                        <li class="active">编辑用户</li>
                    </ol>
                </section> -->
</div><br/>
<div class="container1">
    <div class="row">
        <div class="col-md-6  col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">编辑用户</div>
                <div class="panel-body form-horizontal">
                    <!-- <form> -->
                        <!-- {{ csrf_field() }} -->

                        <div class="form-group{{ $errors->has('account') ? ' has-error' : '' }}">
                            <label for="account" class="col-md-4 control-label">账号(TEL)</label>

                            <div class="col-md-6">
                                <input id="account" type="text" class="form-control account" name="account" value="<?php echo $account;?>" required autofocus>

                                @if ($errors->has('account'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('account') }}</strong>
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
                                <input id="area_add" type="text" class="form-control area_add" name="area_add" value="<?php echo $area_add;?>" required>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">姓名</label>

                            <div class="col-md-6">
                                <input id="name" type="name" class="form-control name" name="name" value="<?php echo $name;?>" required>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary editusersubmit">
                                    提交
                                </button>
                                <button type="" class="btn cancel">
                                    取消
                                </button>
                            </div>
                        </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
        
    </div>
</div>
@include('common/common')
 <script type="text/javascript">
    $(document).ready(function(){
        var provincecode = "<?php echo $provincecode?>";
        var province = "<?php echo $oldprovince?>";
        var citycode = "<?php echo $citycode?>";
        var city = "<?php echo $oldcity?>";
        var town = "<?php echo $oldtown?>";
        if (citycode == '') {
            citycode = '0';
        }
        if (town == '') {
            town = '0';
        }
        FillSheng('sheng', provincecode, province);    //填充省
        FillShi('sheng', 'shi', citycode, city);  //填充市
        FillQu('shi', 'qu', town);//填充区
        //选中项变化    
         $("#sheng").change(function(){            //当省的内容改变的时候，触发市和区的内容改变     
            FillShi('sheng', 'shi', '', '');  //填充市
            FillQu('shi', 'qu', '');//填充区
        })
        $("#shi").change(function(){               //当市的内容改变的时候，触发区的内容改变
            FillQu('shi', 'qu', '');//填充区
        })   
      $('.cancel').click(function(){
        window.location.href = '/Admin/role/adduser';
      });
      $('.editusersubmit').click(function(){
        var oldaccount = "<?php echo $account;?>";
        if(checkempty('account, sheng, shi, qu, name') == false) {
            return false;
        }
        $.ajax({
               type:'post',
               url:'/Admin/role/edituserpost',
               dataType: 'json',
               data:{
                'account' : $('#account').val(),
                'name'    : $('#name').val(),
                'province': $('.sheng option:selected').text(),
                'city'    : $('.shi option:selected').text(),
                'town'    : $('.qu option:selected').text(),
                'area_add':$('#area_add').val(),
                'oldaccount':oldaccount,
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
