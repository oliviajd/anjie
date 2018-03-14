<?php

namespace App\Http\Models\table;

use App\Http\Models\common\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class Jcr_car_history extends Model
{
    protected $table = 'jcr_car_history';
    public $primaryKey = 'id';

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    //添加标的信息
    public static function addCarHistory($params)
    {
        self::getDetailByParams(['model_id'=>$params['model_id'],'reg_date'=>$params['reg_date'],'mile'=>$params['mile'],'zone'=>$params['zone']]);

        $id = self::insertGetId($params);

        return $id;
    }

    public static function getDetailById($history_id)
    {
        $result = self::find($history_id);

        if ($result) {
            return $result->toArray();
        } else {
            return [];
        }
    }

    public static function getDetailByParams($params)
    {
        $result = self::where($params)->first();

        if ($result) {
            return $result->toArray();
        } else {
            return [];
        }

    }

    //获取列表
    public static function getList($p, $n, $params, $fields = ['*'])
    {
        foreach ($params as $k => $value) {
            if ($value !== 0 && $value !== '' && $value !== null && $value !== []) {
                switch ($k) {
                    case 'model_name':
                        $searchCondition[] = ['model_name', 'like', '%' . $value . '%'];
                        break;
                    case 'start_time':
                        $searchCondition[] = ['created_at','>=', date('Y-m-d H:i:s', $value)];
                        break;
                    case 'end_time':
                        $searchCondition[] = ['created_at','<=', date('Y-m-d H:i:s', $value)];
                        break;
                    default:
                        $searchCondition[] = [$k, $value];
                        break;
                }

            }
        }

        $data = self::where($searchCondition)->paginate($n, $fields, 'p', $p);

      $result = self::getPageData($data);

        return $result;
    }

    protected static function getPageData($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            $data = $data->toArray();
        } else {
            throw new Exception('非法的分页数据@500', 9004);
        }

        $rs['rows'] = $data['data'];
        $rs['total'] = $data['total'];

        return $rs;
    }
}
