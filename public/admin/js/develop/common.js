/**
 * AdminLTE Demo Menu
 * ------------------
 * You should not use this file in production.
 * This file is for demo purposes only.
 */
var DataTable_language = { //国际化配置  
    "sProcessing" : "处理中...",    
    "sLengthMenu" : "显示 _MENU_ 项结果",    
    "sZeroRecords" : "没有匹配结果",    
    "sInfo" : "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",    
    "sInfoEmpty" : "显示第 0 至 0 项结果，共 0 项",    
    "sInfoFiltered" : "(由 _MAX_ 项结果过滤)",    
    "sInfoPostFix" : "",    
    "sSearch" : "搜索",    
    "sUrl" : "",    
    "oPaginate": {    
        "sFirst" : "第一页",    
        "sPrevious" : "上一页",    
        "sNext" : "下一页",    
        "sLast" : "最后一页"    
    }  
};
var apiurl = window.location.protocol + '//' + window.location.hostname + '/';/*'http://sp.zjhbjt.cn/';*//*'http://anjie.ifcar99.com/';'http://develop.anjietest-feature.ifcar99.com/';*//*'http://pre.anjie.ifcar99.com/';*/
var apiurlA = /*'http://apitest.ifcar99.com/api.php/';*/'https://www.ifcar99.com/api_v20/';
//重写jquery的ajax方法
(function($){
    $('.modal_close').click(function () {
//      window.location.href =window.location.href;
    });
    $('.signout').click(function(){
        USER.clear();
        $.ajax({
               type:'post',
               url: apiurl + 'auth/logout',
               dataType: 'json',
               data:{'account' : 'www'},
               // headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
               success:function(data){
                if (data.error_no !== 200) {
                    alert(data.error_msg);
                } else {
                    window.location.href = '/admin/login.html'; // 需要改url
                }
               }
         });
        
      });
    //备份jquery的ajax方法  
    var _ajax=$.ajax;  
      
    //重写jquery的ajax方法  
    $.ajax=function(opt){  
        //备份opt中error和success方法  
        var fn = {  
            error:function(XMLHttpRequest, textStatus, errorThrown){},  
            success:function(data, textStatus){}  
        }  
        if(opt.error){  
            fn.error=opt.error;  
        }  
        if(opt.success){  
            fn.success=opt.success;  
        }  
          
        //扩展增强处理  
        var _opt = $.extend(opt,{  
            error:function(XMLHttpRequest, textStatus, errorThrown){  
                //错误方法增强处理  
                  
                fn.error(XMLHttpRequest, textStatus, errorThrown);  
            },  
            success:function(data, textStatus){  
                //成功回调方法增强处理  
//              if (opt.dataType == 'json') {
//                  if (data.error_no == '401') {
//                      USER.clear();
//                      window.location.href = apiurl + 'dev_anjie/login.html'
//                  }
//              }
                 if (opt.dataType == 'json') {
                    if (data.error_no == '409') {
                        $('.modal_close').click(function () {
                            USER.clear();
                            window.location.href = '/admin/login'
                        });
                    }
                }
                fn.success(data, textStatus);  
            },
            cache:false,
        });  
        return _ajax(_opt);  
    };  
})(jQuery);  
 //验证是否是手机号码		
    function checkPhone(phone){		
        if(!(/^1[34578]\d{9}$/.test(phone))){ 		
            return false; 		
        } 		
        return true;		
    }		
    //跳转		
    function error_skip(data){		
        var modal = $('#myModal');		
        modal.find('.modal-body').text(data.error_msg);		
        modal.modal('show');		
        if (data.error_no == '409') {		
            $('.modal_close').click(function () {		
                USER.clear();		
                window.location.href = '/admin/login.html'		
            });		
        }		
    }		
    //验证是否为空的函数，传入字符串，以都好作为分隔		
    function checktelephone(classname){ 		
        var classarr = classname.split(",");		
        for(var i=0;i<classarr.length;i++){		
            var value = $.trim(classarr[i]);		
            if (!checkPhone($('.' + value).val())) {		
                 $('.' +  value).addClass("blur");		
                 $('.' +  value).focus();		
                return false;		
            } else {		
                $('.' +  value).removeClass("blur");  		
            }		
        }   		
    }		
    //验证是否为空的函数，传入字符串，以都好作为分隔		
    function checkcardid(classname){ 		
        var classarr = classname.split(",");		
        for(var i=0;i<classarr.length;i++){		
            var value = $.trim(classarr[i]);		
            if (!isCardID($('.' + value).val())) {		
                 $('.' +  value).addClass("blur");		
                 $('.' +  value).focus();		
                return false;		
            } else {		
                $('.' +  value).removeClass("blur");  		
            }		
        }   		
    }		
    //验证是否是身份证号码		
    function isCardID(sId){		
         var iSum=0 ;		
         var info="" ;		
         if(!/^\d{17}(\d|x)$/i.test(sId)) return false;		
         return true;		
    }		
    		
    //验证首位不为0的数字		
    function checknum(classname){		
        var classarr = classname.split(",");		
        for(var i=0;i<classarr.length;i++){		
            var value = $.trim(classarr[i]);		
            if (!/(^[1-9]([0-9]*)$|^[0-9]$)/.test($('.' + value).val())) {		
                 $('.' +  value).addClass("blur");		
                 $('.' +  value).focus();		
                return false;		
            } else {		
                $('.' +  value).removeClass("blur");  		
            }		
        }		
    }		
     //验证首位不为0的浮点型		
    function checkfloat(classname){		
        var classarr = classname.split(",");		
        for(var i=0;i<classarr.length;i++){		
            var value = $.trim(classarr[i]);		
            if (!/^[1-9][\d]*\.[\d]*$|^[0]\.[\d]*$|^[1-9][\d]*[\d]*$/.test($('.' + value).val())) {		
                 $('.' +  value).addClass("blur");		
                 $('.' +  value).focus();		
                return false;		
            } else {		
                $('.' +  value).removeClass("blur");  		
            }		
        }		
    }		
