<?php

namespace App\Http\Models\common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Token;

class Auth extends Model
{
	public $api_prefix = 'api'; // 接口前缀
    public $log = true; // 是否写日志
    public $in = array(); //api输入参数
    private $token = '';
    private $user = array();
    private $_times = array();
    private $info = null;
    private $_tokenModel = null;

    public function run()
    {
    	//请求接收时间
        $this->_times['request'] = $_SERVER['REQUEST_TIME'];
        //接口开始执行时间
        $this->_times['start'] = microtime(1);
        //处理路由信息，并载入接口配置信息
        if ($this->_load($this->_get_api_method_name())) {
            //TODO 接口请求次数限制
        } else {
            $this->suboutput(false, Constant::ERR_API_NOT_EXISTS_NO, Constant::ERR_API_NOT_EXISTS_MSG);
        }
        //处理接收到的参数，并检查是否需要token信息
        $this->input();

        //初始化用户信息
        if ($this->_need_token()) {
            //验证令牌有效性
            $this->check_token($this->in['token']);
            //获取用户详细信息
            $this->user = $this->_tokenModel->user($this->in['token']);
            // //保存token信息
            $this->token = $this->_tokenModel->detail($this->in['token']);
            if ($this->_need_permission()) {
                $this->check_permission($this->user->user_id, $this->info['method_id']);
            }
        }
        $this->_times['start_run'] = microtime(1); //脚本开始执行时间
    }

    /**
     * 载入接口信息
     * false: 接口不存在 true: 接口存在
     */
    private function _load($api_name) 
    {
    	$sql = "select system_input_id, application_input_id, response_id, check_permission, method_id from api2_method 
    	where method_name_en = ? limit 1";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($api_name));
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!empty($r)) {
            $this->info = $r;
            return true;
        } else {
            return false;
        };
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
     * 对api输入参数 进行规划
     */
    public function input() 
    {
        if (0) {
            error_reporting(E_ALL ^ E_NOTICE);
        }
        $input = array(); //最终输入参数
        $arr_input = array(); // 接口定义的输入参数的规则
        $user_input = Request::all();// 用户提交的参数
        // 系统输入
        if ($this->info['system_input_id']) {
        	$sql = "select a.cate_type, a.cate_name, b.* from api2_category a, api2_field b where b.obj_id = a.id and b.item_id = ?";
            $stmt = $this->_pdo->prepare($sql);
            $stmt->execute(array($this->info['system_input_id']));
            $this->in_sys = $system_input = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $arr_input = array_merge($arr_input, $system_input);
        }
        // 应用输入
        if ($this->info['application_input_id']) {
            $sql = "select a.cate_type, a.cate_name, b.* from api2_category a, api2_field b where b.obj_id = a.id and b.item_id = ?";
            $stmt = $this->_pdo->prepare($sql);
            $stmt->execute(array($this->info['application_input_id']));
            $this->in_app = $app_input = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $arr_input = array_merge($arr_input, $app_input);
        }
        // unset 为空的参数
        foreach ($user_input as $k => $v) {
            if ($v == '') {
                unset($user_input[$k]);
            }
        }
        // 循环接口输入条件，依条件判断输入参数，数据是否合法，是否必须，是否默认值
        foreach ($arr_input as $k => $v) {
            $field_type = trim($v['cate_name']); // 字段类型
            $field_name = trim($v['field_name']); // 字段名称
            if ((isset($user_input[$field_name]) && $field_name != '') || isset($_FILES[$field_name])) {
                if ($field_type == 'Int') {
                    $input[$field_name] = (int) (trim($user_input[$field_name]));
                } elseif ($field_type == 'FormInputFile') {
                    if ($_FILES[$field_name]) {
                        $input[$field_name] = $_FILES[$field_name]; // FILES
                    }
                } else {
                    if ($field_type == 'String') {
                        $input[$field_name] = $user_input[$field_name]; // string
                    }
                }
            } else {
                if ($v['is_necessary']) { // 1:yes
                    $this->suboutput(false, Constant::ERR_FILED_NECESSARY_NO, '[(' . $field_type . ')' . $field_name . '] ' . Constant::ERR_FILED_NECESSARY_MSG);
                } elseif (isset($v['default_value']) && $v['default_value'] != '') {
                    $input[$field_name] = $v['default_value'];
                }
            }
        }
        $this->in = $input;
        return $input;
    }

