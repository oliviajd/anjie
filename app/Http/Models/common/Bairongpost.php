<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\table\Bairong_log;
use Mail;

define('BAIRONG_API_URL', env('BAIRONG_API_URL'));

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 百融的接口
 *
 * @author win7
 */
class Bairongpost
{

    /**
     * 百融失信查询
     * @param 
     * </p>
     * @return 
     * 
     */
    public function bairongcredit($id_card, $tel, $name) 
    {
        $this->_bairong_log = new Bairong_log();
        $common = new Common();
        $params['currenttime'] = $common->getMillisecond();
        $params['salt'] = $this->random_str(8);
        $secretKey = "whosyourdaddy!";
        $query =  $params['currenttime'] .'#' . $id_card . '#' . $name . '#' . $params['salt'];
        $params['orgSign'] = md5(md5($secretKey . $query) . $params['salt']);

        $data = 'cell=' . urlencode($tel) . '&currentTime=' . strval($params['currenttime']) . '&id=' . $id_card . '&name=' . urlencode($name) . '&salt=' . $params['salt'] . '&orgSign=' . $params['orgSign'];
        $url = BAIRONG_API_URL;
        $r = $common->object_array(json_decode($common->curl_post($url, $data)));
        $obj = $common->object_array(json_decode($r['obj']));
        $result = $r['result'];
        if ($result == false) {
            $code = '';
            $params['id_card'] = $id_card;
            $params['tel'] = $tel;
            $params['name'] = $name;
            $flag = Mail::send('common.bairongemail',['arr'=>$params],function($message){
                $to = 'jiangd@ifcar99.com';
                $message->to($to)->subject('百融失信查询数据运行错误');
            });
        } else {
            $code = $obj['code'];
        }
        $addlog = $this->_bairong_log->addlog($url, $data, json_encode($r), $code, $result);       
        return $r;
    }
     /**
   *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
   * 
   * @author Wu Junwei <www.wujunwei.net>
   * 
   * @param int $length 需要生成的字符串的长度
   * @return string 包含 大小写英文字母 和 数字 的随机字符串
   */
  public function random_str($length)
  {
      //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
      $arr = array_merge(range('a', 'z'), range('A', 'Z'));
   
      $str = '';
      $arr_len = count($arr);
      for ($i = 0; $i < $length; $i++)
      {
          $rand = mt_rand(0, $arr_len-1);
          $str.=$arr[$rand];
      }
   
      return $str;
  }
}
function curl_post_bairong($post_url, $post_data) 
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $post_url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    return $error ? $error : $result;
}