//保留两位小数 		
//功能：将浮点数四舍五入，取小数点后2位 		
function toDecimal(x) { 		
    var f = parseFloat(x); 		
    if (isNaN(f)) { 		
    return; 		
    } 		
    f = Math.round(x*100)/100; 		
    return f; 		
}
//获取url中的参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}

function listToTree(data, options) {
    options = options || {};
    var ID_KEY = options.idKey || 'id';
    var PARENT_KEY = options.parentKey || 'parent';
    var CHILDREN_KEY = options.childrenKey || 'children';

    var tree = [],
        childrenOf = {},
        ids = {};
    var item, id, parentId;

    for (var i = 0, length = data.length; i < length; i++) {
        item = data[i];
        id = item[ID_KEY];
        parentId = item[PARENT_KEY] || 0;
        // every item may have children
        childrenOf[id] = childrenOf[id] || [];
        // init its children
        item[CHILDREN_KEY] = childrenOf[id];
        if (parentId != 0) {
            // init its parent's children object
            childrenOf[parentId] = childrenOf[parentId] || [];
            // push it into its parent's children object
            childrenOf[parentId].push(item);
        } else {
            tree.push(item);
        }
        ids[id] = true;
    };
    //有节点但是 无parent_id = 0 的情况
    if (Object.keys(childrenOf).length > 0 && tree.length == 0) {
        for(var i in childrenOf) {
            if (!ids[i]) {
                for(var j in childrenOf[i]) {
                    tree.push(childrenOf[i][j]);
                }
            }
        }
    }
    return tree;
}
    //根据身份证验证性别
    function discriCard(UserCard){
        if (UserCard.length < 18) {
            return '身份证错误';
        }
        if (parseInt(UserCard.substr(16, 1)) % 2 == 1) { 
            return "男";
        } else { 
            return "女"; 
        } 
    }
    //验证是否为空的函数，传入字符串，以都好作为分隔
    function checkempty(classname){ 
        var classarr = classname.split(",");
        for(var i=0;i<classarr.length;i++){
            var value = $.trim(classarr[i]);
            if ($('.' + value).val() == '') {
                 $('.' +  value).addClass("blur");
                 $('.' +  value).focus();
                return false;
            } else {
                $('.' +  value).removeClass("blur");  
            }
        }   
    }
    //填充申请件来源类的方法
    function Fillmerchantclass(id)
    {
        var merchant_class_no = 'HB';
        $.ajax({
                url:apiurl + "system/system/getmerchantclass",
                data:{merchant_class_no:merchant_class_no},
                type:"POST",
                dataType: 'json',
                success: function(data){
                        var str = "<option value=''>请选择</option>";
                        for (i in data.result)
                        {
                            str += "<option value='"+data.result[i].id+"'>"+data.result[i].merchant_name+"</option>";
                        }
                        $("#" + id).html(str);
                    }
            });
    }
    //填充产品名称的方法
    function Fillproductname(id)
    {
        var product_class_no = 'XC';
        $.ajax({
                url: apiurl + "system/system/getproductname",
                data:{product_class_no:product_class_no},
                type:"POST",
                dataType: 'json',
                success: function(data){
                        var str = "<option value=''>请选择</option>";
                        for (i in data.result)
                        {
                            str += "<option value='"+data.result[i].id+"'>"+data.result[i].product_name+"</option>";
                        }
                        $("#" + id).html(str);
                    }
            });
    }
    //填充省的方法
    function FillSheng(id, oldprovincecode, oldprovince)
    {
        var pcode = "0001";       //父级代号
        $.ajax({
            url:apiurl + "system/system/getprovince",
            data:{pcode:pcode},
            type:"POST",
            dataType: 'json',
                success: function(data){              //回调函数
                    if (oldprovincecode != '' && oldprovince != '') {
                        var str = "<option selected='selected' value=''></option>";
                    } else {
                        var str = "";
                    }                    
                    for (i in data.result)
                    {
                        str += "<option value='"+data.result[i].code+"'>"+data.result[i].name+"</option>";
                    }
                        $("#"+id).html(str);        //把显示的地区名称填充进去
                    }
                });
    }
    //填充市的方法
    function FillShi(parentid, id, oldcitycode, oldcity)
    {
        if (oldcitycode == '0') {    //0表示add的时候
            var pcode = '110000';
        } else {
            var pcode = $("#" + parentid).val();
        }
        $.ajax({
            async:false,
            url:apiurl + "system/system/getcity",
            data:{pcode:pcode},
            type:"POST",
            dataType: 'json',
            success: function(data){
                if (oldcitycode != '' && oldcity != '') {
//                  var str = "<option selected='selected' value='"+oldcity+"'>"+oldcity+"</option>";
                } else {
                    var str = "";
                }  
                for (i in data.result)
                {
                    str += "<option value='"+data.result[i].code+"'>"+data.result[i].name+"</option>";
                }
                $("#"+id).html(str);
            }
        });
    }
    //填充区的方法
    function FillQu(parentid, id, oldtown)
    {
        if (oldtown == '0') {
            var pcode = '110100';
        } else {
            var pcode = $("#" + parentid).val();
        }
        $.ajax({
            url:apiurl + "system/system/gettown",
            data:{pcode:pcode},
            type:"POST",
            dataType: 'json',
            success: function(data){
                if (oldtown != '' && oldtown != '0' ) {
//                  var str = "<option selected='selected' value='oldtown'>"+oldtown+"</option>";
                } else {
                    var str = "";
                }
                for (i in data.result)
                {
                    str += "<option value='"+data.result[i].code+"'>"+data.result[i].name+"</option>";
                }
                $("#" + id).html(str);
            }
        });
    }
    
    //填充省的方法
    function SetSheng(callback)
    {
    	console.log('sheng')
        var pcode = "0001";       //父级代号
        $.ajax({
            url:apiurl + "system/system/getprovince",
            data:{pcode:pcode},
            type:"POST",
            dataType: 'json',
                success: function(data){              //回调函数
                    var str = "<option selected='selected' value='0'></option>";                  
                    for (i in data.result)
                    {
                        str += "<option value='"+data.result[i].name+"'>"+data.result[i].name+"</option>";
                    }
                        $("#sheng").html(str);        //把显示的地区名称填充进去
//                      $("#"+id).find("option[text='" + oldprovince + "']").attr("selected",true); // 默认选择
					callback();
                    }
                });
    }
    //填充市的方法
    function SetShi(pname,oldSheng,callback)
    {
        console.log('shi')
//      var pname = $("#sheng").val();
		if(oldSheng != 0){
			if(pname == oldSheng){
				return;
			}
		}
//		if(pname == 0){
//			$("#shi>option").eq(0).attr("selected",true);
//			$("#qu>option").eq(0).attr("selected",true);
//			return;
//		}
        $.ajax({
            async:false,
            url:apiurl + "Api/system/getcitybyname",
            data:{name:pname},
            type:"POST",
            dataType: 'json',
            success: function(data){
                var str = "<option selected='selected' value='0'></option>";
                for (i in data.result)
                {
                    str += "<option value='"+data.result[i].name+"'>"+data.result[i].name+"</option>";
                }
                $("#shi").html(str);
//              $("#shi").find("option[text='" + oldcity + "']").attr("selected",true); // 默认选择
            	callback();
            }
        });
    }
    //填充区的方法
    function SetQu(cname,oldShi,callback)
    {
        console.log('qu')
        if(oldShi != 0){
			if(cname == oldShi){
				return;
			}
		}
//		if(cname == 0){
//			$("#qu>option").eq(0).attr("selected",true);
//			return;
//		}
//      var pcode = $("#shi").val();
        $.ajax({
            url:apiurl + "Api/system/gettownbyname",
            data:{name:cname},
            type:"POST",
            dataType: 'json',
            success: function(data){
                var str = "<option selected='selected' value='0'></option>";
                for (i in data.result)
                {
                    str += "<option value='"+data.result[i].name+"'>"+data.result[i].name+"</option>";
                }
                $("#qu").html(str);
//              $("#"+id).find("option[text='" + oldtown + "']").attr("selected",true); // 默认选择
                callback();
            }
        });
    }

