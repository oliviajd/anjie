<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Common;

class Models extends Model
{
	private $_tokens;

    public function __construct() 
    {
        parent::__construct();
        $dsn = "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_DATABASE') . ";port=" . env('DB_PORT');
        $this->_pdo = new \PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'),  array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));

        $this->_common = new Common();
    }

    //输出信息整理成数组
    public function output($errormsg='', $errorcode=0)
    {
        $result['errormsg'] = $errormsg;
        $result['errorcode'] = $errorcode;
        $result['error_msg'] = $errormsg;
        $result['error_no'] = $errorcode;
        return $result;
    }
}