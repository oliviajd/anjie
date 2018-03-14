<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class File_upload extends Model
{
    protected  $table='file_upload';
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
    public function getInfoByFiletoken($fileToken)
    {
      $sql = "select * from file_upload where file_token = ?";
      $rs = $this->_pdo->fetchOne($sql, array($fileToken));
      return $rs;
    }
    /*  
        通过file_token更新is_end字段
        @params  file_token      文件token 
    */
    public function updateIsEndByFiletoken($isend, $fileToken)
    {
      $sql = "update file_upload set is_end = ? where file_token = ?";
      $rs = $this->_pdo->execute($sql, array($isend, $fileToken));
      return $rs;
    }


    /*  
        通过file_token更新size字段
        @params  file_token      文件token 
    */
    public function updateSizeByFiletoken($size, $fileToken)
    {
      $sql = "update file_upload set size = ? where file_token = ?";
      $rs = $this->_pdo->execute($sql, array($size, $fileToken));
      return $rs;
    }

    /*  
        获取需要上传到又拍云的队列
    */
    public function getUploadsQueue()
    {
      $sql = "select * from file_upload where sync_to_upyun_status = '0' and sync_to_upyun = '1' order by id";
      $rs = $this->_pdo->fetchAll($sql);
      return $rs;
    }
    /*  
        插入file_upload表
    */
    public function setFileuploads($param)
    {
      $sql = "insert into file_upload (user_id, type, path, suffix, create_time, sync_to_upyun, file_token, token_expired_in) values(?, ?, ?, ?, ?, ?, ?, ?)";
      $rs = $this->_pdo->execute($sql, array($param['user_id'], $param['type'], $param['path'], $param['suffix'], $param['create_time'], $param['sync_to_upyun'], $param['file_token'], $param['token_expired_in']));
      $result = $param;
      $result['id'] = $this->_pdo->lastInsertId();
      return $result;
    }
    /*  
        插入file_upload表
    */
    public function setbankfiles($param)
    {
      $sql = "insert into file_upload (user_id, type, path, suffix, create_time, sync_to_upyun, file_token, token_expired_in, file_id) values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $rs = $this->_pdo->execute($sql, array($param['user_id'], $param['type'], $param['path'], $param['suffix'], $param['create_time'], $param['sync_to_upyun'], $param['file_token'], $param['token_expired_in'], $param['file_id']));
      $result = $param;
      $result['id'] = $this->_pdo->lastInsertId();
      return $result;
    }
    /*  
       根据id更新file_id
    */
    public function setFileIDById($fileId, $id)
    {
      $sql = "update file_upload set file_id = ? where id = ?";
      $rs = $this->_pdo->execute($sql , array($fileId, $id));
      return $rs;
    }
    /*  
       获取需要上传到银行的队列
    */
    public function getBankUploadsQueue()
    {
      $sql = "select * from file_upload where sync_to_bank = '1' and is_end = '1' and sync_to_bank_status = '0' order by id";
      $rs = $this->_pdo->fetchAll($sql);
      return $rs;
    }
    /*  
       更新银行的更新状态
    */
    public function setSynctoBankStatus($status, $fileId)
    {
      $sql = "update file_upload set sync_to_bank_status = ? where file_id = ?";
      $rs = $this->_pdo->execute($sql , array($status, $fileId));
      $sql = "update anjie_file set upload_status = ? WHERE file_id = ?";
      $res = $this->_pdo->execute($sql,[$status,$fileId]);
      return $rs && $res;
    }

    public function setUploadSize($fileId,$size){
        $sql = "update file_upload set last_upload_size = ?,last_upload_num=last_upload_num+1 WHERE file_id = ?";
        return $this->_pdo->execute($sql,[$size,$fileId]);
    }
    /*  
       更新又拍云的更新状态
    */
    public function setUpyunStatus($status, $id)
    {
      $sql = "update file_upload set sync_to_upyun_status = ? where id = ?";
      $rs = $this->_pdo->execute($sql, array($status, $id));
      return $rs;
    }

    public function getinfobyfileid($file_id)
    {
      $sql = "select * from file_upload where file_id = ?";
      $rs = $this->_pdo->fetchOne($sql, array($file_id));
      return $rs;
    }
}
