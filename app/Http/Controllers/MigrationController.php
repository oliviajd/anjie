<?php

namespace App\Http\Controllers;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Migration;

use Mail;

class MigrationController extends Controller
{ 
    private $_system = null;
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_migration = new Migration();
    }
    public function migrationdata()
    {
        $rs = $this->_migration->migrationdata();
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    public function imigratefile()
    {
        $rs = $this->_migration->imigratefile();
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    public function getdata()
    {
        $rs = $this->_migration->get();
        // $rs = $this->_migration->migrationdata();
    }
    public function migrationimage()
    {
        $rs = $this->_migration->migrationimage();
    }
    public function moveusers()
    {
        $rs = $this->_migration->updateusers();
    }
}