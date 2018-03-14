		</section>
	</div>
<footer class="main-footer">
	<div class="pull-right hidden-xs">
		Anything you want
	</div>
	<strong>Copyright &copy; 2017 <a href="#">ifcar</a>.</strong> All rights reserved.
</footer>
</div>
</body>
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
		
	}
</script>
</html>