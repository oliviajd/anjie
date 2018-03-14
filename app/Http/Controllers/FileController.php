<?php

namespace App\Http\Controllers;
date_default_timezone_set('PRC');

// 包含文件
use Upyun\Upyun;
use Upyun\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Models\business\File;
use App\Http\Models\business\Auth;
use Request;
use Illuminate\Support\Facades\Storage;
use Log;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;

class FileController extends Controller
{ 
    private $_file = null;
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_file = new File();
        $this->_auth = new Auth();
    }

    public function index()
    {
        return view('admin.file.index')->with('title', 'test');
    }
    public function lunbo()
    {
        return view('admin.file.lunbo')->with('title', 'test');
    }
    //文件上传接口
    public function upload()
    {
        $file = Request::file('file');
        $data = array();
        $token =  Request::input('token', '');
        $data['user_id'] = $this->_auth->getUseridBytoken($token);
        if (!$data['user_id']) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $data['type'] = Request::input('type', '');
        if($file && $file->isValid()){   //判断文件是否上传成功
            $originalName = $file->getClientOriginalName(); //源文件名
            $data['suffix'] = $file->getClientOriginalExtension();    //文件拓展名
            $data['size'] = $file->getClientSize();
            // $type = $file->getClientMimeType(); //文件类型
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $typeallow = File::$_typeallow;
            if (!isset($typeallow[$data['type']]) || $typeallow[$data['type']] !== 1) {
                return $this->_common->output(false, Constant::ERR_NONSUPPORT_FILE_FORMAT_NO, Constant::ERR_NONSUPPORT_FILE_FORMAT_MSG);
            }
            // $imageallow = File::$_imagesallow;
            // if ($data['type'] == 'image' && (!isset($imageallow[$data['suffix']]) || $imageallow[$data['suffix']] !== 1)) {
            //     return $this->_common->output(false, Constant::ERR_NONSUPPORT_FILE_FORMAT_NO, Constant::ERR_NONSUPPORT_FILE_FORMAT_MSG);
            // }
            $path = 'uploads/' . $data['type'] . '/' . date('Ymd') . '/';
            $data['path'] = $path;
            $data['file_token'] = md5($originalName) . microtime() . rand(10000, 99999);
            $data['token_expired_in'] = time() + 3600 * 4 * 7;
            $data['sync_to_upyun'] = Request::input('sync_to_upyun', '0');
            if (!file_exists(FCPATH . $path)) {
                mkdir(FCPATH . $path, 0777, true);
            }
            $return = $this->_file->add($data);
            $result = json_decode($return);
            $fileName =  $result->result->fid . '.' . $result->result->suffix;
            $bool = file_put_contents(FCPATH . $path . $fileName, file_get_contents($realPath));
            return $return;
        } else {
            return $this->_common->output(false, Constant::ERR_NON_FILE_NO, Constant::ERR_NON_FILE_MSG);
        }
    }
    public function thumbfile()
    {
        //检查是否开启GD库
        if (!function_exists('gd_info')) {
            $this->api->output(false, ERR_REQUIRE_GD_NO, ERR_REQUIRE_GD_MSG);
        }
        $thumbfile = $this->_file->thumbfile();
        return $thumbfile;
    }
    //hbuild
    public function hbuilderupload()
    {
        header('Content-type: application/x-www-form-urlencoded');
        $token =  Request::input('token', '');
        $data['user_id'] = $this->_auth->getUseridBytoken($token);
        if (!$data['user_id']) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $data['type'] = Request::input('type', '');
        $typeallow = File::$_typeallow;
        if (!isset($typeallow[$data['type']]) || $typeallow[$data['type']] !== 1) {
            return $this->_common->output(false, Constant::ERR_NONSUPPORT_FILE_FORMAT_NO, Constant::ERR_NONSUPPORT_FILE_FORMAT_MSG);
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ret=array('strings'=>$_POST,'error'=>'0');
            $fs=array();
            foreach ( $_FILES as $name=>$file ) {
                $fn=$file['name'];
                $ft=strrpos($fn,'.',0);
                $fm=substr($fn,0,$ft);
                $fe=substr($fn,$ft);
                $path = 'uploads/' . $data['type'] . '/' . date('Ymd') . '/';
                $data['path'] = $path;
                $fp=$path . $fn;
                $fi=1;
                $data['suffix'] = pathinfo($fn, PATHINFO_EXTENSION);    //文件拓展名
                $data['size'] = $file['size'];    //文件大小
                $data['file_token'] = md5($fn) . microtime() . rand(10000, 99999);
                $data['token_expired_in'] = time() + 3600 * 4 * 7;
                $data['sync_to_upyun'] = Request::input('sync_to_upyun', '0');
                if (!file_exists(FCPATH . $path)) {
                    mkdir(FCPATH . $path, 0777, true);
                }
                $return = $this->_file->add($data);
                $result = json_decode($return);
                $fileName =  $result->result->fid . '.' . $result->result->suffix;
                move_uploaded_file($file['tmp_name'],FCPATH . $path . $fileName);
                return $return;
            }                                 
        }else{
            return $this->_common->output(false, Constant::ERR_NON_FILE_NO, "{'error':'Unsupport GET request!'}");
        }
    }
    //文件打包下载
    public function packagefile()
    {
        $param['work_id'] =  Request::input('work_id', '');
        $data = array();
        $token =  Request::input('token', '');
        $data['user_id'] = $this->_auth->getUseridBytoken($token);
        if (!$data['user_id']) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $packagefile = $this->_file->packagefile($param, $data['user_id']);
        return $packagefile;
    }