    /**
     * 检查是否需要令牌
     */
    private function _need_token()
    {
        $need_token = false;
        if (!$this->in_sys) {
            return false;
        }
        foreach ($this->in_sys as $v) {
            if (isset($v['field_name']) && $v['field_name'] == 'token' && $v['is_necessary'] == 1) {
                $need_token = true;
                break;
            }
        }
        return $need_token;
    }

    /**
     * 检查令牌是否有效
     */
    public function check_token($token) 
    {
        $this->_tokenModel = new Token();
        $data['user_id'] = 9036;
        $data['device_id'] = 0;
        $data['from']='web';
        $data['device'] = '';
        $data['cache'] = '';
        $r = $this->_tokenModel->check($token);
        if ($r === Constant::ERR_TOKEN_NOT_EXISTS_NO) {
            $this->suboutput(FALSE, Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        } elseif ($r === Constant::ERR_TOKEN_EXPIRE_NO) {
            $this->suboutput(FALSE, Constant::ERR_TOKEN_EXPIRE_NO, Constant::ERR_TOKEN_EXPIRE_MSG);
        } elseif ($r === Constant::ERR_TOKEN_DISABLED_NO) {
            //todo 查该用户的下一个token
            $info = $this->_tokenModel->next($token);
            $device = json_decode($info->device) ? json_decode($info->device)->model . '[' . json_decode($info->device)->os . ']' : $info->device;
            $msg = empty($info->device) ? 
                    '您的聚车账号已于' . date('m月d日H:i:s', $info->create_time) . '在其他地方登录。登录IP是' . $info->ip . '，请注意账号安全。':
                '您的聚车账号已于' . date('m月d日H:i:s', $info->create_time) . '在其他地方登录。登录设备是' . $device . '，请注意账号安全。';
            $this->suboutput(FALSE, Constant::ERR_TOKEN_DISABLED_NO, $msg);
        } else {

        }
    }

    /**
     * error_no : 错误类型 (0: 正常 非0：消息提示)
     * error_msg : 消息提示
     * result : 返回结果
     */
    public function suboutput($result, $error_no = false, $error_msg = false, $callback = '') 
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

        header('REQUEST-TIME:' . $this->_times['request']);
        header('START-TIME:' . $this->_times['start']);
        header('END-TIME:' . $this->_times['end']);
        header('COST-TIME:' . ($this->_times['end'] - $this->_times['start']));
        header('RUN-TIME:' . ($this->_times['end'] - (isset($this->_times['start_run']) ? $this->_times['start_run'] : 0)));

        // 有错误写入日志
        // if ($output['error_no'] != 200)
        $this->_do_log(var_export($output, true), $output['error_no']);

        if (isset($_REQUEST['t']) && $_REQUEST['t'] == 'a') {
            echo '<pre>';
            if (isset($_REQUEST['sql']) && $_REQUEST['sql'] == true && DUMP_SQL) {
                var_dump($this->db->queries);
                exit();
            }
            var_dump($output);
            echo '</pre>';
            exit();
        } else {
            exit(json_encode($output));
        }
    }

    /**
     * 用数据库API日志
     */
    private function _do_log($str, $error_no) 
    {
        if ($this->log == TRUE) {
            $data = array();
            $data['uid'] = '';
            $data['username'] = '';
            if (!empty($this->user)) {
                $data['uid'] = $this->user->user_id;
                $data['username'] = $this->user->loginname;
            }
            $data['action'] = $this->_get_api_method_name();
            $data['url'] = $_SERVER['REQUEST_URI'];
            $data['error_no'] = $error_no;
            $data['msg'] = serialize($str);
            $data['script_time'] = $this->_times['end'] - $this->_times['start']; // api 脚本执行时间
            $data['create_time'] = date('Y-m-d H:i:s', $this->_times['request']);
            $sql = "insert into api2_log(uid, username, action, url, error_no, msg, script_time, create_time) values(?, ?, ?, ?, ? , ?, ?, ?)";
            $stmt = $this->_pdo->prepare($sql);
            $inserts = $stmt->execute(array($data['uid'], $data['username'], $data['action'], $data['url'], $data['error_no'], $data['msg'], $data['script_time'], $data['create_time']));
        }
    }

    /**
     * 检查是否需要令牌
     */
    private function _need_permission() 
    {
        return intval($this->info['check_permission']) === 1;
    }


}