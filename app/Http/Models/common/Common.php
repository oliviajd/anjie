<?php

namespace App\Http\Models\common;
use App\Http\Models\common\Constant;
use App\Http\Models\table\Api2_log;
use Request;
use Illuminate\Support\Facades\Log;

class Common
{
    public $log = false; // 是否写日志
    private $_times = array();
    protected $_api2_log = null;

    function __construct() {
        //请求接收时间
        $this->_times['request'] = $_SERVER['REQUEST_TIME'];
        //接口开始执行时间
        $this->_times['start'] = microtime(1);
        $this->_times['start_run'] = microtime(1); //脚本开始执行时间

    }
    //获取当前的控制器名
    public function getController()
    {
        $action = \Route::current()->getActionName();
        list($class, $method) = explode('@', $action);
        $class = substr(strrchr($class,'\\'),1);
        return $class;
    }
    //获取当前的action名
    public function getAction()
    {
        $action = \Route::current()->getActionName();
        list($class, $method) = explode('@', $action);
        $class = substr(strrchr($class,'\\'),1);
        return $method;
    }

    //curl模拟http请求
    public function curltest($url, $data=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        $return['data'] = curl_exec($ch);
        $return['rinfo']=curl_getinfo($ch); 
        curl_close($ch);
        return $return;
    }

