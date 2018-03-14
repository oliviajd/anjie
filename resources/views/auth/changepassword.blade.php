  @include('common.title')
  </head>
  @extends('layouts.admin_template')
  @section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">修改密码</div>

                <div class="panel-body">
                    <!-- @if (session('status')) -->
                        <div class="alert alert-success">
                            <!-- {{ session('status') }} -->
                        </div>
                    <!-- @endif -->

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.request') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="">

                        <div class="form-group">
                            <label for="password" class="col-md-4 control-label">旧密码</label>

                            <div class="col-md-6">
                                <input id="oldpassword" type="password" class="form-control" name="password" required>

                                <!-- @if ($errors->has('password')) -->
                                    <span class="help-block">
                                        <!-- <strong>{{ $errors->first('password') }}</strong> -->
                                    </span>
                                <!-- @endif -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">新密码</label>
                            <div class="col-md-6">
                                <input id="newpassword" type="password" class="form-control" name="password_confirmation" required>

                                <!-- @if ($errors->has('password_confirmation')) -->
                                    <span class="help-block">
                                        <!-- <strong>{{ $errors->first('password_confirmation') }}</strong> -->
                                    </span>
                                <!-- @endif -->
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary changepassword">
                                    修改密码
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('common/common')
<script src="/bower_components/AdminLTE/plugins/md5/jquery.md5.js"></script>
 <script type="text/javascript">
    $(document).ready(function(){
      var token = USER.get_token();
      $(document).keyup(function(e){
        if (e.which == 13) {
            $('.changepassword').click();
        }
      });
      $('.changepassword').click(function(){
        var oldpassword = $('#oldpassword').val();
        var newpassword = $('#newpassword').val();
        if (oldpassword == '') {
            alert('旧密码不能为空');
            return false;
        }
        if (newpassword == '') {
            alert('新密码不能为空');
            return false;
        }
        if (newpassword.length < 6) {
            alert('新密码不能小于6位');
            return false;
        }
        $.ajax({
               type:'post',
               url:'/auth/changepasswordpost',
               dataType: 'json',
               data:{
                'token'          : token,
                'oldpassword'    : $.md5(oldpassword),
                'newpassword'    : $.md5(newpassword),
               },
               success:function(data){
                if (data.error_no !== 200) {
                    alert(data.error_msg);
                } else {
                   alert(data.error_msg);
                   USER.clear();
                    $.ajax({
                           type:'post',
                           url:'/auth/logout',
                           dataType: 'json',
                           data:{'account' : 'www'},
                           // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
                           success:function(data){
                            if (data.error_no !== 200) {
                                alert(data.error_msg);
                            } else {
                                window.location.href = '/login';
                            }
                           }
                     });
                }
               }
         });
      });
    })
 </script>
@endsection
