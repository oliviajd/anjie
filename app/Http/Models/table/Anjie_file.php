<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Anjie_file extends Model
{
    protected  $table='anjie_file';
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
        $sql = "select * from anjie_file " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        foreach ($rs as $key => $value) {
            if ($value['file_path'] !== '') {
                if(strpos($value['file_path'],'http') !== false || strpos($value['file_path'],'https') !== false){
                    // $limiter = 'uploads';
                    // $img = explode($limiter,$value['file_path']);
                    // $rs[$key]['file_path'] = $path.'/'.$limiter.$img[1];
                    $rs[$key]['file_path'] = $value['file_path'];
                }else{
                    $rs[$key]['file_path'] = $path .'/'. $value['file_path'];
                }
                if ($value['need_thumb'] == '1') {
                    $rs[$key]['thumb_file'] = $rs[$key]['file_path'];
                } else {
                    $rs[$key]['thumb_file'] = $path.'/'.$value['thumb_file'];
                }
            }
            if ($value['type'] == '1') {
                if (strpos($value['file_path'],'static02') == false) {
                    $rs[$key]['file_path'] = $rs[$key]['file_path'];
                    $rs[$key]['thumb_file'] = $rs[$key]['thumb_file'];
                }else {
                    $rs[$key]['file_path'] = $value['file_path'];
                    $rs[$key]['thumb_file'] = $value['file_path'] . '?x-oss-process=image/resize,m_lfit,h_100,w_100';
                }
            } else {
                $rs[$key]['file_path'] = env('ALIYUN_PATH') . $value['file_path'];
                $rs[$key]['thumb_file'] = env('ALIYUN_PATH') . $value['file_path'] . '?x-oss-process=image/resize,m_lfit,h_100,w_100';
            }
        }
        return $rs;
    }
    /**  
        获取图片列表
    **/
    public function getDetail2($where, $order='', $limit='')
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select *, anjie_file.id as imageid from anjie_file " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        foreach ($rs as $key => $value) {
            if ($value['file_path'] !== '') {
                if(strpos($value['file_path'],'http') !== false || strpos($value['file_path'],'https') !== false){
                    $limiter = 'uploads';
                    $img = explode($limiter,$value['file_path']);
                    $rs[$key]['file_path'] = $path.'/'.$limiter.$img[1];
                }else{
                    $rs[$key]['file_path'] = $path .'/'. $value['file_path'];
                }
                if ($value['need_thumb'] == '1') {
                    $rs[$key]['thumb_file'] = $rs[$key]['file_path'];
                } else {
                    $rs[$key]['thumb_file'] = $path.'/'.$value['thumb_file'];
                }
            }
            if ($value['type'] == '1') {
                $rs[$key]['file_path'] = $rs[$key]['file_path'];
                $rs[$key]['thumb_file'] = $rs[$key]['thumb_file'];
            } else {
                $rs[$key]['file_path'] = $value['file_path'];
                $rs[$key]['thumb_file'] = env('ALIYUN_PATH') . $value['file_path'] . '?x-oss-process=image/resize,m_lfit,h_100,w_100';
            }
        }
        return $rs;
    }
    //获取所有的信息
    public function getall($where, $order='', $limit='')
    {
        $sql = "select * from anjie_file " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }
    public function getcardapplications($work_id)
    {
        $sql = "select a.id as fid, a.*, b.* from anjie_file as a left join anjie_file_class as b on a.file_class_id = b.id where b.id = 19 and a.status = 1 and a.work_id=?";
        $rs = $this->_pdo->fetchAll($sql, array($work_id));
        return $rs;
    }
    public function getzhaohuibankimages($work_id)
    {
        $sql = "select a.id as fid, a.*, b.* from anjie_file as a left join anjie_file_class as b on a.file_class_id = b.id where b.is_zhaohui_bank =1 and a.status = 1 and a.work_id=?";
        $rs = $this->_pdo->fetchAll($sql, array($work_id));
        return $rs;
    }
    //添加图像
    public function addimage($param)
    {
        if (!isset($param['cover_image'])) {
            $param['cover_image'] = '';
        }
        $path = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $filepath = str_replace($path,'', $param['file_path']);
        $sql = "insert into anjie_file (work_id, file_class_id, file_type, file_type_name, file_path, status, add_userid, file_id, create_time, modify_time, cover_image) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($param['work_id'], $param['file_class_id'], $param['file_type'], $param['file_type_name'], $filepath, '1', $param['add_userid'], $param['file_id'], time(), time(), $param['cover_image']));
        if ($rs !== false) {
            $param['id'] = $this->_pdo->lastInsertId();
            $rs = $this->issetfile($param['id']);
        }
        return $rs;
    }
    //删除图像
    public function deleteimage($param)
    {
        $fileid = explode(',', $param['file_id']);
        $fileidstring = "'".implode("','", $fileid)."'";
        $sql = "update anjie_file set status = ?, delete_userid = ?, create_time =?, modify_time =? where id in(".$fileidstring.")";
        $rs = $this->_pdo->execute($sql, array('2', $param['delete_userid'], time(), time()));
        return $rs;
    }
    //根据file_id判断该文件是否删除
    public function issetfile($id)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from anjie_file where id = ? and status = 1";
        $rs = $this->_pdo->fetchOne($sql, array($id));
        if(strpos($rs['file_path'],'http') !== false || strpos($rs['file_path'],'https') !== false){
            $rs['file_path'] = $rs['file_path'];
        } else {
            $rs['file_path'] = $path . $rs['file_path'];
        }
        
        return $rs;
    }
    
    
    //通过工作id和file_class_id
    public function getInfoByWorkidAndFileclassid($param)
    {
        $path = 'http://' . $_SERVER['HTTP_HOST'];
        $sql = "select * from anjie_file where file_class_id = ? and work_id = ? and status=1";
        $rs = $this->_pdo->fetchAll($sql, array($param['file_class_id'], $param['work_id']));
        if (!empty($rs)) {
            foreach ($rs as $key => $value) {
                if ($value['file_path'] !== '') {
                    if(strpos($rs[$key]['file_path'],'http') !== false || strpos($rs[$key]['file_path'],'https') !== false){
                        $rs[$key]['file_path'] = $value['file_path'];
                    } else {
                        $rs[$key]['file_path'] = $path .'/'. $value['file_path'];
                    }
                    
                }
            }
            
        }
        return $rs;
    }
    //通过工作id和file_class_id计算总共有多少张
    public function countByWorkidAndFileclassid($param)
    {
        $sql = "select count(*) from anjie_file where file_class_id = ? and work_id = ? and status=1";
        $rs = $this->_pdo->fetchOne($sql, array($param['file_class_id'], $param['work_id']));
        return $rs;
    }
    public function getDetailByWorkid($work_id)
    {
        $sql = "select * from anjie_file where work_id = ? and file_type = 1 and status = 1";
        $rs = $this->_pdo->fetchAll($sql, array($work_id));
        return $rs;
    }

    public function listthumbfile()
    {
        $sql = "select * from anjie_file where need_thumb = 1 and file_type=1 and status=1 and type =1 limit 10";
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function updatefilethumb($param, $new_filepath)
    {
        $sql = "update anjie_file set thumb_file = ?, need_thumb = 2 where id = ?";
        $rs = $this->_pdo->execute($sql, array($new_filepath, $param['id']));
        return $rs;
    }
    public function updatefilefail($param)
    {
        $sql = "update anjie_file set need_thumb = 4 where id = ?";
        $rs = $this->_pdo->execute($sql, array($param['id']));
        return $rs;
    }
    public function addimage2($param)
    {
        $sql = "insert into anjie_file (work_id, file_class_id, file_type, file_type_name, file_path, status, add_userid, file_id, create_time, modify_time, type) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($param['work_id'], '1', '1', 'image', $param['file_path'], '1', '0', $param['file_id'], time(), time(), '2'));
        if ($rs !== false) {
            $param['id'] = $this->_pdo->lastInsertId();
            $rs = $this->issetfile($param['id']);
        }
        return $rs;
    }
}
