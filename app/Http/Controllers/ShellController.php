<?php

namespace App\Http\Controllers;
use App\Http\Models\business\Workplatform;
use Request;
use Illuminate\Support\Facades\Log;
use App\Http\Models\common\Pdo;
//主要用于展示静态页面
class ShellController extends Controller
{
    protected $_pdo = null;
    protected $allowIp = null;
    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }


    /**
     * shell脚本定时上传银行资料
     */
    public function shelltranstobank(){
        //查询二审通过并且没有给银行上传资料的数据
        $sql = 'SELECT id FROM anjie_work AS aw LEFT JOIN anjie_task AS atk ON aw.id = atk.work_id  WHERE aw.upload_status = 0 AND atk.item_id = 37 AND atk.task_status = 1';
        $result = $this->_pdo->fetchAll($sql);
        if(!empty($result)){
            Log::info("本次脚本中的work_id列表:".json_encode($result));
            $workplatform = new Workplatform();
            foreach($result as $id){
                $rs = $workplatform->transtobank(['work_id'=>$id['id']]);   //认领征信的具体逻辑
                if(!$rs){
                    Log::info("work_id=".$id['id']."的数据上传出现问题");
                }else{
                    Log::info("work_id=".$id['id']."的数据上传完成");
                }
            }
        }else{
            Log::info("======本次脚本中没有数据=========");
        }
    }
}