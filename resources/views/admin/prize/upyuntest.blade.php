<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{!! csrf_token() !!}"/>  
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('/css/normalize.css')}}">
    <script type="text/javascript" src="{{asset('/js/develop/jquery-3.1.1.min.js')}}"></script>
    <style>
        form {
            margin: 30px 0;
        }
        .submit {
            display: inline-block;
            width: 100px;
            background: #02a3c6;
            border: none;
            color: #fff;
            line-height: 40px;
            text-align: center;
            cursor: pointer;
        }
    </style>
</head>
<body>

<form action="http://v0.api.upyun.com/sdkimg" id="demoForm" method="POST" enctype="multipart/form-data">
    <fieldset>
        <legend>Client Upload Demo</legend>
        <input name="file" type="file">
        <input type="button" value="submit" class="submit" id="upload">
    </fieldset>
</form>

<script>
    // 文件保存的路径
    // var save_path = '/test/filename.txt';
    $('#upload').on('click', function() {
        $.ajax({
               type:'post',
               url:'upyunposttest',
               dataType: 'JSON',
               data:{'date':'abcd'},
               headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
</script>
</body>
</html>
