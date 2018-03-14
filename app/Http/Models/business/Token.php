<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Request;
use App\Http\Models\common\Constant;

class Token extends Model
{
	private $_tokens;

    public function __construct() 
    {
        parent::__construct();
        $this->_tokens = array();
    }

    public function detail($token) 
    {
        $token_str = trim($token);
        if (!$token_str) {
            return false;
        }
        return new obj_token($this->_get($token_str));
    }

    public function next($token) 
    {
        $info = $this->_get($token);
        $sql = "select * from api2_token where id > ? and user_id = ? and `from` = ? order by id asc limit 1";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($info['id'], $info['user_id'], $info['from']));
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!empty($r)) {
            return new obj_token($r);
        } else {
            return false;
        }
    }

    public function check($token) 
    {
        $token_str = trim($token);
        if (!$token_str) {
            return false;
        }
        $info = $this->_get($token_str);
        //token存在且未过期才有效
        if (!$info) {
            return Constant::ERR_TOKEN_NOT_EXISTS_NO;
        } else if ($info['over_time'] < time()) {
            return Constant::ERR_TOKEN_EXPIRE_NO;
        } else if ($info['status'] != 1) {
            return Constant::ERR_TOKEN_DISABLED_NO;
        } else {
            return true;
        }
    }

    public function user($token) 
    {
        $token_str = trim($token);
        if (!$token_str) {
            return false;
        }
        $token_info = $this->_get($token_str);
        if ($token_info && $token_info['over_time'] > time()) {
            return json_decode($token_info['cache']);
        } else {
            //todo 退出
            return false;
        }
    }

    // 通过token获取用户信息
    private function _get($token) 
    {
        if (isset($this->_tokens[$token])) {
            //读缓存数据
        } else {
            $sql = "select * from api2_token where token = ?";
            $stmt = $this->_pdo->prepare($sql);
            $stmt->execute(array($token));
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($r)) {
                $this->_tokens[$token] = $r;
            } else {
                return false;
            }
        }
        return $this->_tokens[$token];
    }

    public function create($data) 
    {      
        $param['from'] = isset($data['from']) ? strtolower($data['from']) : '';
        $param['device_id'] = isset($data['device_id']) ? trim($data['device_id']) : 0;
        $param['token'] = md5($data['user_id'] . microtime(1) . $param['device_id'] . $param['from'] . rand(0, 999));
        $param['refresh_token'] = md5($data['user_id'] . microtime(1) . $param['device_id'] . $param['from'] . rand(1000, 1999) . 'refresh');
        $param['over_time'] = time() + 3600 * 24 * 60;
        $param['refresh_time'] = time() + 3600 * 24 * 180;
        $param['user_id'] = intval($data['user_id']);   
        $param['device'] = isset($data['device']) ? trim($data['device']) : '';
        $param['ip'] = $this->get_ip();
        $param['cache'] = isset($data['cache']) ? json_encode($data['cache']) : '';
        $param['create_time'] = time();
        //检查是否有同一user_id和from的token，有的话设置为无效
        $sql = "update api2_token set status = 2, modify_time = ? where user_id = ? and `from` = ? and status =1";
        $stmt = $this->_pdo->prepare($sql);
        $updates = $stmt->execute(array(time(), $param['user_id'], $param['from']));

        $sql = "insert into api2_token(token, refresh_token, over_time, refresh_time, user_id, device_id, device, ip, `from`, `cache`, create_time) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_pdo->prepare($sql);
        $inserts = $stmt->execute(array($param['token'], $param['refresh_token'], $param['over_time'], $param['refresh_time'], $param['user_id'],$param['device_id'], $param['device'], $param['ip'], $param['from'], $param['cache'], $param['create_time']));

        return new obj_token($param);
    }

    // 获得ip地址
    public function get_ip() 
    {
        $user_IP = isset($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        return ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
    }
    
    public function destroytoken($user_id)
    {
        $sql = "update api2_token set status = 2, modify_time = ? where user_id = ? and status =1";
        $stmt = $this->_pdo->prepare($sql);
        $updates = $stmt->execute(array(time(), $user_id));
    }
}

class obj_token 
{

    public $token = '';
    public $over_time = 0;
    public $refresh_token = '';
    public $refresh_time = 0;
    public $create_time = 0;

    public function __construct($token) 
    {
        $this->token = $token['token'];
        $this->over_time = $token['over_time'];
        $this->refresh_token = $token['refresh_token'];
        $this->refresh_time = $token['refresh_time'];
        $this->create_time = $token['create_time'];
        $this->device = $token['device'];
        $this->ip = $token['ip'];
    }

}