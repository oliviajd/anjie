<?php

namespace App\Http\Controllers;
use Request;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use Excel;
//主要用于展示静态页面
class ExcelController extends Controller
{ 

    public function __construct()
    {
        parent::__construct();
    }
    //Excel文件导出功能 By Laravel学院
    public function export(){
        $cellData = [
            ['学号','姓名','成绩'],
            ['10001','AAAAA','99'],
            ['10002','BBBBB','92'],
            ['10003','CCCCC','95'],
            ['10004','DDDDD','89'],
            ['10005','EEEEE','96'],
        ];
        Excel::create('学生成绩',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}