//删掉打包下载的东西
    public function unlinkpackage()
    {
        $this->_file->unlinkpackage();
    }
//删掉影像资料
    public function unlinkvideoimage()
    {
        $this->_file->unlinkvideoimage();
    }

    //word生成
    public function wordcreate()
    {
        $param['work_id'] =  Request::input('work_id', '');
        $data = array();
        $token =  Request::input('token', '');
        $data['user_id'] = $this->_auth->getUseridBytoken($token);
        if (!$data['user_id']) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $wordcreate = $this->_file->wordcreate($param, $data['user_id']);
        return $wordcreate;
    }

    //列出所有图片的类别
    public function listimagetype()
    {
        $param['type'] =  Request::input('type', '2');
        //列出图片类别
        $rs = $this->_file->listimagetype($param['type']);
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
    /**
     * 添加图像
     * @param token          用户token
     * @param file_class_id  文件类型ID
     * @param file_path      文件地址
     * @param work_id        当前这个件的work_id
     * @param type           为1时，添加图像和视频，为2时，添加家访视频
     */
    public function addimage()
    {
        //获得操作者的userid
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['add_userid'] = $user_id; //添加者的userid
        $param['file_type'] =  Request::input('file_type', '1');  //文件类别，1为image、2为video,给手机端里面，这个参数不需要
        $param['file_type_name'] = ($param['file_type'] == '1') ? 'image' : 'video';
        $param['file_class_id'] = Request::input('file_class_id', '');  //文件类型ID,是在获取图片类别接口中的文件类型id，家访录像的时候，就不用传这个参数了
        $param['file_path'] = Request::input('file_path', '');  //文件地址
        $param['work_id'] = Request::input('work_id', '');  //当前这个件的work_id
        $param['file_id'] = Request::input('file_id', '');  //文件id
        $param['cover_image'] = Request::input('cover_image', '');  //封面图片
        $param['type'] = Request::input('type', '1'); 
        if ($param['type'] == '2') {                //type=2的时候，就固定为传公司家访视频，type=3,则为家里家访视频，file_class_id可以不传
            $param['file_type'] = '2';
            $param['file_class_id'] = '7';
            $param['file_type_name'] = 'video';
        } elseif($param['type'] == '3') {  //type=3,则为家里家访视频，file_class_id可以不传
            $param['file_type'] = '2';
            $param['file_class_id'] = '8';
            $param['file_type_name'] = 'video';
        }
        $rs = $this->_file->addimage($param);  //添加图像逻辑
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
    public function addworkimages()
    {
        //获得操作者的userid
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['imgs'] = json_decode(Request::input('imgs'), true);
        $param['work_id'] = Request::input('work_id', '');
        $param['user_id'] = $user_id;
        $rs = $this->_file->addworkimages($param);  //添加图像逻辑
        return $rs;
    }
    /**
     * 删除图像、视频
     * @param token          用户token
     * @param file_id        文件ID
     */
    public function deleteimage()
    {
        //获得操作者的userid
        $token =  Request::input('token', '');
        $this->_common->setlog();
        $user_id = $this->_auth->getUseridBytoken($token);
        if (!$user_id) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $param['delete_userid'] = $user_id; //删除者的userid
        $param['file_id'] =  Request::input('file_id', '');  //文件的file_id
        $rs = $this->_file->deleteimage($param);  //删除图像逻辑
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
    /**
     *列出图像、视频
     * @param type          1为image、2为家访录像
     * @param work_id            被查询的件的work_id
     * @param file_class_id      文件类型ID
     */
    public function listimages()
    {
        // $param['type'] =  Request::input('type', '0'); //1为image、2为家访录像
        $param['work_id'] = Request::input('work_id', '');  //被查询的件的work_id
        $rs = $this->_file->listimages($param);  //列出图像、视频
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
    /**
     *列出图像、视频
     * @param type          1为image、2为家访录像
     * @param verify_id            被查询的件的verify_id
     * @param file_class_id      文件类型ID
     */
    public function listjcrimages()
    {
        $param['verify_id'] = Request::input('verify_id', '');  //被查询的件的work_id
        $rs = $this->_file->listjcrimages($param);  //列出图像、视频
        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);  //如果返回的是false
        }
    }
    //文件上传接口
    public function uploadfile()
    {
        $file = Request::file('file');
        $data = array();
        $token =  Request::input('token', '');
        $data['user_id'] = $this->_auth->getUseridBytoken($token);
        if (!$data['user_id']) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
        $data['type'] = Request::input('type', '');
        if($file && $file->isValid()){   //判断文件是否上传成功
            $originalName = $file->getClientOriginalName(); //源文件名
            $data['suffix'] = $file->getClientOriginalExtension();    //文件拓展名
            $data['size'] = $file->getClientSize();;
            // $type = $file->getClientMimeType(); //文件类型
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $path = 'uploads/' . $data['type'] . '/' . date('Ymd') . '/';
            $data['path'] = $path;
            $data['file_token'] = md5($originalName) . microtime() . rand(10000, 99999);
            $data['token_expired_in'] = time() + 3600 * 4 * 7;
            $data['sync_to_upyun'] = Request::input('sync_to_upyun', '0');
            if (!file_exists(FCPATH . $path)) {
                mkdir(FCPATH . $path, 0777, true);
            }
            $return = $this->_file->add($data);
            $result = json_decode($return);
            $fileName =  $result->result->fid . '.' . $result->result->suffix;
            $bool = file_put_contents(FCPATH . $path . $fileName, file_get_contents($realPath));
            return $return;
        } else {
            return $this->_common->output(false, Constant::ERR_NON_FILE_NO, Constant::ERR_NON_FILE_MSG);
        }
    }

    //断点下载
    public function downloadfile()
    {
    	$num = 10;
    	$url = Request::input('sourcefile');
    	$outFile = Request::input('filename');
    	$headerarr = $this->_file->getHeaders($url);
    	if (isset($headerarr['X-ErrNo'])) {
    		$headerarr = $this->_file->getHeaders($url);
	    	if (!isset($headerarr['X-ErrNo'])) {
	    		// throw new Exception("$headerarr['X-ErrNo']", -1);
	    	}
    	}
    	$finaloutput = '';
    	$little = ceil(floatval($headerarr['Size'])/$num);
    	$start = 0;
    	for ($i=0; $i <$num ; $i++) {
    		$end = $start + $little;
    		if ($i == $num-1) {
    			$end = trim($headerarr['Size']);
    	    } 
    	    $result = $this->_file->urlRequest($url, $start, $end);
	        if ($result['output'] == '') {
	        	$result = $this->_file->urlRequest($url, $start, $end);
	        }
	        $finaloutput =$finaloutput . $result['output'];
	        $putContent = file_put_contents('download/'.$result['filename'], $result['output']);
            $start = $end +1;
            // $putContent = file_put_contents('download/'.$outFile, $finaloutput);
    	}
    	$putContent = file_put_contents('download/'.$outFile, $finaloutput);
    }
    /*  分块上传文件
        @params  file_token      文件token
        @params  data            需要写入文件的数据
        @params  offset          文件写入起始位置
        @params  isend           是否已经结束
        @return  filesize        文件大小    
    */
    public function getUploadFile()
    {
    	$fileToken = Request::input('file_token', '');
    	$offset = Request::input('offset', 0);
    	$isend = Request::input('isend', 0);
        $file = Request::file('data');
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $tokenInfo = $this->_file->getInfoByFiletoken($fileToken);   //根据file_token获取文件信息
        if (empty($tokenInfo) || $tokenInfo['token_expired_in'] < time()) {
            return $this->_common->output(false, Constant::ERR_FILE_TOKEN_OVERDUE__NO, Constant::ERR_FILE_TOKEN_OVERDUE_MSG);
        }
        $filename = $tokenInfo['file_id'] . '.' . $tokenInfo['suffix'];
        if (!file_exists($tokenInfo['path'] . $filename)) {
            return $this->_common->output(false, Constant::ERR_FILE_ANALYTIC_FAILED_NO, Constant::ERR_FILE_ANALYTIC_FAILED_MSG);
        }
        $getdata = file_get_contents($realPath);
    	$handle = fopen($tokenInfo['path'] . $filename, 'rb+');
    	fseek($handle, $offset);
    	fwrite($handle, $getdata);
    	if ($isend == 1) {             //判断文件是否已传完
    		ftruncate($handle, $offset + strlen($getdata));
            $rs = $this->_file->updateIsEndByFiletoken($isend, $fileToken);
    	} 
    	fclose($handle);
        $size = filesize($tokenInfo['path']. $filename);
        $rs = $this->_file->updateSizeByFiletoken($size, $fileToken);
        return $this->_common->output($size, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }
    /**
     * 生成file_token和token_expired_in
     * @param int filename          传入文件名称
     * @param int type              传入文件类型
     * @param int sync_to_upyun     是否传到又拍云，0为不传，1为传
     * @param int fid               文件id
     * @param int file_token        文件token
     * @param int size              文件当前大小
     * @param int suffix            文件后缀
     * @return type                 文件类型
     * @return url                  访问地址
     */
    public function make()
    {
        // $data['user_id'] = $_SESSION['user_id'];      //获取userid
        $token =  Request::input('token', '');
        $data['user_id'] = $this->_auth->getUseridBytoken($token);
        if (!$data['user_id']) {
            return $this->_common->output('', Constant::ERR_TOKEN_NOT_EXISTS_NO, Constant::ERR_TOKEN_NOT_EXISTS_MSG);
        }
    	$data['type'] = Request::input('type', '');   //文件类型
        $typeallow = File::$_typeallow;               //文件暂时只支持image和vedio的上传
        if (!isset($typeallow[$data['type']]) || $typeallow[$data['type']] !== 1) {
            return $this->_common->output('', Constant::ERR_NONSUPPORT_FILE_FORMAT_NO, Constant::ERR_NONSUPPORT_FILE_FORMAT_MSG);
        }
        $filename = Request::input('filename', '');      //原文件名
        $data['suffix'] = strtolower(pathInfo($filename, PATHINFO_EXTENSION));   //获取原文件的后缀名
        $imageallow = File::$_imagesallow;               //允许传入的图片格式 对应为0的是暂不支持的图片格式
        if ($data['type'] == 'image' && (!isset($imageallow[$data['suffix']]) || $imageallow[$data['suffix']] !== 1)) {
            return $this->_common->output('', Constant::ERR_NONSUPPORT_FILE_FORMAT_NO, Constant::ERR_NONSUPPORT_FILE_FORMAT_MSG);
        }
        $path = 'uploads/' . $data['type'] . '/' . date('Ymd') . '/';   //文件上传的路径，基础路径为public
        $data['path'] = $path;
        $data['sync_to_upyun'] = Request::input('sync_to_upyun', '0');  //是否传到又拍云，0为不传，1为传
        $data['file_token'] = md5($filename) . microtime() . rand(10000, 99999);  //产生file_token
        $data['token_expired_in'] = time() + 3600 * 4 * 7;              //file_token的有效截止时间的时间戳
        if (!file_exists(FCPATH . $path)) {
            mkdir(FCPATH . $path, 0777);                                 //文件路径不存在则创建该文件夹，并给权限
        }
        return $this->_file->add($data);                              //主要逻辑
    }
    /* 
       使用upyun上传文件     
    */
    public function upyunpost()
    {
        $uploadsQueue = $this->_file->getUpyunUploadsQueue();  //获取需要上传到又拍云服务器的队列
        if (empty($uploadsQueue)) {
            return;
        }
        try{
            $this->_file->handleUploadQueue($uploadsQueue);   //处理需要上传到又拍云服务器的队列
        } catch(\Exception $e) { 
            if ($e->getCode()) {
                echo $e->getMessage();
                Log::uploads($e->getMessage());
                $this->_file->handleUploadQueue($uploadsQueue);    //若上传失败，则再次上传
            }
        }
    }    
    /* 
        向银行服务器上传文件     
    */
    public function bankpost()
    {
        set_time_limit(0);
        $bankUploadsQueue = $this->_file->getBankUploadsQueue();  //获取需要上传到银行的文件队列
        if (empty($bankUploadsQueue)) {
            return;
        }
        try{
            $this->_file->uploadsToBankQueue($bankUploadsQueue);   //处理需要上传的文件队列
        } catch(\Exception $e) { 
            if ($e->getCode()) {
                echo $e->getMessage();
                Log::uploads($e->getMessage());
                $this->_file->uploadsToBankQueue($bankUploadsQueue);  //若上传失败，再次上传
            }
        }
    } 
    /**
        图像确认接口     
    */
    public function bankimageconfirm()
    {
        // $rs = $this->_file->getdiviqueryd(); //审批按日批量查询
        // $rs = $this->_file->getdiviquery();  //审批指令查询
        // $rs = $this->_file->getimagelist();  //图像提交确认
        // $rs = $this->_file->getdiviapply(); //审批指令提交
    }
    //审批指令查询接口
    public function getdiviquery()
    {
        $params['tradeno'] =  Request::input('tradeno', '');
        $rs = $this->_file->getdiviquery($params);  //审批指令查询
        return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);
    }

}
