<?php

namespace App\Http\Models\business;
require_once($_SERVER['DOCUMENT_ROOT']."/php-sdk/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php-sdk/src/Upyun/Config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php-sdk/src/Upyun/Upyun.php");

use Illuminate\Database\Eloquent\Model;
use League\Flysystem\Exception;
use Upyun\Upyun;
use Upyun\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Models\table\File_upload;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\table\Anjie_file_class;
use App\Http\Models\table\Anjie_file;
use App\Http\Models\table\Jcr_file;
use App\Http\Models\table\Anjie_bank_mail;
use App\Http\Models\table\Anjie_work;
use App\Http\Models\common\Word;
use Mail;

class File extends Model
{
  protected $_file_upload = null;
  protected $_anjie_file_class = null;
  protected $_anjie_file = null;
  protected $_anjie_bank_mail = null;

  public function __construct()
  {
    parent::__construct();
    $this->_file_upload = new File_upload();
    $this->_anjie_file_class = new Anjie_file_class();
    $this->_anjie_file = new Anjie_file();
    $this->_jcr_file = new Jcr_file();
    $this->_anjie_bank_mail = new Anjie_bank_mail();
    $this->_anjie_work = new Anjie_work();
    $this->_common = new Common();

  }
  //暂时只支持image和vedio的上传
  static public $_typeallow = array(
    'image' => 1,
    'video' => 1,
  );
  //允许传入的图片格式 对应为0的是暂不支持的图片格式
  static public $_imagesallow = array(
    'jpg' => 1, 
    'bmp' => 1, 
    'png' => 1, 
    'tiff'=> 1, 
    'gif' => 1, 
    'pcx' => 1,
    'tga' => 1, 
    'exif'=> 1, 
    'psd' => 1, 
    'pcd' => 1, 
    'raw' => 1, 
    'wmf' => 1,
    'JPG' => 1, 
    'BMP' => 1, 
    'PNG' => 1, 
    'TIFF'=> 1, 
    'GIF' => 1, 
    'PCX' => 1,
    'TGA' => 1, 
    'EXIF'=> 1, 
    'PSD' => 1, 
    'PCD' => 1, 
    'RAW' => 1, 
    'WMF' => 1,
    'fpx' => 0,
    'svg' => 0,
    'cdr' => 0,
    'dxf' => 0,
    'ufo' => 0,
    'eps' => 0,
    'ai'  => 0,
  );
  //列出风控审核所有图片类别
  public function listimagetype($type='2')
  {
    //列出所有图片类别
    if ($type == '2') {
      $rs = $this->_anjie_file_class->listAppImagetype();
    } else {
    $rs = $this->_anjie_file_class->listimagetype();
    }
    return $rs;
  }

