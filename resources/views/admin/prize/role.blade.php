@extends('layouts.admin_template')
<head>  
        <meta charset="utf-8">  
        <meta http-equiv="X-UA-Compatible" content="IE=edge">  
        <meta name="viewport" content="width=device-width, initial-scale=1">  
        <meta name="_token" content="{!! csrf_token() !!}"/>  
        
</head>

<div class="fl">
        <input  type="email" id="email" name="email" value="filetoken生成">
           <span id="pass1" style="display:none;">邮箱格式不正确</span> 
           <span id="pass2" style="display:none;">邮箱格式正确</span>
           <button type="button" class="button-ajax-test">Click Me!</button>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
</div>
<form method="post" role="form" action="#">
    {!! csrf_field() !!}
    <p><input type="file" id="uploadfile" name="uploadfile" />
        <input type="button" id="button-upload-file" value="上传" /></p>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
<input type="button" name="filedownload" id = "filedownload" value="文件下载">
<input type="button" name="fileupload" id = "fileupload" value="文件上传">

<!-- <form id="form1"  action="fileupload" method="post" enctype="multipart/form-data" >
<div>
    <a id="addAttach" href="#">添加上传文件</a>
    <div id="files">
    <input type="file" name="file1"/>
    </div>
    <input type="submit" id="btnSend" text="发送" value="发送" />
    </div>
</form> -->
    <script type="text/javascript" src="{{asset('/js/develop/jquery-3.1.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/develop/ajaxfileupload.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/develop/my.js')}}"></script>
   <!--  <script type="text/javascript">
    $(document).ready(function(){
      $('.button-ajax-test').click(function(){
        alert('test');
         $.ajax({
               type:'post',
               url:'rolepost',
               dataType: 'JSON',
               data:{'date':'abcd'},
               headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                console.log(data);
               }
            });
         });
      })
    </script> -->