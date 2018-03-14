<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Jcr_file extends Model
{
    protected  $table='jcr_file';
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
    public function getDetail($where, $order='', $limit='', $fix = 0)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from jcr_file " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        if (!$fix){
            foreach ($rs as $key => $value) {
                if ($value['file_path'] !== '') {
                    $rs[$key]['file_path'] = $path .'/'. $value['file_path'];
                }
            }
        }
        return $rs;
    }
    //添加图像
    public function addimage($param)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $filepath = str_replace($path,'', $param['file_path']);
        $sql = "insert into jcr_file (verify_id, file_class_id, file_type, file_type_name, file_path, status, add_userid, file_id, filename, ifcar99_path, create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($param['verify_id'], $param['file_class_id'], $param['file_type'], $param['file_type_name'], $filepath, '1', $param['add_userid'], $param['file_id'], $param['filename'], $filepath, time(), time()));
        if ($rs !== false) {
            $param['id'] = $this->_pdo->lastInsertId();
            $rs = $this->issetfile($param['id']);
        }
        return $rs;
    }
    //修改图像
    public function updateimage($param)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $filepath = str_replace($path,'', $param['file_path']);
        $sql = "update jcr_file set file_type = ?, file_type_name = ?, file_path = ?, status = ?, file_id = ?, filename = ?, ifcar99_path = ?, create_time = ?, modify_time = ? where  verify_id = ? and file_class_id = ? and add_userid = ?";
        $rs = $this->_pdo->execute($sql, array( $param['file_type'], $param['file_type_name'], $filepath, '1', $param['file_id'], $param['filename'],$filepath, time(), time(), $param['verify_id'], $param['file_class_id'], $param['add_userid']));
        if ($rs !== false) {
            $param['id'] = $this->_pdo->lastInsertId();
            $rs = $this->issetfile($param['id']);
        }
        return $rs;
    }
    //删除图像
    public function deleteimage($param)
    {
        $sql = "update jcr_file set status = ?, delete_userid = ?, create_time =?, modify_time =? where id = ?";
        $rs = $this->_pdo->execute($sql, array('2', $param['delete_userid'], time(), time(), $param['file_id']));
        return $rs;
    }
    //根据file_id判断该文件是否删除
    public function issetfile($id)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from jcr_file where id = ? and status = 1";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        if ($rs){
            $rs['file_path'] = $path . $rs['file_path'];
        }
        return $rs;
    }
    
    
    //通过工作id和file_class_id
    public function getInfoByVerifyidAndFileclassid($param)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from jcr_file where file_class_id = ? and verify_id = ? and status=1";
        $rs = $this->_pdo->fetchAll($sql, array($param['file_class_id'], $param['verify_id']));
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
    public function countByVerifyidAndFileclassid($param)
    {
        $sql = "select count(*) from jcr_file where file_class_id = ? and verify_id = ? and status=1";
        $rs = $this->_pdo->fetchOne($sql, array($param['file_class_id'], $param['verify_id']));
        return $rs;
    }
    //暂存图片信息
    public function storageverifyfile($params)
    {
        $sql = "update jcr_file set status = ? where verify_id = ? and status =1";
        $rs = $this->_pdo->execute($sql, array($params['status'], $params['verify_id']));
        return $rs;
    }
    //删除图片信息
    public function deleteverifyfile($params)
    {
        $sql = "update jcr_file set status = 2 where verify_id = ? and status =3";
        $rs = $this->_pdo->execute($sql, array($params['verify_id']));
        return $rs;
    }
    //恢复图片信息
    public function recoververifyfile($params)
    {
        $sql = "update jcr_file set status = 1 where verify_id = ? and status =3";
        $rs = $this->_pdo->execute($sql, array($params['verify_id']));
        return $rs;
    }
}
