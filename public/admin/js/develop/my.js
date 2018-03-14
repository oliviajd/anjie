$(function () {

	// $('.button-ajax-test').click(function(){
 //         $.ajax({
 //               type:'post',
 //               url:'rolepost',
 //               dataType: 'JSON',
 //               data:{'date':'abcd'},
 //               headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
 //               success:function(data){
 //                // console.log(data);
 //               }
 //         });
 //    });
    $('.button-ajax-test').click(function(){
         $.ajax({
               type:'post',
               url:'bankimageconfirm',
               dataType: 'json',
               data:{'filename' : 'test.jpg'},
               // beforeSend: function () {
                
               // },
               // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
//上传文件
    $('#button-upload-file').click(function(){
    	var id = 'uploadfile';
    	var str = document.getElementById(id).value;
    	if (str.length>0) {
    		ajaxFileUpload(id);
    	} else {
    		alert('文件不能为空');
    	}
    });
//上传文件的具体实现
    function ajaxFileUpload(id) {
            $.ajaxFileUpload
            (
                {
                	headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
                	type: 'POST',
                	data:{_token:$('meta[name="_token"]').attr('content')},
                    url: 'filepost', //用于文件上传的服务器端请求地址
                    secureuri: false, //是否需要安全协议，一般设置为false
                    fileElementId: id, //文件上传域的ID
                    dataType: 'json', //返回值类型 一般设置为json
                    success: function (data, status)  //服务器成功响应处理函数
                    {
                    	console.log(data);
                        // $("#img1").attr("src", data.imgurl);
                        if (typeof (data.error) != 'undefined') {
                            if (data.error != '') {
                                alert(data.error);
                            } else {
                                alert(data.msg);
                            }
                        }
                    },
                    error: function (data, status, e)//服务器响应失败处理函数
                    {
                        alert(e);
                    }
                }
            )
            return false;
    }
    //文件下载
    $('#filedownload').click(function(){
        var sourcefile = 'http://qlogo2.store.qq.com/qzone/393183837/393183837/50';
        var filename = 'banner.jpg';
        $.ajax({
               type:'post',
               url:'downloadfile',
               dataType: 'JSON',
               data:{'sourcefile':sourcefile, 'filename' : filename},
               headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
    $('#fileupload').click(function(){
        var file = '';
        $.ajax({
               type:'post',
               url:'uploadfile',
               dataType: 'JSON',
               data:{'file':file},
               headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
});