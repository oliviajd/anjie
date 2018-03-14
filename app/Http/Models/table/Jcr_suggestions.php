<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Jcr_suggestions extends Model
{
    protected  $table='jcr_suggestions';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    //添加标的信息
    public function suggestionssubmit($params)
    {
    	$sql = "insert into jcr_suggestions (suggestions, user_id, create_time, modify_time) values (?, ?, ?, ?)";
    	$rs = $this->_pdo->execute($sql, array($params['suggestions'], $params['user_id'], time(), time()));
        $return = $params;
        $return['id'] = $this->_pdo->lastInsertId();
        if ($return) {
            return $return;
        }
        return false;
    }
}
