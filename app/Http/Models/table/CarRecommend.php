<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class CarRecommend extends Model
{
    protected $table = 'car_recommend';
    public $timestamps = false;
    protected $primaryKey = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public static function getRecommend()
    {
        $re = self::where('addtime', date('Y-m-d'))->get();
        if ($re){
            return $re->toArray();
        }
        return $re;
    }

    public static function deleteRecommend()
    {
        self::truncate();
    }

    public static function insert($data)
    {
        $data['addtime'] = date('Y-m-d');
        $id = self::insertGetId($data);
        return $id;
    }

}