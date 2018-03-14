<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_file_class extends Model
{
    protected  $table='anjie_file_class';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
//列出所有风控审核图片类别
    public function listimagetype()
    {
        $sql = "select id, file_type, file_type_name, file_name, max_length, min_length from anjie_file_class where status = 1 and jc_type = 1";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function listAppImagetype()
    {
        $sql = "select id, file_type, file_type_name, file_name, max_length, min_length from anjie_file_class where status = 1 and jc_type = 1 and is_app =1 order by orderby asc";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //列出所有风控审核图片类别
    public function listimagetypeinquire()
    {
        $sql = "select id, file_type, file_type_name, file_name, max_length, min_length from anjie_file_class where status = 1 and jc_type = 1 and id <= 3";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    //列出所有聚车贷图片类别
    public function listjcrimagetype()
    {
        $sql = "select id, file_type, file_type_name, file_name, max_length, min_length from anjie_file_class where status = 1 and jc_type = 2";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
}
