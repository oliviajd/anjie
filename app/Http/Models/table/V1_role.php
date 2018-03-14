<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class V1_role extends Model
{
    protected  $table='v1_role';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    /*  
        通过file_token获取信息
        @params  file_token      文件token 
    */
    public function getDetail($id)
    {
      $sql = "select * from v1_role where id = ?";
      $rs = $this->_pdo->fetchOne($sql, array($id));
      return $rs;
    }

    public function getrole($where, $order, $limit)
    {
        $sql = "select * from v1_role " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getCount($where)
    {
        $sql = "select count(1) as count from v1_role " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function updateByID($title, $desc, $id)
    {
        $sql = "update v1_role set title = ?, `desc`= ? where id = ?";
        $rs = $this->_pdo->execute($sql, array($title, $desc, $id));
        return $rs;
    }

    public function inserttov1role($param)
    {
        $sql = "insert into v1_role (title, parent_id, `desc`, has_child, create_time, depth) values(?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql,array($param['title'], $param['parent_id'], $param['desc'], $param['has_child'], $param['create_time'], $param['depth']));
        $id = $this->_pdo->lastInsertId();
        return $id;
    }

    public function setHaschild($id, $has_child_before, $has_child_after)
    {
        $sql = "update v1_role set has_child = ? where id = ? and has_child = ?";
        $rs = $this->_pdo->execute($sql, array($has_child_after, $id, $has_child_before));
        return $rs;
    }

    public function deleteById($id)
    {
        $sql = "delete from v1_role where id = ?";
        $rs = $this->_pdo->execute($sql, array($id));
        return $rs;
    }

    public function test($parid,$channels,$dep)
    {
        $this->getChild($html,$parid,$channels,$dep);
        if (is_array($html)) {
          return $html;
        } else {
          return array();
        }
        
    }


    /**
     * 递归查找父id为$parid的结点
     * @param array $html  按照父-》子的结构存放查找出来的结点
     * @param int $parid  指定的父id
     * @param array $channels  数据数组
     * @param int $dep  遍历的深度，初始化为1
     */
    function getChild(&$html,$parid,$channels,$dep){
      /*
       * 遍历数据，查找parId为参数$parid指定的id
       */
      for($i = 0;$i<count($channels);$i++){
        if($channels[$i]['parent_id'] == $parid){
          $html[$channels[$i]['id']] = array('id'=>$channels[$i]['id'],'title'=>$channels[$i]['title'],'dep'=>$dep);
          $this->getChild($html,$channels[$i]['id'],$channels,$dep+1);
        }
      }
    }


   
}