  //添加图像逻辑
  public function addimage($param)
  {
    // if ($param['type'] == '2' || $param['type'] == '3') {
    //   $issetfile = $this->_anjie_file->getInfoByWorkidAndFileclassid($param);
    //   if (!empty($issetfile)) {
    //     return false;
    //   }
    // }
    //添加图像逻辑
    $rs = $this->_anjie_file->addimage($param);
    return $rs;
  }
  //添加图像
  public function addworkimages($params)
  {
    $imgs = $params['imgs'];
    if (!is_array($imgs)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
    }
    $imgs = $params['imgs'];
    if (!is_array($imgs)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
    }
    if (!isset($imgs['add'])) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '所传数组必须包含新增');
    }
    if (!is_array($imgs['add'])) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '新增图片必须为数组');
    }
    foreach ($imgs['add'] as $key => $value) {
      if (!isset($value['source_lists']) || !isset($value['source_type'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
      }
      if (!is_array($value['source_lists'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
      }
      foreach ($value['source_lists'] as $k => $v) {
        if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
        }
      }
    }
    $addimgs = $this->addimages($params['imgs']['add'], $params['work_id'], $params['user_id']);  //添加图片
    if ($addimgs !== true) {
      return $addimgs;
    }
    return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功 
  }
  //传入imgs和work_id插入图片
  public function addimages($imgs, $work_id, $user_id)
  {
    $workinfo = $this->_anjie_work->getdetailByid($work_id);
    $file_type = $this->_anjie_file_class->listimagetype();  //文件类型
    $file_types = array();
    foreach ($file_type as $key => $value) {
      if ($value['id'] == '19' && $workinfo['loan_bank'] == '04') {
        $value['min_length'] = '1';
      }
      $file_types[$value['id']] = $value;
    }
    if (!is_array($imgs)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片数据传入格式不对');
    }
    foreach ($imgs as $key => $value) {
      if (!isset($value['source_lists']) || !isset($value['source_type'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, 'imgs必须有图片类别和图片列表');
      }
      if (!is_array($value['source_lists'])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表的传入格式不对');
      }
      foreach ($value['source_lists'] as $k => $v) {
        if (!isset($v['org']) || !isset($v['alt']) || !isset($v['src'])) {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, '图片列表内的格式不对');
        }
      }
    }
    foreach ($imgs as $key => $value) {
      $keytrim = ltrim($key,'file_');
      if (!isset($file_types[$keytrim])) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '该文件类型不存在');                //该文件类型不存在
      }
      $param['file_class_id'] = $keytrim;
      $param['work_id'] = $work_id;
      $curren_length = $this->_anjie_file->countByWorkidAndFileclassid($param);
      $max_length = intval($file_types[$keytrim]['max_length']);  //该文件类型的最大上传数量
      $min_length = intval($file_types[$keytrim]['min_length']);  //该文件类型的最大上传数量
      $count = count($imgs[$key]['source_lists']) + intval($curren_length['count(*)']);    //该文件类型的上传数量
      if ($count > $max_length) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量超过最大上传张数');                //文件数量超过最大上传张数
      }
      if ($count < $min_length) {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '文件数量小于最小上传张数');                  //文件数量小于最小上传张数
      }
    }
    $imagers= true;
    foreach ($imgs as $key => $value) {
      $keytrim = ltrim($key,'file_');
      foreach ($value['source_lists'] as $k => $v) {
        $param['add_userid'] = $user_id; //添加者的userid
        $param['file_type_name'] = $value['source_type'];
        $param['file_type'] =  ($param['file_type_name'] == 'image') ? '1' : '2';
        $param['file_class_id'] = $keytrim;
        $param['file_path'] = $v['org'];
        $param['file_id'] = $v['alt'];
        $param['work_id'] = $work_id;
        $imagers = $this->_anjie_file->addimage($param);  //添加图像逻辑
      }
    }
    if ($imagers == false) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '添加图片失败!'); 
    }
    return true;
  }
  //删除图像逻辑
  public function deleteimage($param)
  {
    $issetfile = $this->_anjie_file->issetfile($param['file_id']);
    if (empty($issetfile)) {          //如果该文件已经被删除或不存在，则返回false
      return false;
    }
    //删除图像逻辑
    $rs = $this->_anjie_file->deleteimage($param);
    if ($rs !== false) {
      return $param;
    }
    return $rs;
  }
  public function thumbfile()
  {
    $file = $this->_anjie_file->listthumbfile();
    foreach ($file as $key => $value) {
      $filepath = FCPATH . $value['file_path'];
      $file_info = @getimagesize($filepath);
      if (!$file_info) {
          // return $this->_common->output(false, Constant::ERR_FAILED_NO, '取图片信息失败');    //取图片信息失败
          $this->_anjie_file->updatefilefail($value);
          continue;
      }
      $arr = explode('/', $value['file_path']);
      $arr['1'] = 'thumb';
      $path = $arr['0']. '/'. $arr['1'] . '/' . $arr['2'];
      if (!file_exists(FCPATH . $path)) {
          mkdir(FCPATH . $path, 0777, true);
      }
      $new_filepath = FCPATH . $path .'/'. $arr['3'];
      $fileput = file_put_contents($new_filepath, '', 0777);
      $this->image_resize($filepath, $new_filepath, $max = 120);
      $new_file_info = getimagesize($new_filepath);
      $this->_anjie_file->updatefilethumb($value, $path . '/' . $arr['3']);
    }
    return $this->_common->output(true, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功 
  }
  
  //图片的等比缩放，指定最大边 或者指定最大宽 或最大高
    public function image_resize($filepath, $outputpath, $max = 0, $w = 0, $h = 0) 
    {
        //取得源图片的宽度和高度和类别
        $src = getimagesize($filepath);
        $old_w = $src[0];
        $old_h = $src[1];
        $type = $src[2];
        switch ($type) {
            case 1://gif
                $img = imagecreatefromgif($filepath);
                break;
            case 2://jpg
                $img = imagecreatefromjpeg($filepath);
                break;
            case 3://png
                $img = imagecreatefrompng($filepath);
                break;
            default:
                return false;
        }

        $new_w = 0;
        $new_h = 0;

        if ($max > 0) {
            //根据最大值，算出另一个边的长度，得到缩放后的图片宽度和高度
            if ($old_w > $old_h) {
                $new_w = $max;
                $new_h = $old_h * ($max / $old_w);
            } else {
                $new_h = $max;
                $new_w = $old_w * ($max / $old_h);
            }
        } else if ($w > 0) {
            //根据最大宽，算出另一个边的长度，得到缩放后的图片宽度和高度
            $new_w = $w;
            $new_h = $old_h * ($max / $old_w);
        } else if ($h > 0) {
            //根据最大高，算出另一个边的长度，得到缩放后的图片宽度和高度
            $new_h = $h;
            $new_w = $old_w * ($max / $old_h);
        }

        //声明一个$w宽，$h高的真彩图片资源
        $image = imagecreatetruecolor($new_w, $new_h);

        //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
        imagecopyresampled($image, $img, 0, 0, 0, 0, $new_w, $new_h, $old_w, $old_h);

        imagepng($image, $outputpath);

        //销毁资源
        imagedestroy($image);
  }
  public function listimages($param)
  { 
    $order = " order by id desc";
    $where = " where work_id = " . $param['work_id'] . ' and status =1'; 
    $rs = $this->_anjie_file->getDetail($where, $order);
    return $rs;
  }
  public function listjcrimages($param)
  { 
    $order = " order by id desc";
    $where = " where verify_id = " . $param['verify_id']; 
    $rs = $this->_jcr_file->getDetail($where, $order);
    return $rs;
  }
  public function listdealerimages($param)
  {
    $order = " order by id desc";
    $where = " where verify_id = " . $param['verify_id']. ' and file_class_id =10';
    $rs = $this->_jcr_file->getDetail($where, $order,'',1);
    return $rs;
  }
  public function packagefile($param, $user_id)
  {
    $workinfo = $this->_anjie_work->getdetailByid($param['work_id']);
    if (empty($workinfo)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '找不到申请件');
    } else {
      $data['user_id'] = $user_id;
      $data['type'] = 'package';
      $data['suffix'] = 'zip';
      $data['path'] = 'uploads/' . $data['type'] . '/' . date('Ymd') . '/';
      $data['file_token'] = md5($param['work_id']) . microtime() . rand(10000, 99999);
      $data['token_expired_in'] = time() + 3600 * 4 * 7;
      $data['sync_to_upyun'] = '0';
      if (!file_exists(FCPATH . $data['path'])) {
          mkdir(FCPATH . $data['path'], 0777, true);
      }
      $addfile = $this->add($data);
      $package = $this->_common->object_array(json_decode($addfile));
      if ($package['error_no'] !== 200) {
          return $addfile;
      }
      $file_type = $this->_anjie_file_class->listimagetype();  //文件类型
      $file_types = array();
      foreach ($file_type as $key => $value) {
        $file_types[$value['id']] = $value;
      }
      $filename = $data['path'] . $package['result']['fid'] . '.' . $data['suffix'];
      $param['file_package'] = $package['result']['url'];
      $filearr = $this->_anjie_file->getDetailByWorkid($param['work_id']);
      $zip = new \ZipArchive();
      $res = $zip->open(public_path($filename), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
      if ($res) {
        $downarr = array();
        foreach ($filearr as $key => $value) {
          if(strpos($value['file_path'],'http') !== false || strpos($value['file_path'],'https') !== false){
            $downloads = $this->UploadVideo($value['file_path']);
            $value['file_path'] = $downloads['file_path'];
            $downarr[] = $downloads['file_path'];
          }
          if (file_exists(public_path($value['file_path']))) {
            $v = explode("/", $value['file_path']);
            $end  = end($v);
            $zip->addFile(public_path($value['file_path']), $file_types[$value['file_class_id']]['file_name'].'_'. $end);
            // $zip->addFile(public_path($value['file_path']), 'image/' . $end);    //创建文件夹的时候就直接加上文件夹名就好
          }
        }
        $zip->close(); 
        $updatepackage = $this->_anjie_work->updatepackage($param);
        foreach ($downarr as $key => $value) {
          if (file_exists(public_path($value))) {
            unlink(public_path($value));
          }
        }
        return $this->_common->output($param['file_package'], Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
      } else {
        return $this->_common->output(false, Constant::ERR_FAILED_NO, '创建文件失败');
      }
    }
  }
//删除打包下载的资料
  public function unlinkpackage()
  {
    $data['path'] = 'uploads/package/';
    $this->unlinkbeforetoday($data['path']);
    // $today = date('Ymd');
    // $data['path'] = 'uploads/package/';
    // $dirs = scandir($data['path']);
    // foreach ($dirs as $key => $value) {
    //   if ($value!='.'&& $value!='..' && $value<$today) {
    //     $this->do_rmdir($data['path'] . $value . '/');
    //   }
    // }
  }
  //删除影像资料
  public function unlinkvideoimage()
  {
    // $today = date('Ymd');
    $data['path'] = 'uploads/video/';
    $this->unlinkbeforetoday($data['path']);
    $data['path'] = 'uploads/image/';
    $this->unlinkbeforetoday($data['path']);
    // $dirs = scandir($data['path']);
    // foreach ($dirs as $key => $value) {
    //   if ($value!='.'&& $value!='..' && $value<$today) {
    //     $this->do_rmdir($data['path'] . $value . '/');
    //   }
    // }
  }
  //删除今天之前的文件夹
  public function unlinkbeforetoday($path)
  {
    $today = date('Ymd');
    $data['path'] = $path;
    $dirs = scandir($data['path']);
    foreach ($dirs as $key => $value) {
      if ($value!='.'&& $value!='..' && $value<$today) {
        $this->do_rmdir($data['path'] . $value . '/');
      }
    }
  }
  /**
 * 清空/删除 文件夹
 * @param string $dirname 文件夹路径
 * @param bool $self 是否删除当前文件夹
 * @return bool
 */
    public function do_rmdir($dirname, $self = true) {
      if (!file_exists($dirname)) {
          return false;
      }
      if (is_file($dirname) || is_link($dirname)) {
          return unlink($dirname);
      }
      $dir = dir($dirname);
      if ($dir) {
          while (false !== $entry = $dir->read()) {
              if ($entry == '.' || $entry == '..') {
                  continue;
              }
              $this->do_rmdir($dirname . '/' . $entry);
          }
      }
      $dir->close();
      $self && rmdir($dirname);
  }
  //word生成
  public function wordcreate($params, $user_id)
  {
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    if (empty($workinfo)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请件不存在');
    }
    $where = " where work_id = " . $params['work_id'] . ' and status = 1'. " and file_class_id in ('1', '2')";
    $files = $this->_anjie_file->getall($where);
    // if (PHP_OS == 'Linux') {
    //   $wordname = $workinfo['customer_name']. date('Ymd') . '.doc';
    // } else {
    //   $wordname = iconv("UTF-8","GB2312//IGNORE",$workinfo['customer_name']). date('Ymd') . '.docx';
    // }
    $wordnameutf8 = $workinfo['customer_name']. date('Ymd') . '.docx';
    $path = 'worddownloads/' . date('Ymd'). '/';
    $phpWord =  new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection();
    foreach ($files as $key => $value) {
      if (file_exists($value['file_path']) || strpos($value['file_path'],'http') !== false) {
        $section->addImage($value['file_path'],array('width'=>550));
      }
    }
    $writers = array('Word2007' => '=docx', 'ODText' => 'odt', 'RTF' => 'rtf', 'HTML' => 'html');
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    if(!file_exists($path)){
        mkdir($path, 0777, true);
    }
    $objWriter->save($path. $wordnameutf8);
    $host = 'http://' . str_replace('\\','/',$_SERVER["HTTP_HOST"]);
    $params['word_url'] = $host . '/' . $path . $wordnameutf8;
    $updateword = $this->_anjie_work->updateword($params);
    return $this->_common->output($params, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
  }
  //word生成
  public function bankwordcreate($params, $user_id)
  {
    $workinfo = $this->_anjie_work->getdetailByid($params['work_id']);
    if (empty($workinfo)) {
      return $this->_common->output(false, Constant::ERR_FAILED_NO, '申请件不存在');
    }
    $where = " where work_id = " . $params['work_id'] . ' and status = 1'. " and file_class_id in ('1', '2')";
    $files = $this->_anjie_file->getall($where);
    // if (PHP_OS == 'Linux') {
    //   $wordname = $workinfo['customer_name']. date('Ymd') . '.doc';
    // } else {
    //   $wordname = iconv("UTF-8","GB2312//IGNORE",$workinfo['customer_name']). date('Ymd') . '.docx';
    // }
    $wordnameutf8 = date('YmdHis') . rand(10,99) . '.doc';
    $path = 'worddownloads/' . date('Ymd'). '/';
    $phpWord =  new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection();
    foreach ($files as $key => $value) {
      if (file_exists($value['file_path']) || strpos($value['file_path'],'http') !== false) {
        $section->addImage($value['file_path'],array('width'=>550));
      }
    }
    $writers = array('Word2007' => '=docx', 'ODText' => 'odt', 'RTF' => 'rtf', 'HTML' => 'html');
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    if(!file_exists($path)){
        mkdir($path, 0777, true);
    }
    $objWriter->save($path. $wordnameutf8);
    $host = 'http://' . str_replace('\\','/',$_SERVER["HTTP_HOST"]);
    $params['word_url'] = $host . '/' . $path . $wordnameutf8;
    $params['word_path'] = $path . $wordnameutf8;
    $params['filename'] = $wordnameutf8;
    $this->_anjie_work->updateword($params);
    return $params;
  }
  /**
     * 文件下载
     * @param $url
     */
    public function UploadVideo($url)
    {
        $day = date('Ymd',time());
        $path = 'downloads/'.$day.'/';
        $uploadPath = FCPATH . $path;//服务器上传视频的目录
        if(!file_exists($uploadPath)){
            Log::info('文件夹:'.$uploadPath.'不存在，开始创建');
            if(!mkdir($uploadPath, 0777, true)){
                Log::info('文件夹:'.$uploadPath.'创建失败');
            }
        }
        $fileinfo = explode('/',$url);
        $cnt = count($fileinfo);
        // $name = md5($fileinfo[$cnt-2]);//服务器文件名
        $sufix = explode('.',$fileinfo[$cnt-1]);//后缀名
        $name = md5($sufix[0]);
        $file = $name.'.'.$sufix[1];//文件名包含后缀
        $filename = $uploadPath.$file;
        Log::info('开始下载文件:'.$url);
        $this->downfile($filename,$url);
        Log::info('文件下载完成');
        $param['user_id'] = '99999';
        $param['type'] = '2';
        $param['path'] = $path;
        $param['suffix'] = $sufix[1];
        $param['create_time'] = time();
        $param['sync_to_upyun'] ='1';
        $param['file_token'] = '';
        $param['token_expired_in'] = '';
        $param['file_id'] = $name;
        $param['file_path'] = $param['path'] . $file;
        return $param;
    }
    /**
     * 下载文件
     * @param $filename    目标路径文件地址
     * @param $fileurl  网上的图片地址
     */
    public function downfile($filename,$fileurl)
    {
        if (!file_exists($filename)) {
          file_put_contents($filename, '');
        }
        $file  =  fopen($fileurl, "rb");
        $chunkSize = 1024 * 1024;
        set_time_limit(0);
        while (!feof($file)) {
            file_put_contents($filename,fread($file, $chunkSize),FILE_APPEND);
        }
        fclose($file);
    }

  /*  
      通过file_token获取信息
      @params  file_token      文件token 
  */
  public function getInfoByFiletoken($fileToken)
  {
    $rs = $this->_file_upload->getInfoByFiletoken($fileToken);
    return $rs;
  }
  /*  
      通过file_token更新is_end字段
      @params  file_token      文件token 
  */
  public function updateIsEndByFiletoken($isend, $fileToken)
  {
    $rs = $this->_file_upload->updateIsEndByFiletoken($isend, $fileToken);
    return $rs;
  }
  /*  
      通过file_token更新size字段
      @params  file_token      文件token 
  */
  public function updateSizeByFiletoken($size, $fileToken)
  {
    $rs = $this->_file_upload->updateSizeByFiletoken($size, $fileToken);
    return $rs;
  }
  /*  
      获取需要上传到又拍云的队列
  */
  public function getUpyunUploadsQueue()
  {
    $rs = $this->_file_upload->getUploadsQueue();
    return $rs;
  }
   /*  @params: $url            被curl请求的URL
                $start          开始字节
                $end            结束字节
       @return  $output         请求结果
   */
    public function urlRequest($url, $start, $end)
    {
    	$urlmd5 = md5($url);
    	$header = array(
	        'Range:bytes='.$start.'-'.$end,
	    );
	    $result['filename'] = $urlmd5.$start.".jpg";
	    $result['output'] = $this->curl_request($url, $header);
      return $result;
    }

   /*  @params: $url            被curl请求的URL
                $header         header数组
       @return  $output         请求结果
   */
    public function curl_request($url, $header=array())
    {
    	$curl = curl_init($url);      
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);  
        $output = curl_exec($curl);   
        curl_close($curl);
        return $output;
    }

   /*  @params: $url            需要取到header得URL
       @return: $headerarr      经过分割后的header数组
   */
    public function getHeaders($url)
    {
    	$headers = get_headers($url);
    	$headerarr = array();
	    foreach ($headers as $key => $value) {
	    	$arr = explode(':', $value);
	    	$headerarr[$arr['0']] = isset($arr['1']) ? $arr['1'] : '';
	    }
	    return $headerarr;
    }

    public function handleUploadQueue($uploadsQueue)
    {
       // 创建实例
        $bucketConfig = new Config(env('UPYUN_CLOUD_NAME'), env('UPYUN_OPERATOR_NAME'), env('UPYUN_OPERATOR_PASSWD'));  
                                       //空间名、操作员名、操作员密码
        $client = new Upyun($bucketConfig);
        foreach ($uploadsQueue as $key => $value) {
            // 读文件
          try{
            $filename = $value['path'] . $value['file_id'] . '.' . $value['suffix'];
            if (!is_file($filename)) {
              $update = $this->_file_upload->setUpyunStatus('-1', $value['id']);
              continue;
            }
            $file = fopen($filename, 'r');
            // 上传文件
            $res = $client->pointWrite($value, $file);
            $update = $this->_file_upload->setUpyunStatus('1', $value['id']);
            echo $filename . 'success';
          } catch(\Exception $e) { 
              if ($e->getCode()) {
                echo $e->getMessage();
                Log::uploads('uploadService:fileid:' . $value['fiel_id']. $e->getMessage());
                $update = $this->_file_upload->setUpyunStatus('-2', $value['id']);
                continue;
              }
          }
          
        }
    }

    public function getBankUploadsQueue()
    {
      $rs = $this->_file_upload->getBankUploadsQueue();
      return $rs;
    }
    /**
     * 文件下载
     * @param $url
     */
    public function UploadbankVideo($params)
    {
        $arr = parse_url($params['file_path']);
        $fileinfo = explode('/',$arr['path']);
        $rs['file_path'] = $arr['path'];
        $cnt = count($fileinfo);
        $rs['path'] = str_replace($fileinfo[$cnt-1], '', $arr['path']);
        $rs['path'] = str_replace('/anjie/', '', $rs['path']);
        $suffix = explode('.',$fileinfo[$cnt-1]);//后缀名
        $rs['file_id'] = $suffix[0];
        $rs['suffix'] = $suffix[1];
        $filename = FCPATH . $rs['path'] . $fileinfo[$cnt-1];
        if(!file_exists(FCPATH . $rs['path'])){
            if(!mkdir(FCPATH . $rs['path'], 0777, true)){
            }
        }
        $this->downfile($filename,$params['file_path']);
        $rs['user_id'] = '99999';
        $rs['type'] = $params['file_type_name'];
        $rs['create_time'] = time();
        $rs['sync_to_upyun'] ='1';
        $rs['file_token'] = md5($rs['file_id']) . microtime() . rand(10000, 99999);
        $rs['token_expired_in'] = time() + 3600 * 4 * 7;
        $file = $this->_file_upload->getinfobyfileid($rs['file_id']);
        if (!empty($file)) {
          return $file;
        }
        $result = $this->_file_upload->setbankfiles($rs);
        return $result;
    }
//上传到银行服务器，记得保存错误
    public function uploadsToBankQueue($uploadsQueue)
    {
        if(!empty($uploadsQueue)){
            Log::info("[".date('Y-m-d H:i:s',time())."]本次资料操作数据:".json_encode($uploadsQueue));
            $publicpath = str_replace('\\','/',public_path());
            //上传目标服务器的地址暂时是写死了的，也可以直接放在.env文件中，可以考虑专门为这个项目写一个application.ini文件
            $path = env('IFCAR_BANK_UPLOAD_PATH');
            foreach ($uploadsQueue as $key => $value) {
                $allowimage = File::$_imagesallow;
                if ($value['type'] == 'image' && isset($allowimage[$value['suffix']]) && $allowimage[$value['suffix']] == 1) {
                    $suffix = 'jpg';
                } elseif($value['type'] == 'image' && (!isset($allowimage[$value['suffix']]) || $allowimage[$value['suffix']] == 0)) {
                    $rs = $this->_file_upload->setSynctoBankStatus(2, $value['file_id']);//不支持的图片格式，直接上传失败
                    if(!$rs){
                        return false;
                    }
                    Log::info("上传到银行服务器时格式不支持".$allowimage[$value['suffix']]);
                    continue;
                } else {
                    $suffix = $value['suffix'];
                }
                $filename = $publicpath . '/' . $value['path'] . $value['file_id'] . '.' . $value['suffix'];
                if (!file_exists($filename)) {
                    $rs = $this->_file_upload->setSynctoBankStatus(2, $value['file_id']);//不支持的图片格式，直接上传失败
                    if(!$rs){
                        return false;
                    }
                    Log::info("上传到银行服务器时未发现本地文件:".$filename);
                    continue;
                }
                $little = env('UPLOAD_BANK_LIMIT_SIZE');  //传送单位1M
                //字节需要转换成KB
                $size = filesize($filename);
                $num = ceil($size/$little);
                $start = $value['last_upload_size'];
                $num = $num-$value['last_upload_num'];
                Log::info('========================开始处理'.$value['file_id'].'文件========================');
                $isend = 0;
                for ($i=1; $i <=$num; $i++) {
                    if ($i == $num) {
                        $little = $size - $start;
                        $isend = 1;
                    }
                    Log::info('开始获取文件['.$filename.']的'.$start.'字节到'.$little.'字节的数据！');
                    $getdata = file_get_contents($filename,  NULL, NULL, intval($start), intval($little));
                    Log::info('文件['.$filename.']的'.$start.'字节到'.$little.'字节的数据获取成功！');
                    $offset = $start;
                    Log::info('开始链接到SFTP服务器['.env('SFTP_SERVER').']');
                    $resConnection = ssh2_connect(env('SFTP_SERVER'), env('SFTP_SERVER_PORT'));
                    Log::info('SFTP服务器链接完成，开始验证用户名和密码['.env('SFTP_SERVER_USERNAME').':'.env('SFTP_SERVER_PASSWORD').']');
                    //connect to server
                    if(ssh2_auth_password($resConnection, env('SFTP_SERVER_USERNAME'), env('SFTP_SERVER_PASSWORD'))){
                        //Initialize SFTP subsystem
                        Log::info('SFTP用户名密码验证成功！');
                        $resSFTP = ssh2_sftp($resConnection);
                        if (!file_exists("ssh2.sftp://{$resSFTP}/" . $path . $value['path'])) {
                          mkdir("ssh2.sftp://{$resSFTP}/".$path.$value['path'],0777,true);
                          Log::info('创建文件夹['.$path.$value['path'].']成功!');
                        }
                        $file  = $value['path'] . $value['file_id'] . '.' . $suffix;

                        if (!is_file("ssh2.sftp://{$resSFTP}/" . $path . $file)) {
                            Log::info('创建的文件夹为:'.$path.$value['path'].'写入文件的地址为:'.$path.$file);
                            file_put_contents("ssh2.sftp://{$resSFTP}/" . $path . $file, '');
                            Log::info('初始化完成');
                        }
                        Log::info('打开文件['.$file.']');
                        $resFile = fopen("ssh2.sftp://{$resSFTP}/".$path.$file, 'rb+');
                        fseek($resFile, $offset);
                        fwrite($resFile, $getdata);
                        fclose($resFile);
                        $start = $start + $little;
                        if ($isend == 1) {
                            $updatestatus = $this->_file_upload->setSynctoBankStatus($isend, $value['file_id']);
                            if(!$updatestatus){
                                Log::info("文件id=[".$value['file_id']."]的文件上传成功，更新file_upload表状态失败！");
                                return false;
                            }else{
                                Log::info("文件id=[".$value['file_id']."]的文件上传成功，更新file_upload表状态成功");
                            }
                        }else{
                            $rs = $this->_file_upload->setUploadSize($value['file_id'], $start);
                            if(!$rs){
                                Log::info("文件id=[".$value['file_id']."]的文件第[".$i."]分段上传成功，file_upload表字段更新失败！");
                                return false;
                            }else{
                                Log::info("文件id=[".$value['file_id']."]的文件第[".$i."]分段上传成功，file_upload表字段更新成功！");
                            }
                        }
                    } else {
                        Log::info("验证ssh2服务器失败。。。。。。。");
                    }
                }
                Log::info('========================结束处理'.$value['file_id'].'文件========================');
            }
            return true;
        }
    }


    public function _uploadFileContinue($start,$filename,$data,$resSFTPth,$iscontinue=true){
        $little = env('UPLOAD_BANK_LIMIT_SIZE');  //传送单位1M
        $size = filesize($filename);
        $num = ceil($size/$little);
        $isend = 0;
        for ($i=0; $i <$num; $i++) {
            if ($i == $num-1) {
                $little = $size - ($num-1) * $little;
                $isend = 1;
            }
            $getdata = file_get_contents($filename,  NULL, NULL, intval($start), intval($little));
            $offset = $start;
            if (!file_exists($resSFTPth . $data['path'])) {
                mkdir($resSFTPth . $data['path'], 0777, true);
            }
            $file  = $data['path'] . $data['file_id'] . '.' . $data['suffix'];
            if(!$iscontinue && !is_file($resSFTPth . $file)){
                $re = file_put_contents($resSFTPth . $file, '');
            }
            $resFile = fopen($resSFTPth . $file, 'rb+');
            fseek($resFile, $offset);
            fwrite($resFile, $getdata);
            fclose($resFile);
            $start = $start + $little;
            if ($isend == 1) {
                $this->_file_upload->setSynctoBankStatus($isend, $data['file_id']);
                $this->_file_upload->delContinue($data['file_id']);
            }else{
                //记录本次上传的进度下次续传
                $this->_file_upload->setContinueStart($start,$data['file_id']);
            }
        }
    }

    public function create_file_id($id)
    {
      $sn_left = microtime(1) . $_SERVER['SERVER_ADDR'];
        if ($id > 1000) {
            //方法1,每秒不超过100w的并发数,
            $sn_right = str_pad($id % 1000000, 6, '0', STR_PAD_LEFT);
        } else {
            //方法2,每毫秒不超过1000个并发
            $str = str_pad(microtime(1) * 1000 % 1000, 3, '0', STR_PAD_LEFT);
            $sn_right = $str . str_pad($id % 1000, 3, '0', STR_PAD_LEFT);
        }
        return md5($sn_left . $sn_right);
    }
    /**
     * 添加这条记录到file_upload表中，以及添加新文件
     * @param user_id               用户id
     * @param int type              传入文件类型
     * @param int path              文件上传的路径，基础路径为public
     * @param int fid               文件id
     * @param int file_token        文件token
     * @param int size              文件当前大小
     * @param int suffix            文件后缀
     * @return type                 文件类型
     * @return url                  访问地址
     */
    public function add($data)
    {
        $param['user_id'] = intval($data['user_id']);    //用户id
        $param['type'] = trim($data['type']);            //传入文件类型
        $param['path'] = trim($data['path']);            //文件上传的路径，基础路径为public
        $param['suffix'] = trim($data['suffix']);        //文件的后缀名
        if ($param['suffix'] == '') {
          $param['suffix'] = 'jpg';
        }
        if ($param['type'] == 'image') {
          $param['suffix'] = 'jpg';
        }
        $param['create_time'] = time();
        $param['sync_to_upyun'] = in_array(intval($data['sync_to_upyun']), array(1, 2)) ? intval($data['sync_to_upyun']) : 2;  
        //是否传到又拍云，0为不传，1为传
        $param['file_token'] = trim($data['file_token']);  //file_token
        $param['token_expired_in'] = $data['token_expired_in'];  //file_token的有效截止时间的时间戳
        $result = $this->_file_upload->setFileuploads($param);   //插入file_upload表
        $result['fid'] = $this->create_file_id($result['id']);   //生成file_id
        $result['size'] = 0;                                     //当前文件大小为0
        $host = 'http://' . str_replace('\\','/',$_SERVER["HTTP_HOST"]);
        $result['url'] = $host . '/' . $result['path'] . $result['fid'] . '.' . $result['suffix'];   //生成文件的url路径

        $update = $this->_file_upload->setFileIDById($result['fid'], $result['id']);       //将file_id写入数据库
        if ($update) {
          file_put_contents($result['path'] . $result['fid'] . '.' . $result['suffix'], '');
          return $this->_common->output(new obj_file($result), Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
          // $return['errorcode']= 200;
          // $return['errormsg'] = '';
          // $return['result'] = new obj_file($result);
        } else {
          return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);
        }
        // return $return;
    }

    /**
   * 根据原文生成签名内容
   *
   * @param string $data 原文内容
   *
   * @return string
   * @author confu
   */
  public function sign($data, $key)
  {
    $pass = '12345678';
    if (openssl_pkcs12_read($key, $certs, $pass)) {  
      $privateKey = $certs['pkey'];
      $publicKey = $certs['cert'];
      if (openssl_sign($data, $binarySignature, $privteKey, OPENSSL_ALGO_SHA1)) {
        return $binarySignature;
      } else {
        return '';
      }
    } else {
      return '';
    }
  }

  public function genReqData($reqData, $signature)
  {
    $length = strval(strlen($reqData));
    if (strlen($length) <= 10) {
        $length = substr('0000000000', 10 - $length) . strval($length);
    }
    return $length . $reqData . "ICBCCMP" . base64_encode($signature);
  }

  /**
   * 验证签名自己生成的是否正确
   *
   * @param string $data 签名的原文
   * @param string $signature 签名
   *
   * @return bool
   * @author confu
   */
  public function verifySign($data, $signature, $private_key_path)
  {
    if(!file_exists($private_key_path)) {
      return false;
    }
    $pkcs12 = file_get_contents($private_key_path);
    if (openssl_pkcs12_read($pkcs12, $certs, '123456')) {
      $publicKey = $certs['cert'];
      $ok = openssl_verify($data, $signature, $publicKey);
      if ($ok == 1) {
        return true;
      }
    }
    return false;
  }

  /**
   *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
   * 
   * @author Wu Junwei <www.wujunwei.net>
   * 
   * @param int $length 需要生成的字符串的长度
   * @return string 包含 大小写英文字母 和 数字 的随机字符串
   */
  function random_str($length)
  {
      //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
      $arr = array_merge(range('a', 'z'), range('A', 'Z'));
   
      $str = '';
      $arr_len = count($arr);
      for ($i = 0; $i < $length; $i++)
      {
          $rand = mt_rand(0, $arr_len-1);
          $str.=$arr[$rand];
      }
   
      return $str;
  }
//审批按日批量查询
  public function getdiviqueryd()
  {
    $common = new Common();
    $mictime = $common->get_millisecond();
    $currenttime = $common->getMillisecond();
    $transCode = 'DIVIQUERYD';
    $salt = $this->random_str(8);
    $transdate = date('Ymd');
    $transtime = date('His') . $mictime;
    $array = array(
      'BCIS'=>array(
          'eb'=>array(
              'pub'=> array(
                  'TransCode'=>$transCode,
                  'CIS'=>'002',
                  'ID'=>env('IFCAR_BANK_ID'),  //证书号
                  'TranDate'=>$transdate,  //ERP系统产生的交易日期，格式是yyyyMMdd
                  'TranTime'=>$transtime,  //ERP系统产生的交易时间，格式如HHmmssSSS，精确到毫秒；
                  'QueryDate'=>$transdate, //ERP系统产生的指令包序列号，一个集团永远不能重复；
              )
          )
       )
    );
    $secretKey = "whosyourdaddy!";
    $inputJson = json_encode($array);
    $query =  $currenttime .'#' . $inputJson . '#' . $salt . '#' . $transCode;
    $orgSign = md5(md5($secretKey . $query) . $salt);
    $url = env('IFCAR_BANK_URL')."/icbc/api/batchQuery.action";
    $data = 'transCode=' . $transCode . '&inputJson=' . $inputJson . '&salt=' . $salt . '&currentTime=' . strval($currenttime) . '&orgSign=' . $orgSign;
    $common = new Common();
    $rs = $common->curltest($url, $data);
    return $rs;
  }
//审批指令查询
  public function getdiviquery($params)
  {
    $common = new Common();
    $mictime = $common->get_millisecond();
    $currenttime = $common->getMillisecond();
    $transCode = 'DIVIQUERY';
    $salt = $this->random_str(8);
    $transdate = date('Ymd');
    $transtime = date('His') . $mictime;
    $array = array(
      'BCIS'=>array(
          'eb'=>array(
              'pub'=> array(
                  'TransCode'=>$transCode,
                  'CIS'=>'002',
                  'ID'=>env('IFCAR_BANK_ID'),  //证书号
                  'TranDate'=>$transdate,  //ERP系统产生的交易日期，格式是yyyyMMdd
                  'TranTime'=>$transtime,  //ERP系统产生的交易时间，格式如HHmmssSSS，精确到毫秒；
                  'fSeqno'=>$transdate . $transtime, //ERP系统产生的指令包序列号，一个集团永远不能重复；
              ),
              'in' => array(
                'TradeCode' => '1',
                'CardNum' => '',
                'TradeNo' => $params['tradeno'],
              )
          )
       )
    );
    $secretKey = "whosyourdaddy!";
    $inputJson = stripslashes(json_encode($array));
    $query =  $currenttime .'#' . $inputJson . '#' . $salt . '#' . $transCode;
    $orgSign = md5(md5($secretKey . $query) . $salt);
    $url = env('IFCAR_BANK_URL')."/icbc/api/approveQuery.action";
    $data = 'transCode=' . $transCode . '&inputJson=' . $inputJson . '&salt=' . $salt . '&currentTime=' . strval($currenttime) . '&orgSign=' . $orgSign;
    $common = new Common();
    $rs = $common->curltest($url, $data);
    return $rs;
  }
//图像提交确认
  public function getimagelist($param='')
  {
    $common = new Common();
    $mictime = $common->get_millisecond();
    $currenttime = $common->getMillisecond();
    $transCode = 'IMAGELIST';
    $salt = $this->random_str(8);
    $transdate = date('Ymd');
    $transtime = date('His') . $mictime;
    $length = 10;
    $imageid = rand(pow(10, ($length-1)), intval(pow(10,$length)-1));
    $seqno = rand(pow(10, ($length-1)), intval(pow(10,$length)-1));
    $array = array(
      'BCIS'=>array(
          'eb'=>array(
              'pub'=> array(
                  'TransCode'=>$transCode,
                  'CIS'=>'002',
                  'ID'=>env('IFCAR_BANK_ID'),  //证书号
                  'TranDate'=>$transdate,  //ERP系统产生的交易日期，格式是yyyyMMdd
                  'TranTime'=>$transtime,  //ERP系统产生的交易时间，格式如HHmmssSSS，精确到毫秒；
                  'fSeqno'=>$param['fSeqno'], //ERP系统产生的指令包序列号，一个集团永远不能重复；
              ),
              'in'=>array(
                  'TotalNum'=>$param['TotalNum'], //指令包内的指令笔数
                  'Backup1'=>'',
                  'rd'=>array(
                      array(
                          'Seqno'=>$param['Seqno'], //每笔指令的序号，本包内不重复。（工行只检查包内不重复，不同的包，工行不做指令顺序号重复性的检查。）
                          'ImageID'=>$param['ImageID'],  //图像视频唯一标识，不能重复
                          'ImageType'=>$param['ImageType'],  //1-图像，2-视频
                          'ImageFile'=>$param['ImageFile'],  //图像视频文件名称
                          'ImagePath'=>$param['ImagePath'],  //sftp目录下图像视频的路径，如目录文件夹20161012，写/20161012/即可
                          'TradeNo'=>$param['TradeNo'],  //图像视频对应申请的Tradeno
                          'Serialno'=>$param['Serialno'],  //申请下图像视频对应的需要，如第一张图片为1，第二张为2，第一段视频为1，所有文件ImageType,TradeNo,Serialno三个字段不能完全相同。
                          'Catagory'=>$param['Catagory'],  //图像视频对应种类 1-身份资料 2-单位职业资料 3-户籍居住资料4-联系方式资料5-婚姻资料6-购车资料7-抵押资料8-担保资料9-资产资料10-配偶资料11-担保人资料12-联系人一资料13-联系人二资料14-其他15-备用
                          'Note'=>$param['Note'],//图像视频内容备注，表明图像视频内容
                          'Backup2'=>'',
                          'Backup3'=>'',
                      ),
                  )
              )
          )
       )
    );
    $secretKey = "whosyourdaddy!";
    $inputJson = stripslashes(json_encode($array));
    $query =  $currenttime .'#' . $inputJson . '#' . $salt . '#' . $transCode;
    $orgSign = md5(md5($secretKey . $query) . $salt);
    $url = env('IFCAR_BANK_URL')."/icbc/api/imgConfirm.action";
    $data = 'transCode=' . $transCode . '&inputJson=' . $inputJson . '&salt=' . $salt . '&currentTime=' . strval($currenttime) . '&orgSign=' . $orgSign;
    $common = new Common();
    $rs = $this->_common->object_array(json_decode($common->curl_post($url, $data)));
    $arr['transcode'] = $transCode;
    $arr['status'] = $rs['obj']['BCIS']['eb']['out']['rd'][0]['Result'];
    $arr['iretmsg'] = '';
    $arr['url'] = $url;
    $arr['param'] = $data;
    $arr['work_id'] = $param['work_id'];
    $arr['response'] = json_encode($rs);
    $bank_mail = $this->_anjie_bank_mail->bank_mail($arr);
    if ($bank_mail !== false) {
      return $arr;
    }else {
      return false;
    }
    return $rs;
  }

//审批指令提交
  public function getdiviapply($param = '')
  {
    $common = new Common();
    $mictime = $common->get_millisecond();
    $currenttime = $common->getMillisecond();
    $transCode = 'DIVIAPPLY';
    $salt = $this->random_str(8);
    $transdate = date('Ymd');
    $transtime = date('His') . $mictime;
    $array = array(
      'BCIS'=>array(
          'eb'=>array(
              'pub'=> array(
                  'TransCode'=>$transCode,
                  'CIS'=>'002',
                  'ID'=>env('IFCAR_BANK_ID'), //证书号
                  'TranDate'=>$transdate,  //ERP系统产生的交易日期，格式是yyyyMMdd
                  'TranTime'=>$transtime, //ERP系统产生的交易时间，格式如HHmmssSSS，精确到毫秒；
                  'fSeqno'=>$param['fSeqno'],  //ERP系统产生的指令包序列号，一个集团永远不能重复；
              ),
              'in'=>array(
                  'TradeCode'=>'1', //指令包内的指令笔数
                  'TradeNo'=>$param['TradeNo'],
                  'ApplyDate'=>$transdate,
                  'aName'=>$param['aName'],
                  'aAge'=>strval($param['aAge']),
                  'aSex'=>$param['aSex'],
                  'aHuji'=>strval($param['aHuji']),
                  'aCertType'=>$param['aCertType'],
                  'aCertNum'=>$param['aCertNum'],
                  'aAddress'=>strval($param['aAddress']),
                  'aCorp'=>strval($param['aCorp']),
                  'aPhone'=>$param['aPhone'],
                  'aIncome'=>'150000',
                  'mName'=>'a',
                  'mCertType'=>'000',
                  'mCertNum'=>'370105198606060022',
                  'mCorp'=>'d',
                  'mPhone'=>'15566666666',

                  'mIncome'=>'100000',
                  'rName'=>'a',
                  'rCertType'=>'000',
                  'rCertNum'=>'370105198606060034',
                  'rAddress'=>'g',
                  'rCorp'=>'h',
                  'rPhone'=>'13677777777',

                  'rIncome'=>'18000',
                  'sName'=>'a',
                  'sCertType'=>'000',
                  'sCertNum'=>'370105198606060047',
                  'sCorp'=>'j',
                  'sPhone'=>'13699999999',
                  'sIncome'=>'160000',

                  'CarBrand'=>strval($param['CarBrand']),
                  'CarID'=>$param['CarID'],

                  'CarNoType'=>'',
                  'CarNo'=>'0',
                  'Insurance'=>'',

                  'CarPrice'=>strval($param['CarPrice']),
                  'FirstPay'=>strval($param['FirstPay']),
                  'CardNum'=>$param['CardNum'],
                  'DiviAmt'=>strval($param['DiviAmt']),
                  'Term'=>strval($param['Term']),
                  'FeeRate'=>$param['FeeRate'],
                  'FeeAmt'=>$param['FeeAmt'],
                  'IsAmort'=>$param['IsAmort'],
                  'AmortDetail'=>$param['AmortDetail'],
                  'AmortNum' =>'12345678',
                  'IsAssure'=>$param['IsAssure'],

                  'AssureCorp'=>'N',
                  'AssureAccount'=>'787878787878787',

                  'CoName'=>$param['CoName'],
                  'FeeMode'=>$param['FeeMode'],
                  'TellerNum'=>$param['TellerNum'],
                  'Note'=>'0',
                  'PicNum'=>$param['PicNum'],
                  'VideoNum'=>'',
                  'Backup1'=>'0000000000000000000',
                  'Backup2'=>'',
                  'Backup3'=>'',
                  'Backup4'=>'',
              )
          )
       )
    );
    $secretKey = "whosyourdaddy!";
    $inputJson = json_encode($array);
    $query =  $currenttime .'#' . $inputJson . '#' . $salt . '#' . $transCode;
    $orgSign = md5(md5($secretKey . $query) . $salt);
    $url = env('IFCAR_BANK_URL')."/icbc/api/approveSubmit.action";
    $data = 'transCode=' . $transCode . '&inputJson=' . $inputJson . '&salt=' . $salt . '&currentTime=' . strval($currenttime) . '&orgSign=' . $orgSign;
    $common = new Common();
    $rs = $this->_common->object_array(json_decode($common->curl_post($url, $data)));
    $arr['transcode'] = $transCode;
    $arr['status'] = $rs['obj']['BCIS']['eb']['out']['Result'];
    $arr['iretmsg'] = $rs['obj']['BCIS']['eb']['out']['iRetMsg'];
    $arr['url'] = $url;
    $arr['param'] = $data;
    $arr['work_id'] = $param['work_id'];
    $arr['response'] = json_encode($rs);
    $bank_mail = $this->_anjie_bank_mail->bank_mail($arr);
    if ($bank_mail !== false) {
      return $arr;
    }else {
      return false;
    }
  }
  //审批指令补录接口
  public function divisupple($param = '')
  {
    $common = new Common();
    $mictime = $common->get_millisecond();
    $currenttime = $common->getMillisecond();
    $transCode = 'DIVISUPPLE';
    $salt = $this->random_str(8);
    $transdate = date('Ymd');
    $transtime = date('His') . $mictime;
    $array = array(
      'BCIS'=>array(
          'eb'=>array(
              'pub'=> array(
                  'TransCode'=>$transCode,
                  'CIS'=>'002',
                  'ID'=>env('IFCAR_BANK_ID'), //证书号
                  'TranDate'=>$transdate,  //ERP系统产生的交易日期，格式是yyyyMMdd
                  'TranTime'=>$transtime, //ERP系统产生的交易时间，格式如HHmmssSSS，精确到毫秒；
                  'fSeqno'=>$param['fSeqno'],  //ERP系统产生的指令包序列号，一个集团永远不能重复；
              ),
              'in'=>array(
                  'TradeCode'=>'1', //指令包内的指令笔数
                  'TradeNo'=>$param['TradeNo'],
                  'ApplyDate'=>$transdate,
                  'info'=>array(
                    'aName'=>$param['aName'],
                    'aAge'=>strval($param['aAge']),
                    'aSex'=>$param['aSex'],
                    'aHuji'=>strval($param['aHuji']),
                    'aCertType'=>$param['aCertType'],
                    'aCertNum'=>$param['aCertNum'],
                    'aAddress'=>strval($param['aAddress']),
                    'aCorp'=>strval($param['aCorp']),
                    'aPhone'=>$param['aPhone'],
                    'aIncome'=>'150000',
                    'mName'=>'a',
                    'mCertType'=>'000',
                    'mCertNum'=>'370105198606060022',
                    'mCorp'=>'d',
                    'mPhone'=>'15566666666',

                    'mIncome'=>'100000',
                    'rName'=>'a',
                    'rCertType'=>'000',
                    'rCertNum'=>'370105198606060034',
                    'rAddress'=>'g',
                    'rCorp'=>'h',
                    'rPhone'=>'13677777777',

                    'rIncome'=>'18000',
                    'sName'=>'a',
                    'sCertType'=>'000',
                    'sCertNum'=>'370105198606060047',
                    'sCorp'=>'j',
                    'sPhone'=>'13699999999',
                    'sIncome'=>'160000',

                    'CarBrand'=>strval($param['CarBrand']),
                    'CarID'=>$param['CarID'],

                    'CarNoType'=>'',
                    'CarNo'=>'0',
                    'Insurance'=>'',

                    'CarPrice'=>strval($param['CarPrice']),
                    'FirstPay'=>strval($param['FirstPay']),
                    'CardNum'=>$param['CardNum'],
                    'DiviAmt'=>strval($param['DiviAmt']),
                    'Term'=>strval($param['Term']),
                    'FeeRate'=>$param['FeeRate'],
                    'FeeAmt'=>$param['FeeAmt'],
                    'IsAmort'=>$param['IsAmort'],
                    'AmortDetail'=>$param['AmortDetail'],
                    'AmortNum' =>'12345678',
                    'IsAssure'=>$param['IsAssure'],

                    'AssureCorp'=>'N',
                    'AssureAccount'=>'787878787878787',

                    'CoName'=>$param['CoName'],
                    'FeeMode'=>$param['FeeMode'],
                    'TellerNum'=>$param['TellerNum'],
                    'Note'=>'0',
                    'PicNum'=>$param['PicNum'],
                    'VideoNum'=>'',
                    'Backup1'=>'0000000000000000000',
                    'Backup2'=>'',
                    'Backup3'=>'',
                    'Backup4'=>'',
                  ),
              )
          )
       )
    );
    $secretKey = "whosyourdaddy!";
    $inputJson = json_encode($array);
    $query =  $currenttime .'#' . $inputJson . '#' . $salt . '#' . $transCode;
    $orgSign = md5(md5($secretKey . $query) . $salt);
    $url = env('IFCAR_BANK_URL'). "/icbc/api/approveUpdate.action";
    $data = 'transCode=' . $transCode . '&inputJson=' . $inputJson . '&salt=' . $salt . '&currentTime=' . strval($currenttime) . '&orgSign=' . $orgSign;
    $common = new Common();
    $rs = $this->_common->object_array(json_decode($common->curl_post($url, $data)));
    $arr['transcode'] = $transCode;
    $arr['status'] = $rs['obj']['BCIS']['eb']['out']['Result'];
    $arr['iretmsg'] = $rs['obj']['BCIS']['eb']['out']['iRetMsg'];
    $arr['url'] = $url;
    $arr['param'] = $data;
    $arr['work_id'] = $param['work_id'];
    $arr['response'] = json_encode($rs);
    $bank_mail = $this->_anjie_bank_mail->bank_mail($arr);
    if ($bank_mail !== false) {
      return $arr;
    }else {
      return false;
    }
  }
}

class obj_file {
    public $fid = '';
    public $file_token = '';
    public $size = 0;
    public $url = '';
    public $type = '';
    public $suffix = '';
    
    public function __construct($file) {
        $this->file_token = $file['file_token'];
        $this->fid = $file['fid'];
        $this->size = $file['size'];
        $this->url = $file['url'];
        $this->type = $file['type'];
        $this->suffix = $file['suffix'];
    }
}
