<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class Jcr_csr_car extends Model
{
    protected $table = 'jcr_csr_car';
    public $primaryKey = 'id';
    public $timestamps=false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    //添加标的信息
    public static function addCarCsr($params)
    {
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


    public static function getList($p, $n, $params, $fields = '*')
    {
        foreach ($params as $k => $value) {
            if ($value !== 0 && $value !== '' && $value !== null && $value !== []) {
                switch ($k) {
                    case 'city':
                        $searchCondition[] = ['code', $value];
                        break;

                    default:
                        $searchCondition[] = [$k, $value];
                        break;
                }

            }
        }

        $result = self::where($searchCondition);

        $data = $result->paginate($n, $fields, 'p', $p);

        $rs = self::getPageData($data);

        return $rs;
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
