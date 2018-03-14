<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Suggestion_file extends Model
{
    protected  $table='suggestion_file';
    public $primaryKey='id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }
    /**  
        获取图片列表
    **/
    public function getDetail($where, $order='', $limit='')
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from suggestion_file " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        foreach ($rs as $key => $value) {
            if ($value['file_path'] !== '') {
                $rs[$key]['file_path'] = $path .'/'. $value['file_path'];
            }
        }
        return $rs;
    }
    //添加图像
    public function addimage($param)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $filepath = str_replace($path,'', $param['file_path']);
        $sql = "insert into suggestion_file (suggestion_id, file_class_id, file_type, file_type_name, file_path, status, add_userid, file_id, create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($param['suggestion_id'], $param['file_class_id'], $param['file_type'], $param['file_type_name'], $filepath, '1', $param['add_userid'], $param['file_id'], time(), time()));
        if ($rs !== false) {
            $param['id'] = $this->_pdo->lastInsertId();
            $rs = $this->issetfile($param['id']);
        }
        return $rs;
    }
    //删除图像
    public function deleteimage($param)
    {
        $sql = "update suggestion_file set status = ?, delete_userid = ?, create_time =?, modify_time =? where id = ?";
        $rs = $this->_pdo->execute($sql, array('2', $param['delete_userid'], time(), time(), $param['file_id']));
        return $rs;
    }
    //根据file_id判断该文件是否删除
    public function issetfile($id)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from suggestion_file where id = ? and status = 1";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        $rs['file_path'] = $path . $rs['file_path'];
        return $rs;
    }
    
    
    //通过工作id和file_class_id
    public function getInfoBySuggestionidAndFileclassid($param)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from suggestion_file where file_class_id = ? and suggestion_id = ? and status=1";
        $rs = $this->_pdo->fetchAll($sql, array($param['file_class_id'], $param['suggestion_id']));
        if (!empty($rs)) {
            foreach ($rs as $key => $value) {
                if ($value['file_path'] !== '') {
                    $rs[$key]['file_path'] = $path .'/'. $value['file_path'];
                }
            }
            
        }
        return $rs;
    }
    //通过工作id和file_class_id计算总共有多少张
    public function countBySuggestionidAndFileclassid($param)
    {
        $sql = "select count(*) from suggestion_file where file_class_id = ? and suggestion_id = ? and status=1";
        $rs = $this->_pdo->fetchOne($sql, array($param['file_class_id'], $param['suggestion_id']));
        return $rs;
    }
}
