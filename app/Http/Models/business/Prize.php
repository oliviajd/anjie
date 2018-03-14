<?php

namespace App\Http\Models\business;
require_once($_SERVER['DOCUMENT_ROOT']."/php-sdk/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php-sdk/src/Upyun/Config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php-sdk/src/Upyun/Upyun.php");

use Illuminate\Database\Eloquent\Model;
use Upyun\Upyun;
use Upyun\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Prize extends Model
{
  //暂时只支持image和vedio的上传
  static public $_typeallow = array(
    'image' => 1,
    'vedio' => 1,
  );
  //允许传入的图片格式 对应为0的是暂不支持的图片格式
  static public $_imagesallow = array(
    'jpg' => 1, 
    'bmp' => 1, 
    'png' => 1, 
    'tiff'=> 1, 
    'gif' => 1, 
    'pcx' => 1,
    'tga' => 1, 
    'exif'=> 1, 
    'psd' => 1, 
    'pcd' => 1, 
    'raw' => 1, 
    'wmf' => 1,
    'fpx' => 0,
    'svg' => 0,
    'cdr' => 0,
    'dxf' => 0,
    'ufo' => 0,
    'eps' => 0,
    'ai'  => 0,
  );
    //protected $connection = 'connection-name';
    protected  $table='v1_prize';
    public $primaryKey='pid';
    public $timestamps=false;

   /*  @params: $url            被curl请求的URL
                $start          开始字节
                $end            结束字节
       @return  $output         请求结果
   */
    public function urlRequest($url, $start, $end)
    {
    	$urlmd5 = md5($url);
    	$header = array(
	        'Range:bytes='.$start.'-'.$end,
	    );
	    $result['filename'] = $urlmd5.$start.".jpg";
	    $result['output'] = $this->curl_request($url, $header);
      return $result;
    }

   /*  @params: $url            被curl请求的URL
                $header         header数组
       @return  $output         请求结果
   */
    public function curl_request($url, $header=array())
    {
    	$curl = curl_init($url);      
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);  
        $output = curl_exec($curl);   
        curl_close($curl);
        return $output;
    }

   /*  @params: $url            需要取到header得URL
       @return: $headerarr      经过分割后的header数组
   */
    public function getHeaders($url)
    {
    	$headers = get_headers($url);
    	$headerarr = array();
	    foreach ($headers as $key => $value) {
	    	$arr = explode(':', $value);
	    	$headerarr[$arr['0']] = isset($arr['1']) ? $arr['1'] : '';
	    }
	    return $headerarr;
    }

    public function curltest($url, $data)
  	{
  		$ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_HEADER, 0);
  		curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
  		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  		curl_setopt($ch, CURLOPT_TIMEOUT,30);
  		$return = curl_exec($ch);
  		curl_close($ch);
  		return $return;
  	}

    public function handleUploadQueue($uploadsQueue)
    {
       // 创建实例
        $bucketConfig = new Config(env('UPYUN_CLOUD_NAME'), env('UPYUN_OPERATOR_NAME'), env('UPYUN_OPERATOR_PASSWD'));  
                                       //空间名、操作员名、操作员密码
        $client = new Upyun($bucketConfig);
        foreach ($uploadsQueue as $key => $value) {
            // 读文件
          try{
            $filename = $value->path . $value->file_id . '.' . $value->suffix;
            if (!is_file($filename)) {
              $update = DB::statement("update file_upload set sync_to_upyun_status = ? where id = ?", ['-1', $value->id]);
              continue;
            }
            $file = fopen($filename, 'r');
            // 上传文件
            $res = $client->pointWrite($value, $file);
            $update = DB::statement("update file_upload set sync_to_upyun_status = ? where id = ?", ['1', $value->id]);
            echo filename . 'success';
          } catch(\Exception $e) { 
              if ($e->getCode()) {
                echo $e->getMessage();
                Log::uploads('uploadService:fileid:' . $value->fiel_id. $e->getMessage());
                $update = DB::statement("update file_upload set sync_to_upyun_status = ? where id = ?", ['-2', $value->id]);
                continue;
              }
          }
          
        }
    }

    public function getBankUploadsQueue()
    {
      $sql = "select * from file_upload where sync_to_bank = '1' and is_end = '1' and sync_to_bank_status = '0' order by id";
      $stmt = $this->_pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    }

    public function setSynctoBankStatus($isend, $fileId)
    {
      $sql = "update file_upload set sync_to_bank_status = ? where file_id = ?";
      $stmt = $this->_pdo->prepare($sql);
      $rs = $stmt->execute(array($isend, $fileId));
      return $rs;
    }

    public function uploadsToBankQueue($uploadsQueue)
    {
      $publicpath = str_replace('\\','/',public_path());
      foreach ($uploadsQueue as $key => $value) {
        $allowimage = prize::$_imagesallow;
        if ($value['type'] == 'image' && isset($allowimage[$value['suffix']]) && $allowimage[$value['suffix']] == 1) {
          $suffix = 'jpg';
        } elseif($data['type'] == 'image' && (!isset($allowimage[$value['suffix']]) || $allowimage[$value['suffix']] == 0)) {
          $updatefailstatus = $this->setSynctoBankStatus(2, $value['file_id']); //不支持的图片格式，直接上传失败
          continue;
        } else {
          $suffix = $value['suffix'];
        }
        $filename = $publicpath . '/' . $value['path'] . $value['file_id'] . '.' . $value['suffix'];
        $little = env('UPLOAD_BANK_LIMIT_SIZE');  //传送单位1M
        $size = filesize($filename);
        $num = ceil($size/$little);
        $start = 0;
        $isend = 0;
        for ($i=0; $i <$num; $i++) { 
            if ($i == $num-1) {
                $little = $size - ($num-1) * $little;
                $isend = 1;
            }
            $getdata = file_get_contents($filename,  NULL, NULL, intval($start), intval($little));
            $offset = $start;
            //connect to server
            $resConnection = ssh2_connect(env('SFTP_SERVER'), env('SFTP_SERVER_PORT'));
            if(ssh2_auth_password($resConnection, env('SFTP_SERVER_USERNAME'), env('SFTP_SERVER_PASSWORD'))){
                //Initialize SFTP subsystem
                $resSFTP = ssh2_sftp($resConnection);
                //上传目标服务器的地址暂时是写死了的，也可以直接放在.env文件中，可以考虑专门为这个项目写一个application.ini文件
                $path = "/home/wwwroot/dev_anjie/public/sftpuploads/"; 
                if (!file_exists("ssh2.sftp://{$resSFTP}/" . $path . $value['path'])) {
                  mkdir("ssh2.sftp://{$resSFTP}/" . $path. $value['path'], 0777, true);
                }  
                $file  = $value['path'] . $value['file_id'] . '.' . $suffix;
                if (!is_file("ssh2.sftp://{$resSFTP}/" . $path . $file)) {
                    $re = file_put_contents("ssh2.sftp://{$resSFTP}/" . $path . $file, '');
                }
                $resFile = fopen("ssh2.sftp://{$resSFTP}/".$path.$file, 'rb+');
                fseek($resFile, $offset);
                fwrite($resFile, $getdata);
                fclose($resFile);
                $start = $start + $little;
                if ($isend == 1) {
                  $updatestatus = $this->setSynctoBankStatus($isend, $value['file_id']);
                }
            } else {
              throw new \Exception('Unable to authenticate on server', -1);
            }
        }
      }
    }

    public function create_file_id($id)
    {
      $sn_left = microtime(1) . $_SERVER['SERVER_ADDR'];
        if ($id > 1000) {
            //方法1,每秒不超过100w的并发数,
            $sn_right = str_pad($id % 1000000, 6, '0', STR_PAD_LEFT);
        } else {
            //方法2,每毫秒不超过1000个并发
            $str = str_pad(microtime(1) * 1000 % 1000, 3, '0', STR_PAD_LEFT);
            $sn_right = $str . str_pad($id % 1000, 3, '0', STR_PAD_LEFT);
        }
        return md5($sn_left . $sn_right);
    }

    public function add($data)
    {
        $param['user_id'] = intval($data['user_id']);
        $param['type'] = trim($data['type']);
        $param['path'] = trim($data['path']);
        $param['suffix'] = trim($data['suffix']);
        $param['create_time'] = time();
        $param['sync_to_upyun'] = in_array(intval($data['sync_to_upyun']), array(1, 2)) ? intval($data['sync_to_upyun']) : 2;
        $param['file_token'] = trim($data['file_token']);
        $param['token_expired_in'] = $data['token_expired_in'];

        $sql = "insert into file_upload (user_id, type, path, suffix, create_time, sync_to_upyun, file_token, token_expired_in) values(?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_pdo->prepare($sql);
        $insert = $stmt->execute(array($param['user_id'], $param['type'], $param['path'], $param['suffix'], $param['create_time'], $param['sync_to_upyun'], $param['file_token'], $param['token_expired_in']));
        $param['id'] = $this->_pdo->lastInsertId();
        $param['fid'] = $this->create_file_id($param['id']);
        $param['size'] = 0;
        $host = 'http://' . str_replace('\\','/',$_SERVER["HTTP_HOST"]);
        $param['url'] = $host . '/' . $param['path'] . $param['fid'] . '.' . $param['suffix'];

        $sql = "update file_upload set file_id = ? where id = ?";
        $stmt = $this->_pdo->prepare($sql);
        $update = $stmt->execute(array($param['fid'], $param['id']));
        if ($update) {
          file_put_contents($param['path'] . $param['fid'] . '.' . $param['suffix'], '');
          $return['errorcode']= 0;
          $return['errormsg'] = '';
          $return['result'] = new obj_file($param);
        }
        return $return;
    }

    /**
   * 根据原文生成签名内容
   *
   * @param string $data 原文内容
   *
   * @return string
   * @author confu
   */
  public function sign($data, $key)
  {
    $pass = '12345678';
    if (openssl_pkcs12_read($key, $certs, $pass)) {  
      $privateKey = $certs['pkey'];
      $publicKey = $certs['cert'];
      if (openssl_sign($data, $binarySignature, $privteKey, OPENSSL_ALGO_SHA1)) {
        return $binarySignature;
      } else {
        return '';
      }
    } else {
      return '';
    }
  }

  public function genReqData($reqData, $signature)
  {
    $length = strval(strlen($reqData));
    if (strlen($length) <= 10) {
        $length = substr('0000000000', 10 - $length) . strval($length);
    }
    return $length . $reqData . "ICBCCMP" . base64_encode($signature);
  }
  
  //curl模拟http请求
    public function curltestfor($url, $data=null)
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

  /**
   * 验证签名自己生成的是否正确
   *
   * @param string $data 签名的原文
   * @param string $signature 签名
   *
   * @return bool
   * @author confu
   */
  public function verifySign($data, $signature, $private_key_path)
  {
    if(!file_exists($private_key_path)) {
      return false;
    }
    $pkcs12 = file_get_contents($private_key_path);
    if (openssl_pkcs12_read($pkcs12, $certs, '123456')) {
      $publicKey = $certs['cert'];
      $ok = openssl_verify($data, $signature, $publicKey);
      if ($ok == 1) {
        return true;
      }
    }
    return false;
  }

}

class obj_file {
    public $fid = '';
    public $file_token = '';
    public $size = 0;
    public $url = '';
    public $type = '';
    public $suffix = '';
    
    public function __construct($file) {
        $this->file_token = $file['file_token'];
        $this->fid = $file['fid'];
        $this->size = $file['size'];
        $this->url = $file['url'];
        $this->type = $file['type'];
        $this->suffix = $file['suffix'];
    }
}
