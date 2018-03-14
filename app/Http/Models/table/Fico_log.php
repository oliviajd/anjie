<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Fico_log extends Model
{
    protected  $table='fico_log';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function addlog($url, $param, $response, $retCode)
    {
        $sql = "insert into fico_log (url, param, response, retCode) values (?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($url, $param, $response, $retCode));
        return $rs;
    }
}
