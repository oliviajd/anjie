$(function () {
    $('.make').click(function(){
         $.ajax({
               type:'post',
               url:'/Admin/file/make',
               dataType: 'json',
               data:{'filename' : 'test.jpg', 'type':'image'},
               // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
    $('.getuploadfile').click(function(){
      var token = "36f57e79569e395380c32905c350b6d30.01640100 149075624910818";
         $.ajax({
               type:'post',
               url:'getuploadfile',
               dataType: 'json',
               data:{'filename' : 'test.jpg', 'file_token':token},
               // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
    $('.upyunpost').click(function(){
         $.ajax({
               type:'post',
               url:'upyunpost',
               dataType: 'json',
               data:{'filename' : 'test.jpg'},
               // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
    $('.bankpost').click(function(){
         $.ajax({
               type:'post',
               url:'bankpost',
               dataType: 'json',
               data:{'filename' : 'test.jpg'},
               // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
    $('.bankimageconfirm').click(function(){
         $.ajax({
               type:'post',
               url:'bankimageconfirm',
               dataType: 'json',
               data:{'filename' : 'test.jpg'},
               // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                // console.log(data);
               }
         });
    });
    $('.queryday').click(function(){
         $.ajax({
               type:'post',
               url:'http://120.192.84.62:9080/icbc/api/batchQuery.action',
               dataType: 'json',
               data:{
                'transCode' : 'DIVIQUERYD',
                'inputJson' : '{"BCIS":{"eb":{"pub":{"TransCode":"DIVIQUERYD","CIS":"002","ID":"B2C2017.e.1602","TranDate":"20170413","TranTime":"19374844","QueryDate":"20170413"}}}}',
                'salt' : 'OPyZpzHi',
                'currentTime' :'1491997068044',
                'orgSign' : '72740a061a4838a1830f1ea2a7888ce2'
               },
               success:function(data){
                console.log(data);
               }
         });
    });
    $('.query').click(function(){
         $.ajax({
               type:'post',
               url:'http://120.192.84.62:9080/icbc/api/approveQuery.action',
               dataType: 'json',
               data:{
                'transCode' : 'DIVIQUERY',
                'inputJson' : '{"BCIS":{"eb":{"pub":{"TransCode":"DIVIQUERY","CIS":"002","ID":"B2C2017.e.1602","TranDate":"20170413","TranTime":"165304193","fSeqno":"20170413165304193"},"in":{"TradeCode":"1","CardNum":"","TradeNo":"002165304193"}}}}',
                'salt' : 'BbLITtNu',
                'currentTime' :'1491987184193',
                'orgSign' : '31b58d630ce8877c2fb583f2211eedac'
               },
               success:function(data){
                console.log(data);
               }
         });
    });
    $('.divapply').click(function(){
         $.ajax({
               type:'post',
               url:'http://120.192.84.62:9080/icbc/api/approveSubmit.action',
               dataType: 'json',
               data:{
                'transCode' : 'DIVIAPPLY',
                'inputJson' : 'CoName',
                'salt' : 'bTjZRkmI',
                'currentTime' :'1491987864281',
                'orgSign' : '572f60b7d2e66be5f9225b914f2e58ca'
               },
               success:function(data){
                console.log(data);
               }
         });
    });
    $('.imageconfirm').click(function(){
         $.ajax({
               type:'post',
               url:'http://120.192.84.62:9080/icbc/api/imgConfirm.action',
               dataType: 'json',
               data:{
                'transCode' : 'IMAGELIST',
                'inputJson' : '{"BCIS":{"eb":{"pub":{"TransCode":"IMAGELIST","CIS":"002","ID":"B2C2017.e.1602","TranDate":"20170413","TranTime":"190121504","fSeqno":"20170413190121504"},"in":{"TotalNum":1,"Backup1":"","rd":[{"Seqno":1267628748,"ImageID":1118972529,"ImageType":"1","ImageFile":"1.jpg","ImagePath":"/upload/","TradeNo":"002000000013","Serialno":"1","Catagory":"14","Note":"","Backup2":"","Backup3":""}]}}}}',
                'salt' : 'UOorWTsO',
                'currentTime' :'1491994881504',
                'orgSign' : 'fb3dd3a11ea0a69adcace817843ff5cc'
               },
               success:function(data){
                console.log(data);
               }
         });
    });
    $('.requestcommit').click(function(){
         $.ajax({
               type:'post',
               url:'http://120.192.84.62:9080/icbc/api/approveSubmit.action',
               dataType: 'json',
               data:{
                'transCode' : 'DIVIAPPLY',
                'inputJson' : '{"BCIS":{"eb":{"pub":{"TransCode":"DIVIAPPLY","CIS":"002","ID":"B2C2017.e.1602","TranDate":"20170413","TranTime":"200247407","fSeqno":"20170413200247407"},"in":{"TradeCode":"1","TradeNo":"002000000012","ApplyDate":"20170413","aName":"0","aAge":"12","aSex":"2","aHuji":"a","aCertType":"000","aCertNum":"370105198606060010","aAddress":"b","aCorp":"c","aPhone":"15588888888","aIncome":"150000","mName":"a","mCertType":"000","mCertNum":"370105198606060022","mCorp":"d","mPhone":"15566666666","mIncome":"100000","rName":"a","rCertType":"000","rCertNum":"370105198606060034","rAddress":"g","rCorp":"h","rPhone":"13677777777","rIncome":"18000","sName":"a","sCertType":"000","sCertNum":"370105198606060047","sCorp":"j","sPhone":"13699999999","sIncome":"160000","CarBrand":"K","CarID":"0","CarNoType":"","CarNo":"0","Insurance":"","CarPrice":"30000","FirstPay":"15000","CardNum":"6222888888888888","DiviAmt":"15000","Term":"36","FeeRate":"12","FeeAmt":"18000","IsAmort":"1","AmortDetail":"M","AmortNum":"12345678","IsAssure":"1","AssureCorp":"N","AssureAccount":"787878787878787","CoName":"0","FeeMode":"0","TellerNum":"000833658","Note":"0","PicNum":"1","VideoNum":"","Backup1":"","Backup2":"","Backup3":"","Backup4":""}}}}',
                'salt' : 'cSsIaJdq',
                'currentTime' :'1492000667407',
                'orgSign' : '93e453c9cfe56c86c12979773c4eb7a1'
               },
               success:function(data){
                console.log(data);
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