<?php

namespace App\Http\Controllers;
date_default_timezone_set('PRC');

// 包含文件
use Upyun\Upyun;
use Upyun\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Models\business\Prize;
use Request;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\Handler;
use Log;
use GuzzleHttp\Psr7;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;

class PrizeController extends Controller
{
    public function index()
    {
         // return response()->view('errors.404', [], 500);
    	try{
            $pdo = new Pdo();
            $sql = "select * from v1_prize where iid = 1";
            $rs = $pdo->fetchAll($sql);
            try {
                $pdo->beginTransaction();
                // $sql = "update api2_category set cate_type = 1 where id = 1";
                // $rs = $pdo->execute($sql);
                // $sql = "update api2_category set cate_type = 1 where id = 3";
                // $rs = $pdo->execute($sql);
                // $sql = "insert into api2_category(cate_type, cate_name, `desc`, is_menu, sort) values(?,?,?,?,?)";
                // $rs = $pdo->execute($sql, array('0', 'test', '', '2', '0'));
                // $id = $pdo->lastInsertId();
                $pdo->commit();
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                $pdo->rollback();
            }
            // $sql = "update api2_category set cate_type = 1 where id = 1";
            // $rs = $pdo->execute($sql);


         //    throw new \Exception("Error Processing Request", 1);
	        // $select = DB::select("select * from v1_prize where iid = 1");
        }
        catch(\Exception $e){ 
            var_dump($e->getMessage());
            var_dump($e->getCode());
            return response()->json([ 'msg' => 'success']);
            return view('home');
        // $error_code = $e->errorInfo[1];
        }
    	$select = DB::select("select * from v1_prize where iid = 1");
    	// $update = DB::statement("update v1_prize set c?id = 1 where pid = 1");
    	// $insert = DB::statement("insert into test (id) values (1)");
       // $list=DB::table('v1_prize')->paginate(5);
        $list=Prize::where('pid','<=',50)->orderBy('pid','desc')->paginate(10);
       // dd($list);die();
       // dd($list->links());die();
        //return view('admin.prize.index',$list);
        return view('admin.prize.index',compact('list'));
    }

    public function role()
    {
        return view('admin.prize.role')->with('title', 'test');
    }

//try ajax
    public function rolepost()
    {
    	try{
	        $select = DB::select("select * from v1_prize where iid = 1");
	        $query = new QueryException("select * from v1_prize where iid = 1", array(), null);
        }
        catch (Illuminate\Database\QueryException $e){
        	$result['errorcode'] = '-1';
        	// $result['errormsg'] = $e->errorInfo[1];
        	return $result;
        }
    	if(Request::ajax()) {
    		$url = Request::url();
    		$name = Request::input('date', 'default');
    		$input = Request::all();
    		return $input;
        }
    }

//try ajaxuploadfile
    public function filepost()
    {
    	$file = Request::file('uploadfile');
    	if($file->isValid()){	//判断文件是否上传成功
	        $originalName = $file->getClientOriginalName(); //源文件名
	        $ext = $file->getClientOriginalExtension();    //文件拓展名
	        $type = $file->getClientMimeType(); //文件类型
	        $realPath = $file->getRealPath();   //临时文件的绝对路径
	        $fileName = date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;  //新文件名
	        $bool = Storage::disk('uploads')->put($fileName,file_get_contents($realPath));   //传成功返回bool值
	        return json_encode($bool);
        }else {
        	return view('admin.prize.role');
        }
    }
    //断点下载
    public function downloadfile()
    {
    	$num = 10;
    	$url = Request::input('sourcefile');
    	$outFile = Request::input('filename');
    	$prize = new Prize();
    	$headerarr = $prize->getHeaders($url);
    	if (isset($headerarr['X-ErrNo'])) {
    		$headerarr = $prize->getHeaders($url);
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
    	    $result = $prize->urlRequest($url, $start, $end);
	        if ($result['output'] == '') {
	        	$result = $prize->urlRequest($url, $start, $end);
	        }
	        $finaloutput =$finaloutput . $result['output'];
	        $putContent = file_put_contents('download/'.$result['filename'], $result['output']);
            $start = $end +1;
            // $putContent = file_put_contents('download/'.$outFile, $finaloutput);
    	}
    	$putContent = file_put_contents('download/'.$outFile, $finaloutput);
    }
    //断点上传
   //  public function uploadfile()
   //  {
   //  	// $num = 10;
   //  	$little = 200; //最小尺寸
   //  	$file = Request::input('file');
   //  	$outfile = 'banner.jpg';
   //  	$file = 'C:\uploadsfile\banner.jpg';
   //  	$size = filesize($file);
   //  	$num = ceil($size/$little);
   //  	// $little = ceil($size/$num);
   //  	$start = 0;
   //  	$finaldata = '';
   //  	for ($i=0; $i <$num ; $i++) { 
   //  		if ($i == $num-1) {
   //  			$little = $size - ($num-1) * $little;
   //  		}
   //  		$getdata = file_get_contents($file,  NULL, NULL, intval($start), $little);
   //  		if ($getdata == '') {
   //  			$getdata = file_get_contents($file,  NULL, NULL, intval($start), $little);
   //  		}
   //  		$finaldata = $finaldata . $getdata;
   //  		$filename = md5($file).$start . '.jpg';

