<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>AdminLTE 2 </title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!--include('common.css')-->		
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<button type="submit" class="btn btn-default stop" style="width: 140px;height: 40px;">
        停止
     </button>
   </body>
   <script type="text/javascript" src="/admin/js/develop/jquery-3.1.1.min.js"></script>
   <script type="text/javascript">
   		$(function(){ 
			var handler = function(){ 
				$.ajax({
                url:  'http://dev.anjie.com:8080/system/migration/imigratefile',
                type: 'post',
                data: '' ,
                dataType: 'json',
                'success': function (r) {
                    handler();
                },
                'error': function (r) {
                    // handler();
                }
            });
			} 
			// handler();
			// var timer = setInterval(handler , 1500); 
			var clear = function(){ 
				clearInterval(timer); 
			} 
			$('.stop').click(function () {
				clear();
			});
			}); 

   </script>
</html>
