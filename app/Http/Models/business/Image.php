<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Common;
use App\Http\Models\table\B_image;
use App\Http\Models\table\Linrun_image;
use App\Http\Models\table\Image_migration;
use App\Http\Models\table\Anjie_work;
class Image extends Model
{
    public function __construct()
    {
        parent::__construct();
        // $this->_b_image = new B_image();
        // $this->_linrun_image = new Linrun_image();
        // $this->_image_migration = new Image_migration();
        // $this->_anjie_work = new Anjie_work();
    }

    public function imagemigration()
    {
        ini_set('max_execution_time', '0');
        $getmigration = $this->_image_migration->getmigration('ID3');
        $where  = " where ID > ". $getmigration['value'];
        $limitout = " limit 500";
        $image = $this->_b_image->get($where, $limitout);
        foreach ($image as $key => $value) {
            try{
                $filename = iconv("UTF-8","GB2312//IGNORE",$value['Name'])  . '.jpg';
                $path = 'uploads/linrun/';   //文件上传的路径，基础路径为public
                $updateimage = $this->_image_migration->updateimage('ID3', $value['ID']);
                $isset = $this->_linrun_image->getinfobyId($value['ID']);
                if (!empty($isset)) {
                    // if (file_exists($path . $filename)) {
                        continue;
                    // }
                }
                $issetanjie_work = $this->_anjie_work->getinfoByRecid($value['RecID']);
                if (empty($issetanjie_work)) {
                    continue;
                }
                // $fileput = file_put_contents($path . $filename, $value['Data']);
                $this->_linrun_image->addimage($value['ID'], $value['RecID'], $value['Type'], $value['Name'], $value['Width'], $value['Height'], $value['Weights'], $value['IsDeleted']);
                $updatecounts = $this->_image_migration->updatecounts('ID3', $value['ID']);
            }catch (Exception $e){
                Log::info("事务操作失败，事务回滚！");
                var_dump($e->getMessage());
                $this->_pdo->rollBack();
                return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
            }
            
        }
    }
    public function filemigration()
    {
        ini_set('max_execution_time', '0');
        $getmigration = $this->_image_migration->getmigration('FILEID3');
        $where  = " where ID > ". $getmigration['value'] . " order by ID asc";
        $limitout = " limit 300";
        $image = $this->_linrun_image->getimage($where, $limitout);
        foreach ($image as $key => $value) {
            try{
                $filename = $value['ID'] .'_ID_'. iconv("UTF-8","GB2312//IGNORE",$value['Name'])  . '.jpg';
                $path = 'uploads/linrun/';   //文件上传的路径，基础路径为public
                $updateimage = $this->_image_migration->updateimage('FILEID3', $value['ID']);
                if (file_exists($path . $filename)) {
                    continue;
                }
                $data = $this->_b_image->getinfobyid($value['ID']);
                $fileput = file_put_contents($path . $filename, $data['Data']);
                $updatecounts = $this->_image_migration->updatecounts('FILEID3', $value['ID']);
            }catch (Exception $e){
                Log::info("事务操作失败，事务回滚！");
                var_dump($e->getMessage());
                $this->_pdo->rollBack();
                return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
            }
            
        }
    }
}