   //  		$url = 'http://dev.anjie.com:8080/index.php/Admin/prize/getuploadfile';
   //  		// $file = 'uploads/'.$outfile;
   //  		$data = array(
   //  			'data' => $getdata, 
   //  		);
   //  		$ch = curl_init();
   //  		curl_setopt($ch, CURLOPT_URL, $url);
			// //curl_setopt($ch, CURLOPT_POST, 1);
			// curl_setopt($ch, CURLOPT_HEADER, 0);
			// curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			// curl_setopt($ch, CURLOPT_TIMEOUT,30);
			// $return = curl_exec($ch);
			// curl_close($ch);
			// var_dump($return);


   //  		$putContent = file_put_contents('uploads/'.$filename, $getdata);
   //  		$start = $start + $little;
   //  		echo 1;exit;
   //  	}
   //  	$putContent = file_put_contents('uploads/'.$outfile, $finaldata);
   //  }

    public function uploadfile()
    {
    	$little = 200; 
		$outfile = 'banner.jpg';
		$file = 'C:\uploadsfile\banner.jpg';
		$size = filesize($file);
		$num = ceil($size/$little);
		$start = 0;
		$finaldata = '';
		for ($i=0; $i <$num ; $i++) { 
			if ($i == $num-1) {
				$little = $size - ($num-1) * $little;
			}
			$getdata = file_get_contents($file,  NULL, NULL, intval($start), $little);
			if ($getdata == '') {
				$getdata = file_get_contents($file,  NULL, NULL, intval($start), $little);
			}
			$finaldata = $finaldata . $getdata;
			$filename = md5($file).$start . '.jpg';

			$url = 'http://dev.anjie.com:8080/index.php/Admin/prize/getuploadfile';
			$data = array(
		    	'file' => $filename, 
		    	'data' => $getdata,
		    );
		    $prize = new Prize();
		    $return = $prize->curltest($url, $data);

			//$putContent = file_put_contents('uploads/'.$filename, $getdata);
			$start = $start + $little;
		}
		    $data = array(
		    	'file' => $outfile, 
		    	'data' => $finaldata,
		    );
		    $return = $prize->curltest($url, $data);
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
    	$getdata = isset($_POST['data']) ? $_POST['data'] : '';
    	$offset = Request::input('offset', 0);
    	$isend = Request::input('isend', 0);
    	$sql = "select * from file_upload where file_token = ?";
    	$getTokenInfo = DB::select($sql, [$fileToken]);
    	if (empty($getTokenInfo) || $getTokenInfo[0]->token_expired_in < time()) {
            $return['errorcode'] = -1;
            $return['errormsg'] = '文件token已超期!';
            $return['result'] = '1';
    		return $return;
    	}
        $filename = $getTokenInfo[0]->file_id . '.' . $getTokenInfo[0]->suffix;
        if (!file_exists($getTokenInfo[0]->path . $filename)) {
            $return['errorcode'] = -1;
            $return['errormsg'] = '文件解析失败，请重试!';
            $return['result'] = '2';
            return $return;
        }
    	$handle = fopen($getTokenInfo[0]->path . $filename, 'rb+');
    	fseek($handle, $offset);
    	fwrite($handle, $getdata);
    	if ($isend == 1) {             //判断文件是否已传完
    		ftruncate($handle, $offset + strlen($getdata));
            $sql = "update file_upload set is_end = ? where file_token = ?";
            $update = DB::statement($sql, [$isend, $fileToken]);
    	} 
    	fclose($handle);
        $size = filesize($getTokenInfo[0]->path. $filename);
        $return['errorcode'] = 0;
        $return['errormsg'] = '';
        $return['filesize'] = $size;
        $sql = "update file_upload set size = ? where file_token = ?";
        $update = DB::statement($sql, [$size, $fileToken]);
    	return $return;
    	// $putContent = file_put_contents('uploads/'.$filename, $getdata, FILE_APPEND);
    }
    /*  生成file_token和token_expired_in
        @params  filename          传入文件名称
        @params  type              传入文件类型
        @params  sync_to_upyun     是否传到又拍云，0为不传，1为传
        @return  fid               文件id
        @return  file_token        文件token
        @return  size              文件当前大小
        @return  suffix            文件后缀
        @return  type              文件类型
        @return  url               访问地址      
    */
    public function make()
    {
        // $data['user_id'] = $this->api->user()->user_id;   获取userid
        $data['user_id'] = 200;    //暂时先随便定一个
    	$data['type'] = Request::input('type', '');
        $typeallow = Prize::$_typeallow;
        if (!isset($typeallow[$data['type']]) || $typeallow[$data['type']] !== 1) {
            $return['errorcode'] = -1;
            $return['errormsg'] = '所传文件类型暂不支持！';
            $return['result'] = '';
            return $return;
        }
        $filename = Request::input('filename', '');
        $data['suffix'] = strtolower(pathInfo($filename, PATHINFO_EXTENSION));
        $imageallow = Prize::$_imagesallow;
        if ($data['type'] == 'image' && (!isset($imageallow[$data['suffix']]) || $imageallow[$data['suffix']] !== 1)) {
            $return['errorcode'] = -1;
            $return['errormsg'] = '所传文件格式暂不支持！';
            $return['result'] = '';
            return $return;
        }
        $path = 'uploads/' . $data['type'] . '/' . date('Ymd') . '/';
        $data['path'] = $path;
        $data['sync_to_upyun'] = Request::input('sync_to_upyun', '0');
        $data['file_token'] = md5($filename) . microtime() . rand(10000, 99999);
        $data['token_expired_in'] = time() + 3600 * 4 * 7;
        if (!file_exists(FCPATH . $path)) {
            mkdir(FCPATH . $path, 0777, true);
        }
        $prize = new Prize();
        $return = $prize->add($data);
        return $return;
    }
    //hbuild

