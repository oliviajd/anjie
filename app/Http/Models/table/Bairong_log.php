<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Bairong_log extends Model
{
    protected  $table='bairong_log';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function addlog($url, $param, $response, $code, $result)
    {
        $sql = "insert into bairong_log (url, param, response, code, result) values (?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($url, $param, $response, $code, $result));
        return $rs;
    }
}
