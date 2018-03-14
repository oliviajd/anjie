<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_query_userid extends Model
{
    protected  $table='anjie_query_userid';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function getallbyuserid($user_id)
    {
        $sql = "select * from anjie_query_userid where user_id = ? and is_valid = 1";
        $rs = $this->_pdo->fetchAll($sql, array($user_id));
        return $rs;
    }
}
