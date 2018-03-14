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
  @section('content')
      <head>  
              <meta charset="utf-8">  
              <meta http-equiv="X-UA-Compatible" content="IE=edge">  
              <meta name="viewport" content="width=device-width, initial-scale=1">  
              <meta name="_token" content="{!! csrf_token() !!}"/>  
              
      </head>
      <div class="fl">
              <input  type="email" id="email" name="email" value="filetoken生成">
                 <button type="button" class="make">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="文件分块上传">
                 <button type="button" class="getuploadfile">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="上传到又拍云">
                 <button type="button" class="upyunpost">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="上传到银行">
                 <button type="button" class="bankpost">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="上传到银行的图像确认接口">
                 <button type="button" class="bankimageconfirm">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="queryday" value="日查询">
                 <button type="button" class="queryday">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="查询">
                 <button type="button" class="query">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="审批提交">
                 <button type="button" class="divapply">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="图像确认">
                 <button type="button" class="imageconfirm">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <div class="fl">
              <input  type="email" id="email" name="email" value="指令提交">
                 <button type="button" class="requestcommit">Click Me!</button>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">   
      </div>
      <!-- <form method="post" role="form" action="#">
          {!! csrf_field() !!}
          <p><input type="file" id="uploadfile" name="uploadfile" />
              <input type="button" id="button-upload-file" value="上传" /></p>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
      </form>
      <input type="button" name="filedownload" id = "filedownload" value="文件下载">
      <input type="button" name="fileupload" id = "fileupload" value="文件上传"> -->

      <script type="text/javascript" src="{{asset('/js/develop/jquery-3.1.1.min.js')}}"></script>
      <script type="text/javascript" src="{{asset('/js/develop/file.js')}}"></script>
      @include('common/common')

  @endsection