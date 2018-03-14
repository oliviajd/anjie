<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_bank_mail extends Model
{
    protected  $table='anjie_bank_mail';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function bank_mail($arr)
    {
        $sql = "insert into anjie_bank_mail (transcode, status, iretmsg, url, param, response, work_id) values (?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($arr['transcode'], $arr['status'], $arr['iretmsg'], $arr['url'], $arr['param'], $arr['response'], $arr['work_id']));
        return $rs;
    }
}
