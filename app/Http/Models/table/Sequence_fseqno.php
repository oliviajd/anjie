<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Sequence_fseqno extends Model
{
    protected  $table='sequence_fseqno';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    /**  
    *自增id
    */
    public function sequence_increment()
    {
        $res = array();
        $sql = "insert into sequence_fseqno (name) value(?)";
        $rs = $this->_pdo->execute($sql, array('fseqno'));
        $res['id'] = $this->_pdo->lastInsertId();
        $sql = "delete from sequence_fseqno where id = ?";
        $delete = $this->_pdo->execute($sql, array($res['id']));   //删除刚刚插入的记录
        if ($delete == false) {
            return false;
        }
        return $res['id'];
    }
    /**  
    *自增id
    */
    public function Serialno_increment()
    {
        $res = array();
        $sql = "insert into sequence_Serialno (name) value(?)";
        $rs = $this->_pdo->execute($sql, array('Serialno'));
        $res['id'] = $this->_pdo->lastInsertId();
        $sql = "delete from sequence_Serialno where id = ?";
        $delete = $this->_pdo->execute($sql, array($res['id']));   //删除刚刚插入的记录
        if ($delete == false) {
            return false;
        }
        return $res['id'];
    }
    /**  
    *自增id
    */
    public function Seqno_increment()
    {
        $res = array();
        $sql = "insert into sequence_Seqno (name) value(?)";
        $rs = $this->_pdo->execute($sql, array('Seqno'));
        $res['id'] = $this->_pdo->lastInsertId();
        $sql = "delete from sequence_Seqno where id = ?";
        $delete = $this->_pdo->execute($sql, array($res['id']));   //删除刚刚插入的记录
        if ($delete == false) {
            return false;
        }
        return $res['id'];
    }
   
}
