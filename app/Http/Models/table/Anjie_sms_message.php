<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_sms_message extends Model
{
    protected  $table='anjie_sms_message';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //发送短信验证码入库
    public function insertmessage($params)
    {
      $param = $params;
      $param['over_time'] = time() + 15 * 60;
      $param['create_time'] = time();
      $param['modify_time'] = time();
      $sql = "insert into anjie_sms_message (account, type, sms_code, over_time, create_time, modify_time, content) values (?, ?, ?, ?, ?, ?, ?)";
      $rs = $this->_pdo->execute($sql, array($param['account'], $param['type'], $param['sms_code'], $param['over_time'], $param['create_time'], $param['modify_time'], $param['content']));
      if ($rs == false) {
          return $rs;
      }
      $param['id'] = $this->_pdo->lastInsertId();
      return $param;
    }

    public function checkmessage($param)
    {
        $sql = "select * from anjie_sms_message where account = ? and type = ? and sms_code =? and over_time > ?";
        $rs = $this->_pdo->fetchOne($sql, array($param['account'], $param['type'], $param['sms_code'], time()));
        return $rs;
    }
    
    public function issetmessage($param)
    {

        $sql = "select * from anjie_sms_message where account = ? and type = ? and over_time > ?";
        $rs = $this->_pdo->fetchOne($sql, array($param['account'], $param['type'], time()));
        return $rs;
    }
}
