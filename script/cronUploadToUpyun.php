<?php 
//$url = 'http://dev.anjie.com:8080/index.php/Admin/prize/upyunposttest';
$url = 'http://anjie.ifcar99.com/index.php/Admin/prize/upyunposttest';
$upyun = new upyun();
$return = $upyun->curltest($url);
if ($return['rinfo']['http_code'] !== 200) {
    $return = $upyun->curltest($url);
}

/**
* upyun类，暂时只有一个模拟curl请求的方法
*/
class upyun
{
    //curl模拟http请求
    public function curltest($url, $data=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        $return['data'] = curl_exec($ch);
        $return['rinfo']=curl_getinfo($ch); 
        curl_close($ch);
        return $return;
    }
}