    public function requesttest()
    {
        header('Content-type: application/x-www-form-urlencoded');

    	file_put_contents('input.txt', 'text', FILE_APPEND);
    	// $input = Request::all();
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ret=array('strings'=>$_POST,'error'=>'0');
		    $fs=array();
		    foreach ( $_FILES as $name=>$file ) {
		        $fn=$file['name'];
		        file_put_contents('input.txt', $fn, FILE_APPEND);
		        $ft=strrpos($fn,'.',0);
		        $fm=substr($fn,0,$ft);
		        $fe=substr($fn,$ft);
		        $fp='uploads/'.$fn;
		        file_put_contents('input.txt', $fp, FILE_APPEND);
		        $fi=1;
		        while(file_exists($fp)) {
		            $fn=$fm.'['.$fi.']'.$fe;
		            $fp='uploads/'.$fn;
		            $fi++;
		        }
		        file_put_contents('input.txt', $file['tmp_name'], FILE_APPEND);
		        $upload = move_uploaded_file($file['tmp_name'],$fp);
		        // file_put_contents('input.txt', $upload, FILE_APPEND);
		        $fs[$name]=array('name'=>$fn,'url'=>$fp,'type'=>$file['type'],'size'=>$file['size']);
		    }                                 
			 $ret['files']=$fs;
			 echo json_encode($ret);
		}else{
		  	  echo "{'error':'Unsupport GET request!'}";
		}
    }

    public function upyuntest()
    {
        return view('admin.prize.upyuntest');
    }
    /* 
       使用upyun上传文件     
    */
    public function upyunposttest()
    {
        $uploadsQueue = DB::select("select * from file_upload where sync_to_upyun_status = '0' order by id");
        if (empty($uploadsQueue)) {
            return;
        }
        try{
            $prize = new Prize;
            $prize->handleUploadQueue($uploadsQueue);   //处理需要上传的队列
        } catch(\Exception $e) { 
            if ($e->getCode()) {
                echo $e->getMessage();
                Log::uploads($e->getMessage());
                $prize = new Prize;
                $prize->handleUploadQueue($uploadsQueue);
            }
        }
    }
    
    /* 
        向银行服务器上传文件     
    */
    public function bankpost()
    {
        set_time_limit(0);
        $prize = new Prize;
        $bankUploadsQueue = $prize->getBankUploadsQueue();
        if (empty($bankUploadsQueue)) {
            return;
        }
        try{
            $prize->uploadsToBankQueue($bankUploadsQueue);   //处理需要上传的队列
        } catch(\Exception $e) { 
            if ($e->getCode()) {
                echo $e->getMessage();
                Log::uploads($e->getMessage());
                $prize->uploadsToBankQueue($bankUploadsQueue);
            }
        }
    }

    /* 
        向银行请求图像确认     
    */
    public function bankimageconfirm()
    {
        $prize = new Prize();
        $Version = '0.0.0.1';
        $TransCode = 'IMAGELIST';
        $BankCode = '102';
        $GroupCIS = '002';
        $ID = 'B2C2017.e.1602';
        $PackageID = "2017032918491729536228";
        $fSeqno = "2017032918491729536228";
        $Cert = base64_encode(file_get_contents('D:/phpStudy/WWW/dev_anjie/public/user.cer'));
        $bkey = file_get_contents('D:/phpStudy/WWW/dev_anjie/public/user.key');
        $fc = file_get_contents('D:/phpStudy/WWW/dev_anjie/public/imagelist.txt');
        $fc = str_replace("@fSeqno", $fSeqno, $fc);
        $date = date('Ymd');
        $time = date('His');
        $fc = str_replace("@TranDate", $date, $fc);
        $fc = str_replace("@TranTime", $time, $fc);
        $rd = base64_encode($fc);
        $password = "12345678";
        $signature = $prize->sign($rd, $bkey);
        $reqData = base64_encode($rd);
        $rd = base64_encode($prize->genReqData($reqData, $signature));

        $url = "https://myipad.dccnet.com.cn:450/icbc/bcisfront/TestInfo.jsp";
        $data = array(
            'Version' => $Version,
            'TransCode' => $TransCode,
            'BankCode' => $BankCode,
            'GroupCIS' => $GroupCIS,
            'ID' => $ID,
            'PackageID' => $PackageID,
            'Cert' => $Cert,
            'reqData' => $rd,
        );
        $result = $prize->curltestfor($url, $data);
        // var_dump($result);
    }   

    public function report(Exception $e){
    if ($e instanceof CustomException) {
        //
    }

    return parent::report($e);
}

    // public function getUploadFile()
    // {
    // 	$filename = Request::input('file');
    // 	$getdata = Request::input('data');
    // 	$num = Request::input('num');
    // 	$present = Request::input('present');
    // 	$putContent = file_put_contents('uploads/'.$filename, $getdata);
    // }
}
