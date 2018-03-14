<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Models\common\Common;
use App\Http\Models\common\Pdo;
use View;
date_default_timezone_set('PRC');

class Controller extends BaseController
{
	protected $_common = null;
    protected $_pdo = null;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->_common = new Common();
        $this->_pdo = new Pdo();
    }
    

    //输出信息整理成数组
    public function output($errormsg='', $errorcode=0)
    {
        $result['errormsg'] = $errormsg;
        $result['errorcode'] = $errorcode;
        $result['error_no'] = $errorcode;
        $result['error_msg'] = $errormsg;
        return $result;
    }
}
