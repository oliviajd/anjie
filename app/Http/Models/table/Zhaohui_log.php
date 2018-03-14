<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Zhaohui_log extends Model
{
    protected  $table='zhaohui_log';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function addlog($params)
    {
        $sql = "insert into zhaohui_log (url, param, response, orderno, idno) values (?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($params['url'], $params['param'], $params['response'], $params['orderno'], $params['idno']));
        return $rs;
    }
}
