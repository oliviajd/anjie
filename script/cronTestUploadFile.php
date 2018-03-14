<?php 
//获取token和fileID
// $test = new test();
// $make = $test->getToken();
$make = array (
  // "fileid" => 'e796f42a41098565614256452fd3a75c',
  "file_token" => "364be8860e8d72b4358b5e88099a935a0.90400200 149032557390903",
);
// $headers = array(
// 	'Coentent-Type:application/x-www-form-urlencoded',
// );

$little = 1024 * 1024;  //传送单位1M
$file = 'C:\uploadsfile\test.png';
$test = new test();

$size = filesize($file);
$num = ceil($size/$little);
$start = 0;
$isend = 0;
$finaldata = '';

for ($i=0; $i <$num ; $i++) { 
	if ($i == $num-1) {
		$little = $size - ($num-1) * $little;
		$isend = 1;
	}
	$getdata = file_get_contents($file,  NULL, NULL, intval($start), $little);

	$url = 'http://dev.anjie.com:8080/index.php/Admin/prize/getuploadfile';
	// $url = 'http://anjie.ifcar99.com/index.php/Admin/prize/getuploadfile';
	$data = array(
    	'data' => $getdata,
    	// 'fid' => $make['fileid'],
    	'file_token' => $make['file_token'],
    	'offset' => $start,
    	'isend' => $isend,
    );
    $test = new test();
    $return = $test->curltest($url, $data);
    //这里前端需要加上判断,如果返回的errorcode不等于0,则终止此文件的传输
    if ($return['rinfo']['http_code'] !== 200) {
    	sleep(3);
    	$return = $test->curltest($url, $data);
    }
	$start = $start + $little;
}
//test
/**
* 
*/
class test
{
	//模拟获取token
	public function getToken()
	{
		$filename = 'banner.jpg';
		$url = 'http://dev.anjie.com:8080/index.php/Admin/prize/make';
		$data = array(
		    'filename' => $filename, 
		);
		$make = $this->curltest($url, $data);
		return $make;
	}
	//curl模拟http请求
	public function curltest($url, $data)
	{
		$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_HEADER, 0);  
		curl_setopt($ch,CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_TIMEOUT,60);
		$return['data'] = curl_exec($ch);
		$return['rinfo']=curl_getinfo($ch); 
		curl_close($ch);
		return $return;
	}
}