<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<title>林润审批后台管理中心</title>
		<link rel="shortcut icon" type=images/favicon.ico href="/images/favicon.ico" media=screen>
		<link rel="stylesheet" href="{{ asset('/css/login.css')}}">
		<script>
			var _hmt = _hmt || [];
			(function() {
			  var hm = document.createElement("script");
			  hm.src = "https://hm.baidu.com/hm.js?6b2f1babf4842a78f0afb7ecf07b1070";
			  var s = document.getElementsByTagName("script")[0]; 
			  s.parentNode.insertBefore(hm, s);
			})();
		</script>
	</head>

	<body>
		<div class="login-title">
			<img src="images/login-logo.png" />
		</div>
		<div class="login-bg">
			<div class="login-bar">
				<div class="login-bar-title">登录</div>
				<input type="text" name="loginUN" id="username" maxlength="11" pattern="[0-9]*" value="" placeholder="请输入手机号" />
				<input type="password" name="loginPW" id="password" value="" placeholder="请输入密码" />
				<div class="login-bar-opt">
					<span class="remember"><input type="checkbox" name="" id="remember" /><span>记住密码</span></span>
					<span class="forget"><a href="###">忘记登录密码？</a></span>
				</div>
				<div id="loginBtn">登录</div>
			</div>
		</div>
		<div class="login-footer">
			<p class="footer-tab">
				<span><a href="http://www.zjhbjt.cn/introduction/21.html">关于我们</a></span><!--<span><a href="###">诚聘英才</a></span><span><a href="###">安全保障</a></span>--><span><a href="http://www.zjhbjt.cn/contactus.html">联系我们</a></span>
			</p>
			<p class="copyright">Copyright 2014 浙江杭标集团 All Rights Reserved
Zhejiang Hangzhou</p>
		</div>
	</body>
	<script type="text/javascript" src="{{asset('/js/develop/jquery-3.1.1.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('/js/develop/common2.js')}}"></script>
	<script src="/bower_components/AdminLTE/plugins/md5/jquery.md5.js"></script>
	<script>
		FuckInternetExplorer()
		function FuckInternetExplorer() {
			var userAgent = navigator.userAgent;
			var isOpera = userAgent.indexOf("Opera") > -1;
			if(((userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1 && !isOpera)) || (userAgent.toLowerCase().indexOf("trident") > -1 && userAgent.indexOf("rv") > -1)){
				var Dbody = document.getElementsByTagName("body")[0];
	            Dbody.innerHTML = "<div style='margin: 50px auto;width: 450px;height: 310px;text-align: center;border: 3px solid gray;'><h1 style='border-bottom: 1px solid gray;margin-top: 80px;display: inline-block;line-height: 2;'><span style='color: gray;'>您的浏览器</span><span style='color: red;'>不受支持</span></h1><p style='width: 65%;margin: 0 auto;font-size: 14px;text-align: left;'>为了使您能正常浏览页面，我们建议您使用最新版的Chrome</p><ul><li style='list-style: none;width: 65%;margin: 0 auto;text-align: left;font-size: 14px;'>点击下载：<a href='http://test-jcjr.test.ifcar99.com:18082/Chrome.exe'>谷歌Chrome</a></li></ul></div>";
	            return false;
			} else {
				return true;
			}
//		    var browser = navigator.appName;
//		    var b_version = navigator.appVersion;
//		    var version = b_version.split(";");
//			if (version.length > 1) {
//				console.log(version)
//		        var trim_Version = parseInt(version[1].replace(/[ ]/g, "").replace(/MSIE/g, ""));
//		        console.log(trim_Version)
//		        if (trim_Version < 9) {
//		        	var Dbody = document.getElementsByTagName("body")[0];
//		            Dbody.innerHTML = "<div style='margin: 50px auto;width: 450px;height: 310px;text-align: center;border: 3px solid gray;'><h1 style='border-bottom: 1px solid gray;margin-top: 80px;display: inline-block;line-height: 2;'><span style='color: gray;'>您的浏览器</span><span style='color: red;'>不受支持</span></h1><p style='width: 65%;margin: 0 auto;font-size: 14px;text-align: left;'>为了使您能正常浏览页面，我们建议您使用最新版的Chrome</p><ul><li style='list-style: none;width: 65%;margin: 0 auto;text-align: left;font-size: 14px;'><a href='http://test-jcjr.test.ifcar99.com:18082/Chrome.exe'>谷歌Chrome</a></li></ul></div>";
//		            return false;
//		        }
//			}
			
		}
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			if(getCookie('loginUN') != null && getCookie('loginPW') != null) {
				$('#username').val(getCookie('loginUN'));
				$('#password').val(getCookie('loginPW'));
			}

			function getCookie(name) {
				var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
				if(arr = document.cookie.match(reg))
					return unescape(arr[2]);
				else
					return null;
			}
			function setCookie(name, value, days) { //设置cookie
    			var d = new Date();
    			d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    			var expires = "expires=" + d.toUTCString();
    			document.cookie = name + "=" + value + "; " + expires;
    		}
			$('.forget').click(function() {
				alert('请联系管理员！');
			});
			$(document).keyup(function(e) {
				if(e.which == 13) {
					$('.submitlogin').click();
				}
			});
			document.addEventListener('keydown',function(e){
				var ev = e || window.event;
				var keyCode = ev.keyCode || ev.which;
				if(keyCode == 13){
					$('#loginBtn').click()
				}
			})
			$('#loginBtn').click(function() {
				var account = $('#username').val();
				var password = $('#password').val();
				var loginData = {
					'account': account,
					'password': $.md5(password)
				};
				$.ajax({
					type: 'post',
					url: 'auth/login',
					dataType: 'json',
					data: loginData,
					success: function(data) {
						if(data.error_no !== 200) {
							alert(data.error_msg);
						} else {
							if($("#remember").prop('checked')) {
								setCookie('loginUN',account,7);
								setCookie('loginPW',password,7);
							}
							USER.set_token(data.result.token);
							USER.set_info(data.result.user);
							ROLE.load_module({
								token: USER.get_token(),
								callback: function(r) {
									if(r.error_no == 200) {
										ROLE.set_module(r.result.rows);
										ROLE.load_method({
											token: USER.get_token(),
											callback: function(r) {
												if(r.error_no == 200) {
													ROLE.set_method(r.result.rows);
													window.location.href = '/Admin/index';
												} else {
													var modal = $('#myModal');
													modal.find('.modal-body').text(r.error_msg);
													modal.modal('show');
												}
											}
										});
									} else {
										var modal = $('#myModal');
										modal.find('.modal-body').text(r.error_msg);
										modal.modal('show');
									}
								}
							});

						}
					}
				})
			});
		})
	</script>

</html>