    function curl_post($post_url, $post_data) 
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        Log::info("请求url:{$post_url}");
        Log::info("请求参数:".json_encode($post_data));
        $result = curl_exec($curl);
        $error = curl_error($curl);
        Log::info("错误代码：{$error}");
        Log::info("请求结果:{$result}");
        curl_close($curl);
        return $error ? $error : $result;
    }
    /**
     *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
     * 
     * @author 蒋丹
     * 
     * @param int $length 需要生成的字符串的长度
     * @return string 包含 大小写英文字母 和 数字 的随机字符串
     */
    function random_str($length)
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
    /**
     *  生成指定长度的随机数
     * 
     * @author 蒋丹
     * 
     * @param int $length 需要生成的字符串的长度
     * @return string 
     */
    function generate_code($length = 4) 
    {
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }
    /**
     *  生成当前13位Unix时间戳
     * 
     * @author 蒋丹 
     * @return string 当前时间戳
     */
    public function getMillisecond() 
    { 
        list($t1, $t2) = explode(' ', microtime()); 
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000); 
    }

    /* 
     * microsecond 微秒     millisecond 毫秒 
     *返回时间戳的毫秒数部分 
    */  
    function get_millisecond()  
    {  
        list($usec, $sec) = explode(" ", microtime());  
        $msec=round($usec*1000);  
        return $msec;       
    }
    /* 
     * cid     身份证号码 
     *根据身份证号码返回性别
    */ 
    function get_xingbie($cid) 
    {  
        //根据身份证号，自动返回性别  
        // if(!$this->isIdCard($cid)) return false;  
        $sexint = (int)substr($cid,16,1);  
        return $sexint % 2 === 0 ? '女' : '男';  
    }  
    /* 
     * number     身份证号码 
     *判断传入的是否身份证号码
    */ 
    // function isIdCard($number) 
    // {  
    //     if(strlen($number)!=18){ 
    //         return $this->output(false, Constant::ERR_FAILED_NO, '身份证号码格式不对');  //如果返回的是false
    //     } 
    //     //检查是否是身份证号  
    //     // 转化为大写，如出现x  
    //     $number = strtoupper($number);  
    //     //加权因子  
    //     $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);  
    //     //校验码串  
    //     $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');  
    //     //按顺序循环处理前17位  
    //     $sigma = 0;  
    //     for($i = 0;$i < 17;$i++){  
    //         //提取前17位的其中一位，并将变量类型转为实数  
    //         $b = (int) $number{$i};      //提取相应的加权因子  
    //         $w = $wi[$i];     //把从身份证号码中提取的一位数字和加权因子相乘，并累加  
    //         $sigma += $b * $w;  
    //     }  
    //     //计算序号  
    //     $snumber = $sigma % 11;  
    //     //按照序号从校验码串中提取相应的字符。  
    //     $check_number = $ai[$snumber];  
    //     if($number{17} == $check_number){   
    //         return $this->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功 
    //     }else{  
    //         return $this->output(false, Constant::ERR_FAILED_NO, '身份证号码格式不对');  //如果返回的是false
    //     }  
    // }
    function isIdCard($number) {
        if (strlen($number) == 15) {
            $number = idcard_15to18($number);
        }
        $number = strtoupper($number);
        //加权因子 
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码串 
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $sigma = 0;  
        //按顺序循环处理前17位 
        for ($i = 0; $i < 17; $i++) {
            //提取前17位的其中一位，并将变量类型转为实数 
            $b = (int) $number{$i};

            //提取相应的加权因子 
            $w = $wi[$i];

            //把从身份证号码中提取的一位数字和加权因子相乘，并累加 
            $sigma += $b * $w;
        }
        //计算序号 
        $snumber = $sigma % 11;

        //按照序号从校验码串中提取相应的字符。 
        $check_number = $ai[$snumber];

        if ($number{17} == $check_number) {
            return $this->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功 
        } else {
            return $this->output(false, Constant::ERR_FAILED_NO, '身份证号码格式不对');  //如果返回的是false
        }
    }

    function idcard_15to18($idcard) {
        if (strlen($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码  
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . idcard_verify_number($idcard);
        return $idcard;
    }
    // 计算身份证校验码，根据国家标准GB 11643-1999  
    function idcard_verify_number($idcard_base) {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子  
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值  
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    public function setlog($boolval=TRUE)
    {
        if ($boolval) {
            $this->log = TRUE;
        }
    }

    /**
     * error_no : 错误类型 (0: 正常 非0：消息提示)
     * error_msg : 消息提示
     * result : 返回结果
     */
    public function output($result, $error_no = false, $error_msg = false, $callback = '') 
    {
        $output = array();
        if ($error_no === false) {
            $error_no = Constant::ERR_SUCCESS_NO;
            $error_msg = Constant::ERR_SUCCESS_MSG;
        }
        $output['error_no'] = intval($error_no);
        $output['error_msg'] = $error_msg;
        $output['result'] = $result;

        $this->_times['end'] = microtime(1);
        $this->_do_log(var_export($output, true), $output['error_no']);

        return json_encode($output);
    }   
    /**
     * 用数据库API日志
     */
    public function _do_log($str, $error_no) 
    {
        $this->_times['end'] = microtime(1);
        if ($this->log == TRUE) {
            $data = array();
            if (isset($_SESSION['user_id']) && isset($_SESSION['name'])) {
                $data['uid'] = $_SESSION['user_id'];
                $data['username'] = $_SESSION['name'];
            } else {
                $data['uid'] = '';
                $data['username'] = '';
            }
            
            $data['work_id'] =  Request::input('work_id', '');
            $data['action'] = $this->_get_api_method_name();
            $data['path'] = $this->_get_path();
            $data['ip'] = $_SERVER["REMOTE_ADDR"];
            $data['url'] = $_SERVER['REQUEST_URI'];
            $data['error_no'] = $error_no;
            $data['msg'] = serialize($str);
            $data['script_time'] = $this->_times['end'] - $this->_times['start']; // api 脚本执行时间
            $data['create_time'] = $this->_times['request'];
            $this->_api2_log = new Api2_log();
            $this->_api2_log->insertLog($data);
            $data['post'] = $_POST;
            $data['get'] = $_GET;
            $data['request'] = Request::all();
            $str = array();
            $str[] = 'date:' . date('Y-m-d H:i:s');
            $str[] = print_r($data, true);
            $str[] = "\n\n";
            return file_put_contents(storage_path() . '/apilogs/api_' . date('Y-m-d') . '.log', implode("\n", $str), FILE_APPEND);
        }
    }
    /**
     * 获得接口名称
     */
    private function _get_api_method_name()
    {
        $uri = Request::path();
        $segments = explode('/', strtolower($uri));
        return implode('.', $segments);
    } 
    /**
     * 获得接口名称
     */
    private function _get_path()
    {
        $uri = Request::path();
        return '/'. $uri;
    }  

    /**
     * 对象转数组
     */
    public function object_array($array)
    {
        if (is_object($array)) {
            $array = $this->object_array((array)($array));
        }
        if (is_array($array)) {
            foreach ($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    // 日志
    function do_log() 
    {
        $argvs = func_get_args();
        $str = array();
        $str[] = 'date:' . date('Y-m-d H:i:s');
        foreach ($argvs as $v) {
            $str[] = print_r($v, true);
        }
        $str[] = "\n\n";
        return file_put_contents(storage_path() . '/logs/' . date('Y-m-d') . '.log', implode("\n", $str), FILE_APPEND);
    }
    //验证是否是手机号码
    function check_telephone_number($phonenumber)
    {
        // if(preg_match("/^1[34578]{1}\d{9}$/",$phonenumber)){  
        if(preg_match("/^\d{11}$/",$phonenumber)){  
            return $this->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //是手机号码 
        }else{  
            return $this->output(false, Constant::ERR_FAILED_NO, '手机号码格式不对');    //不是手机号，则返回false
        }  
    }
    function checktelephone($telephone)
    {
        // if(preg_match("/^1[34578]{1}\d{9}$/",$telephone)){  
        if(preg_match("/^\d{11}$/",$telephone)){  
            return true;    //是手机号码 
        }else{  
            return false;    //不是手机号，则返回false
        }  
    }
    function checkcardid($cardid)
    {
        // if(preg_match("/^\d{17}(\d|x)$/i",$cardid)){  
        if(preg_match("/^\d{15}$)|(^\d{17}([0-9]|X)$/isu",$cardid)){  
            return true;    //是手机号码 
        }else{  
            return false;    //不是手机号，则返回false
        }  
    }
    //验证是否是第一个数字不为0的数字
    function checknumber($number)
    {
        if(preg_match("/(^[1-9]([0-9]*)$|^[0-9]$)/",$number)){  
            return true;    //是数字格式
        }else{  
            return false;    //不是数字格式，则返回false
        }  
    }
    //验证浮点型数字格式
    function checkfloat($float)
    {
        if(preg_match("/^[1-9][\d]*\.[\d]*$|^[0]\.[\d]*$|^[1-9][\d]*[\d]*$/",$float)){  
            return true;    //是数字格式
        }else{  
            return false;    //不是数字格式，则返回false
        }  
    }
    //根据身份证获取年龄
    function getAgeByID($id)
    { 
        if(empty($id)) return ''; 
        $date=strtotime(substr($id,6,8));
        //获得出生年月日的时间戳 
        $today=strtotime('today');
        //获得今日的时间戳 
        $diff=floor(($today-$date)/86400/365);
        //得到两个日期相差的大体年数            
        //strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比 
        $age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;      
        return $age; 
    } 
}