<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use Upyun\Upyun;
use Upyun\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Constant;
use App\Http\Models\table\Anjie_sms_message;

class Message extends Model
{
  protected $_anjie_sms_message = null;

  public function __construct()
  {
    parent::__construct();
    $this->_anjie_sms_message = new Anjie_sms_message();
  }
  /**  
    *发送短信验证码
    *@params  token      token 
    */
  public function sendmessage($params)
  {
    $isset = $this->_anjie_sms_message->issetmessage($params);
    if (!empty($isset)) {
      $result = $this->send($isset['account'], $isset['content']);
    if ($result === true) {
        return $this->_common->output($isset['id'], Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
      } else {
        return $result;
      }
    } else {
      $result = $this->send($params['account'], $params['content']);
    if ($result === true) {
        $rs = $this->_anjie_sms_message->insertmessage($params);
        return $this->_common->output($rs['id'], Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
      } else {
        return $result;
      }
    }
  }


  public function send($mobile, $content)
  {
    return true;
    $config['account'] = env('SMS_NEW_ACCOUNT');
    $config['password'] = env('SMS_NEW_PASSWORD');
    $config['url'] = env('SMS_NEW_URL');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $config['url']);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
      'account' => $config['account'],
      'password' => $config['password'],
      'userid' => 1239,
      'mobile' => $mobile,
      'content' => $content . '【林润万车】',
      'json' => '1',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // php version >= 5.5 需要 传filesize
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    $xml = json_decode($result);
    if ($xml->code == 'Success') {
      return true;
    } else {
      $this->_common->do_log('SEND_SMS_ERROR:', $error);
      $msg =$this->_common->object_array(json_decode(json_encode($xml->msg)));
      return $this->_common->output(false, Constant::ERR_FAILED_NO, $msg[0]);
    }
  }
//验证验证码是否存在
  public function checkmessage($param)
  {
    $issetmessage = $this->_anjie_sms_message->checkmessage($param);
    return $issetmessage;
  }
  
  
}

