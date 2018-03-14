<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_origin extends Model
{
    protected  $table='anjie_origin';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function getInfoBymerchantclassno($merchant_class_no)
    {
        $sql = "select * from anjie_origin where merchant_class_number = ?";
        $rs = $this->_pdo->fetchAll($sql, array($merchant_class_no));
        return $rs;
    }
}