function loadSources(r) {
                var arr = {};
                arr['1'] = {
                    'source_type':'image',
                    'source_lists':[]
                };
                
                for(var i in r) {
                    var row = r[i];
                    if(row.file_class_id == 10){
                    	continue;
                    }
                    
                    arr['1'].source_lists.push({
                    	// org: row.file_path,
                    	org: row.file_path,//'/admin/images/test4.jpg',
                    	src: row.file_path,//'/admin/images/test4.jpg',
                    	alt: row.id,
                    	id: row.id,
                    });
                }
//                  console.log(arr)
                return arr;
           
}
var task_pickup = function (obj, id, item_instance_id, user_id) {
    var _obj = $(obj);
    _obj.prop('disabled', true);
    var data = {
        'token': token,
        'csr_id': id,
        'item_instance_id': item_instance_id,
    }
//  console.log(data)
    $.ajax({
        url:  apiurl + 'Jcr/jcd/pickup/',
        data: data,
        dataType: 'json',
        'success': function (r) {
            _obj.prop('disabled', false);
            if (r.error_no == '200') {
                _obj.text('已认领');
            } else {
                var modal = $('#myModal');
                modal.find('.modal-body').text(r.error_msg + r.error_no);
                modal.modal('show');
            }
        },
        'error': function (r) {
            _obj.prop('disabled', false);
        }
    });
}
var show_actions = function(lists) {
    var timeline = $('.timeline');
    timeline.empty();
    var c_date = function(str) {
        return '<li class="time-label"><span class="bg-red">' + str + '</span></li>'
    }
    var c_text = function(row){
//      if (row.status == '1') {
//          row.msg = '已认领';
//      }
//      if (row.status == '2' &&　row.task_status=='1') {
//          row.msg = '审核已通过';
//      }
//      if (row.status == '2' &&　row.task_status=='2') {
//          row.msg = '已拒件';
//      }
		if (row.msg=='') {
            if (row.status == '1') {
            row.msg = '已认领';
            }
            if (row.status == '4') {
                row.msg = '待认领';
            }
            if (row.status == '2' &&　row.task_status=='1') {
                row.msg = '审核已通过';
            }
            if (row.status == '2' &&　row.task_status=='2') {
                row.msg = '已拒件';
            }
        }
        var li = ''
        li += '<li>'
        li += '<i class="fa fa-user bg-blue"></i>'

        li += '<div class="timeline-item">'
        li += '<span class="time"><i class="fa fa-clock-o"></i> '+time_to_str(parseInt(row.create_time))+'</span>'

        li += '<h3 class="verifyinfoline-header"><a href="javascript:;">'+(row.name||'') +'</a> '+(row.task_title||'') +'</h3>'

        li += '<div class="timeline-body">'
//      li += ''+(row.msg||'') +''
//      li += '</div>'
		li += ''+(row.msg||'') +'';
        if (row.button_title !== '') {
            li += '<button type="button" class="btn  '+ row.button_class +'" style="float:right;">' + row.button_title +'</button>';
        }
        li += '</div>';
        li += '</div>'
        li += '</li>'
        return li;
    }
    var c_end = function () {
        return '<li><i class="fa fa-clock-o bg-gray"></i></li>';
    }
    var ds = {};
    for(var i in lists){
        var date = new Date(parseInt(lists[i]['create_time'])*1000);
        var ymd = date.getFullYear() + '/' + (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '/' + date.getDate();
        if (!ds[ymd]) {
            ds[ymd] = true;
            timeline.append(c_date(ymd));
        }
        timeline.append(c_text(lists[i]));
    }
    timeline.append(c_end());
}
var task_list = function(option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Api/workplatform/listtasks/',
        data: $.extend({},option.bill,{work_id:option.work_id},{token:option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}




var USER = {};
USER.login = function(option){
    var callback = option.callback || function () {};
    $.ajax({
        url: API_HOST + 'user/login',
        data: {loginname:option.loginname,password_md5:$.md5(option.password),from:'admin'},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}

USER.set_token = function(token) {
    if (navigator.userAgent.match(/(iPhone|iPod|Android|ios|Windows Phone)/i)) {
        window.localStorage.setItem('token', token.token);
        window.localStorage.setItem('token_over_time', token.over_time);
    }
    window.sessionStorage.setItem('token', token.token);
    window.sessionStorage.setItem('token_over_time', token.over_time);
    return true;
}

USER.get_token = function() {
    if (navigator.userAgent.match(/(iPhone|iPod|Android|ios|Windows Phone)/i)) {
        if (window.localStorage.getItem('token_over_time')) {
            return window.localStorage.getItem('token');
        } else {
            return false;
        }
    } else {
        if (window.sessionStorage.getItem('token_over_time')) {
            return window.sessionStorage.getItem('token');
        } else {
            return false;
        }
    }
}

USER.set_info = function(user) {
    if (navigator.userAgent.match(/(iPhone|iPod|Android|ios|Windows Phone)/i)) {
        window.localStorage.setItem('user_name', user.name);
        window.localStorage.setItem('user_account', user.account);
        window.localStorage.setItem('user_avatar', user.head_portrait);
    } else {
    	window.sessionStorage.setItem('user_name', user.name);
        window.sessionStorage.setItem('user_account', user.account);
        window.sessionStorage.setItem('user_avatar', user.head_portrait);
    }
    return true;
}

USER.get_info = function(key){
    if (navigator.userAgent.match(/(iPhone|iPod|Android|ios|Windows Phone)/i)) {
        return window.localStorage.getItem('user_'+key);
    } else {
        return window.sessionStorage.getItem('user_'+key);
    }
}

USER.clear = function() {
    window.sessionStorage.removeItem('token');
    window.sessionStorage.removeItem('token_over_time');
    window.sessionStorage.removeItem('user_name');
    window.sessionStorage.removeItem('user_account');
    window.sessionStorage.removeItem('role_sidebar');
    window.localStorage.removeItem('token');
    window.localStorage.removeItem('token_over_time');
    window.localStorage.removeItem('user_name');
    window.localStorage.removeItem('user_account');
    window.localStorage.removeItem('role_sidebar');
    return true;
}

USER.find = function(option) {
    var callback = option.callback || function () {};
    $.ajax({
        url:apiurl + 'Admin/role/finduser',
        data: {'token':option.token,loginnames:option.loginnames},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}

var ROLE = {};
ROLE.add = function (option) {
    var role = {
        
    }
    $.extend(role, option.role);
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/addrole',
        data: $.extend(role,{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.update = function (option) {
    var role = {
        
    }
    $.extend(role, option.role);
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/update',
        data: $.extend({role_id:option.role_id},role,{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.addrole = function (option) {
    var role = {
        
    }
    $.extend(role, option.role);
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/roleadd',
        data: $.extend(role,{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.updaterole = function (option) {
    var role = {
        
    }
    $.extend(role, option.role);
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/updaterole',
        data: $.extend({role_id:option.role_id},role,{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.get = function (option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/get',
        data: $.extend({'role_id':option.role_id},{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.lists = function (option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/lists',
        data: $.extend({'role_id':option.role_id},{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.delete = function (option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/deleterole',
        data: $.extend({'role_id':option.role_id},{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r,option);
        }
    });
}
ROLE.permission_tree = function (option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/permissiontree',
        data: $.extend({'role_id':option.role_id},{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r,option);
        }
    });
}
ROLE.tree_permission = function (option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/treepermission',
        data: $.extend({'role_id':option.role_id},{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r,option);
        }
    });
}
ROLE.load_module = function(option){
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/listsusermodule/',
        data: {token:option.token},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.set_module = function(data) {
    window.localStorage.setItem('role_module', JSON.stringify(data));
}

ROLE.get_module = function() {
    return JSON.parse(window.localStorage.getItem('role_module'));
}
ROLE.load_method = function(option){
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/listsusermethod/',
        data: {token:option.token},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.set_method = function(data) {
    window.localStorage.setItem('role_method', JSON.stringify(data));
}

ROLE.get_method = function() {
    return JSON.parse(window.localStorage.getItem('role_method'));
}

ROLE.set_sidebar = function(data) {
    window.localStorage.setItem('role_sidebar', JSON.stringify(data));
}

ROLE.get_sidebar = function() {
    return JSON.parse(window.localStorage.getItem('role_sidebar'));
}

ROLE.list_module = function(option){
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/listsmodule/',
        data: {token:option.token,role_id:option.role_id},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.list_method = function(option){
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/listsmethod/',
        data: {token:option.token,role_id:option.role_id},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.list_privilege = function(option){
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/listsprivilege',
        data: {token:option.token,role_id:option.role_id},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.sidebar = function(option){
	var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Api/privilege/sidebar',
        data: {token:option.token},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
ROLE.delete_user = function(option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/deleteuser/',
        data: {token:option.token,role_id:option.role_id,user_id:option.user_id},
        dataType: 'json',
        success: function (r) {
            callback(r,option);
        }
    });
}
ROLE.add_user = function(option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/adduserrole/',
        data: {token:option.token,role_ids:option.role_id,user_id:option.user_id},
        dataType: 'json',
        success: function (r) {
            callback(r,option);
        }
    });
}
ROLE.lists_user = function(option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: apiurl + 'Admin/role/listsuser/',
        data: {token:option.token,role_id:option.role_id,user_id:option.user_id},
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}

var SCRIPT = {};
SCRIPT.run = function(option) {
    var callback = option.callback || function () {};
    $.ajax({
        url: API_HOST + '/script/run',
        data: $.extend({'script_id':option.script_id},{'token':option.token}),
        dataType: 'json',
        success: function (r) {
            callback(r);
        }
    });
}
var FILE = {};
FILE.download = function(option) {
    window.open(API_HOST + '/file/download?token=' + option.token+'&file_id=' + option.file_id + '&download_name=' + (option.download_name||''));
}

//图片视频上传
var SOURCE = {
    '_data': {},//页面初始化或者图片上传后加到这个数组里来，删除时从这个数组里移除
    'galley': '',
    'viewer': '',
    'viewer_config': {},
    'option':{},
};
// DEFAULTS.minWidth = 550;
// DEFAULTS.minHeight = 416;
SOURCE.init = function (option) {
    var default_option = {
        onRemove:function(){},
        onChange:function(){},
        data:{},
    }
    this.option = $.extend({},default_option,option);
    this._data = this.option.data;
    //显示区域
    this.galley = document.getElementById('galley');
    //指定显示区域的高度
    //$(this.galley).parent().height(416); 
    //$(this.galley).parent().width()-134
    // $(this.galley).parent().width(550);

    //默认的类别
    if (this.current_Class == '' || !this.current_Class) {
        for (var i in this._data) {
            this.current_Class = i;
            this.current_ST = this._data[this.current_Class]['source_type'];
            break;
        }
    }
    var _this = this;
    this.viewer_config = {
        url: 'data-original',
        inline: true,
        remove: function (index) {//删除逻辑是点击x按钮后，从sources对象里移除对应index的图片，然后重新加载
            var remove_obj = _this._data[_this.current_Class]['source_lists'].splice(index, 1);
            _this.show(_this.current_Class);
            _this.option.onRemove(_this.current_Class, remove_obj[0]);
            _this.option.onChange(_this.current_Class);
        },
    }
    _this.option.onChange(_this.current_Class);
    $(window).resize(function(){
//      if ($(window).width() <= 975) {
//          $('#source_window').parent().css('position') == 'relative' ? '': $('#source_window').parent().css('position','relative');
//      	$('#source_window').parent().css('left','0px')
//      } else {
//          $('#source_window').parent().css('position') == 'fixed' ? '': $('#source_window').parent().css('position','fixed');
//          $('#source_window').parent().css('left','900px')
//      }
//      if(/detailmanage/g.test(location.href)){
//	        if ($(window).width() <= 975) {
//	            $('#source_window').css('position') == 'relative' ? '': $('#source_window').css('position','relative');
//	        } else {
//	            $('#source_window').css('position') == 'fixed' ? '': $('#source_window').css('position','fixed');
//	        }
//      }
//      if(/detailmanage/g.test(location.href)){
//	        $("#source_window").width($('body').width());
//	        $('#galley').find('video').height(416);// Math.min($('#galley').find('video').width(),$(window).height()-200)
//	        $("#mainVideoDiv").height($(this).parent().height() - 65);
//	        $("#mainVideo").height($("#mainVideoDiv").height())
//      }
		
		if ($(window).width() <= 975) {
            $('#source_window').css('position') == 'relative' ? '': $('#source_window').css('position','relative');
        } else {
            $('#source_window').css('position') == 'fixed' ? '': $('#source_window').css('position','fixed');
        }
        $("#source_window").width($("#source_window").parent().width());
        //$('#galley').find('video').height(416);// Math.min($('#galley').find('video').width(),$(window).height()-200)
        $("#mainVideoDiv").height($(this).parent().height() - 65);
        $("#mainVideo").height($("#mainVideoDiv").height())
    });
//  if(/detailmanage/g.test(location.href)){
//  	$("#source_window").width($("body").width());
//  }
    
    $(window).scroll(function(){
        if ($(window).scrollTop() >= 100) {
            if ($('#source_window').css('top') == 'auto') {
                $('#source_window').css('top','10px')
            }
        } else {
            if ($('#source_window').css('top') == '10px') {
                $('#source_window').css('top','auto')
            }
        }
    });
}
SOURCE.show = function (Class, index) {
    var _this = this;
    this.current_Class = Class|| this.current_Class;
    if (!this.current_Class || !this._data.hasOwnProperty(this.current_Class)) {
        if (_this.viewer != '') {
            $(_this.galley).children('ul').empty();
            _this.viewer.destroy();
        }
        return false;
    }
    this.current_ST = this._data[this.current_Class]['source_type'];
    this.prepare_data(this.current_Class);
    if (_this.viewer != '') {
        _this.viewer.destroy();
    }
    var config = {};
    if (!!index) {
        config = $.extend({}, {'index': index}, _this.viewer_config);
    } else {
        config = $.extend({}, _this.viewer_config);
    }
    _this.viewer = new Viewer(_this.galley, config);
    if(/detailmanage/g.test(location.href)){
    	$('.viewer-container').width(740);
    	$('.viewer-container').height(670);
    }
    
}
//Class需要大写
SOURCE.data_append = function (Class, type, data) {
    if (!this._data.hasOwnProperty(Class)) {
        this._data[Class] = {
            'source_type': type,
            'source_lists': []
        };
    }
    this._data[Class]['source_lists'].push(data);
    this.option.onChange(Class);
};
SOURCE.empty = function (Class) {
    var ul = $('#galley>ul');
    ul.empty();
    SOURCE._data = {};
    $('.input-group-addon.source_length').text('共0张');
};
// 切换列表
SOURCE.prepare_data = function (Class) {
    var ul = $('#galley>ul');
    ul.empty();
    if (this._data[Class]['source_type'] == 'image') {
        $('#source_length').html('共0张');
    }else if (this._data[Class]['source_type'] == 'video') {
        $('#source_length').html('共0张');
    }
    if (this._data.hasOwnProperty(Class)) {
        var sum1 = 0;
        var sum2 = 0;
//      if (this._data[Class]['source_type'] == 'image') {
			$('.pictures').css('display','none');
            sum1 = 0;
            for (var i in this._data[Class]['source_lists']) {
                ul.append('<li><img data-original="' + this._data[Class]['source_lists'][i].org + '" src="' + this._data[Class]['source_lists'][i].src + '" alt="' + this._data[Class]['source_lists'][i].alt + '"></li>');
                sum1++;
            }
            $('#source_length').html('共' + sum1 + '张');
//      }
    }
}
SOURCE.get_length = function(Class){
    return this._data.hasOwnProperty(Class) ? this._data[Class]['source_lists'].length : 0;
}

if (window.location.host == 'admintest.ifcar99.com') {
    var INIT = {
        'API_HOST': 'http://apitest.ifcar99.com',
        'SOURCE_HOST': 'http://apitest.ifcar99.com'
    };
} else if (window.location.host == '127.0.0.1:9000') {
    var INIT = {
        'API_HOST': 'http://api.car.com',
        'SOURCE_HOST': 'http://api.car.com'
    };
} else if (window.location.host == '127.0.0.1:8020') {
    var INIT = {
        'API_HOST': 'http://release-anjie.anjietest-feature.ifcar99.com',
        'SOURCE_HOST': 'http://release-anjie.anjietest-feature.ifcar99.com'
    };
} else if (window.location.host == 'admintest.api_lsk.com') {
    var INIT = {
        'API_HOST': 'http://api_lsk.com/api.php',
        'SOURCE_HOST': 'http://api_lsk.com/api.php'
    };
} else if (window.location.host == 'dev.anjie.com:8080') {
    var INIT = {
        'API_HOST': 'http://dev.anjie.com:8080',
        'SOURCE_HOST': 'http://dev.anjie.com:8080'
    };
} else if (window.location.host == 'http://release-anjie.anjietest-feature.ifcar99.com') {
    var INIT = {
        'API_HOST': 'http://release-anjie.anjietest-feature.ifcar99.com',
        'SOURCE_HOST': 'http://release-anjie.anjietest-feature.ifcar99.com'
    };
}else {
    var INIT = {
        'API_HOST': 'http://anjie.ifcar99.com',
        'SOURCE_HOST': 'http://anjie.ifcar99.com'
    };
}
var API_HOST = INIT.API_HOST;
INIT.go = function(){
    if (window.location.pathname != '/login.html') {
        USER.get_token() || (window.location.href = '/admin/login.html');
    } else {
        USER.get_token() && (window.location.href = '/admin/login.html');
    }
    // 连接服务端
   var socket = io('http://'+document.domain+':2120');
   // 连接后登录
   socket.on('connect', function(){
   	socket.emit('login', USER.get_token());
   });
   // 后端推送来消息时
   socket.on('new_msg', function(msg){
       var num = parseInt(msg);
       $('#count_new_order').text(parseInt($('#count_new_order').text()) + num);
   });
    //菜单显示
    var modules = ROLE.get_module();
    for(var m in modules) {
        $('.sidebar-menu .treeview[data-mid="'+modules[m]['module_id']+'"]').removeClass('hide');
    }
    //菜单选中
    $('.sidebar-menu .treeview').find('a').each(function(){
        if ($(this).attr('href').replace(/(^\s*)|(\s*$)/g, "") == window.location.pathname) {
            $(this).parents('li').addClass('active');
        }
    });
    //用户信息设置
    $('#user_nick').text(USER.get_info('nick'));
}
function time_mdy_exchange(time){
	var Y = String(time.substring(0,4));
	var M = String(time.substring(4,6));
	var D = String(time.substring(6,8));
	return (M + '/' + D + '/' + Y);
}
function time_to_str(time) {
    var date = new Date(time*1000);
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    var D = date.getDate() + ' ';
    var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    var m = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
    var s = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
    return Y + M + D + h + m + s; 
}
function time_to_str_ymd(time) {
    var date = new Date(time*1000);
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    var D = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    return Y + M + D; 
}
function time_to_str_ymd_num(time) {
    var date = new Date(time*1000);
    var Y = date.getFullYear();
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
    var D = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    return Y + '' + M + '' + D; 
}
function parseFileUrl (url){
    var index1 = url.lastIndexOf('.');
    var index2 = url.lastIndexOf('/');
    var suffix = url.substring(index1+1,url.length);
    var fid = url.substring(index2+1,index1);
    return {"fid":fid,"url":url,"suffix":suffix};
}
// $(function(){
//     INIT.go();
// })

// 取地址栏参数
function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}

// 除login以外的页面自动获取cookie个人信息
$(function(){
	var reg = /login/;
	if(reg.test(location.href)){
		return;
	}
	$(".usernameWrite").html(USER.get_info('name'));
	$(".accountWrite").html(USER.get_info('account'));
	var sidebarRes = JSON.parse(ROLE.get_sidebar());
	$(".treeview-menu>li").css('display','none');
	for(var j in sidebarRes.method){
		$(".treeview-menu>li[methodid=" + sidebarRes.method[j].method_id + "]").css('display','block')
	}
	$(".treeview").css('display','none');
	for(var k in sidebarRes.category){
		$(".treeview[cid=" + sidebarRes.category[k].id + "]").css('display','block')
	}
	var avatarSrc = USER.get_info('avatar');
	if(avatarSrc != null && avatarSrc.length>0){
		$(".avatar").attr('src',avatarSrc);
	}
})

// 打开弹窗后调整viewer窗口大小
$(document).ready(function(){
	$('#source_window').on('shown.bs.modal', function (e) {  
  		SOURCE.show();
	})
})

// 浮点计算方法
var floatFun = {
	/**
	* 加法运算，避免数据相加小数点后产生多位数和计算精度损失。
	*
	* @param num1加数1 | num2加数2
	*/
	'add': function(num1, num2) {
	    var baseNum, baseNum1, baseNum2;
	    try {
	        baseNum1 = num1.toString().split(".")[1].length;
	    } catch (e) {
	        baseNum1 = 0;
	    }
 	    try {
	        baseNum2 = num2.toString().split(".")[1].length;
	    } catch (e) {
	        baseNum2 = 0;
	    }
	    baseNum = Math.pow(10, Math.max(baseNum1, baseNum2));
	    return parseInt((num1 * baseNum + num2 * baseNum) / baseNum * 10000) / 10000;
	},
	/**
	* 减法运算，避免数据相减小数点后产生多位数和计算精度损失。
	*
	* @param num1被减数  |  num2减数
	*/
	'sub': function(num1, num2) {
    	var baseNum, baseNum1, baseNum2;
    	var precision;// 精度
    	try {
    	    baseNum1 = num1.toString().split(".")[1].length;
    	} catch (e) {
     	    baseNum1 = 0;
    	}
    	try {
        	baseNum2 = num2.toString().split(".")[1].length;
    	} catch (e) {
        	baseNum2 = 0;
    	}
    	baseNum = Math.pow(10, Math.max(baseNum1, baseNum2));
    	precision = (baseNum1 >= baseNum2) ? baseNum1 : baseNum2;
    	return parseInt(((num1 * baseNum - num2 * baseNum) / baseNum).toFixed(precision) * 10000) / 10000;
	},
	/**
	* 乘法运算，避免数据相乘小数点后产生多位数和计算精度损失。
	*
	* @param num1被乘数 | num2乘数
	*/
	'multi': function(num1, num2) {
    	var baseNum = 0;
    	try {
        	baseNum += num1.toString().split(".")[1].length;
    	} catch (e) {
    	}
    	try {
        	baseNum += num2.toString().split(".")[1].length;
    	} catch (e) {
    	}
    	return parseInt(Number(num1.toString().replace(".", "")) * Number(num2.toString().replace(".", "")) / Math.pow(10, baseNum) * 10000) / 10000;
	},
	/**
	* 除法运算，避免数据相除小数点后产生多位数和计算精度损失。
	*
	* @param num1被除数 | num2除数
	*/
	'div': function(num1, num2) {
    	var baseNum1 = 0, baseNum2 = 0;
    	var baseNum3, baseNum4;
    	try {
        	baseNum1 = num1.toString().split(".")[1].length;
    	} catch (e) {
        	baseNum1 = 0;
    	}
    	try {
        	baseNum2 = num2.toString().split(".")[1].length;
    	} catch (e) {
        	baseNum2 = 0;
    	}
    	with (Math) {
        	baseNum3 = Number(num1.toString().replace(".", ""));
        	baseNum4 = Number(num2.toString().replace(".", ""));
        	return parseInt((baseNum3 / baseNum4) * pow(10, baseNum2 - baseNum1) * 10000) / 10000;
    	}
	},
}