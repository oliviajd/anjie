<?php

namespace App\Http\Models\table;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Models\table\CarFiles;

class CarSource extends Model
{
    protected $table = 'car_source';
    public $timestamps = false;
    protected $primaryKey = 'id';

    public static function getSource($ids, $fields = ['id', 'new_photo as photo', 'brandName', 'modelName', 'styleName', 'kmNum', 'price', 'firstRegDate'])
    {
        $data = self::select($fields)->whereIn('id', $ids)->get();
        return $data->toArray();
    }

    public static function listSource($p, $n, $param, $col, $fields = ['id', 'new_photo as photo', 'brandName', 'modelName', 'styleName', 'kmNum', 'price', 'firstRegDate'])
    {
        $searchCondition = [];
        $result = self::where($searchCondition);

        if ($param['name']) {
            $result->where(function ($query) use ($param) {
                $query->where('brandName', 'like', '%' . $param['name'] . '%')
                    ->orWhere('modelName', 'like', '%' . $param['name'] . '%');
            });
            unset($param['name']);
        }

        if ($param['emissionStandard']) {
            $result->whereIn('emission_standard', $param['emissionStandard']);
            unset($param['emissionStandard']);
        }

        if ($param['carType']) {
            $result->whereIn('levelId', $param['carType']);
            unset($param['carType']);
        }

        foreach ($param as $k => $value) {
            if ($value !== 0 && $value !== '' && $value !== null && $value !== []) {
                switch ($k) {
                    case 'city':
                        $searchCondition[] = ['code', $value];
                        break;
                    case 'minPrice':
                        $searchCondition[] = ['price', '>=', $value * 10000];
                        break;
                    case 'maxPrice':
                        $searchCondition[] = ['price', '<=', $value * 10000];
                        break;
                    case 'minOld':
                        $searchCondition[] = ['firstRegDate', '<=', strtotime('-' . $value . ' year') * 1000];
                        break;
                    case 'maxOld':
                        $searchCondition[] = ['firstRegDate', '>=', strtotime('-' . $value . ' year') * 1000];
                        break;
                    case 'minKmNum':
                        $searchCondition[] = ['kmNum', '>=', $value * 10000];
                        break;
                    case 'maxKmNum':
                        $searchCondition[] = ['kmNum', '<=', $value * 10000];
                        break;
                    case 'theme':
                        if ($value == 1) {
                            $searchCondition[] = ['is_new', 1];
                        } else {
                            $searchCondition[] = ['less_mileage', 1];
                        }

                        break;
                    default:
                        $searchCondition[] = [$k, $value];
                        break;
                }

            }
        }

        $result->where($searchCondition);

        switch ($col) {
            case 1:
                $col = 'price';
                $order = 'desc';
                break;
            case 2:
                $col = 'price';
                $order = 'asc';
                break;
            case 3:
                $col = 'kmNum';
                $order = 'asc';
                break;
            default:
                $col = 'id';
                $order = 'desc';
                break;
        }


        $data = $result->orderBy($col, $order)->paginate($n, $fields, 'p', $p);

        list($total, $result) = self::getPageData($data);

        if(count($result) == 0){
            return ['rows' => $result, 'total' => $total];
        }

        foreach ($result as $k => $v) {
            $result[$k]['firstRegDate'] = date('Y-m', ($v['firstRegDate'] / 1000));
            $result[$k]['price'] = sprintf("%.2f", $v['price'] / 10000);
            $result[$k]['kmNum'] = sprintf("%.2f", $v['kmNum'] / 10000);
        }

        return ['rows' => $result, 'total' => $total];
    }

    public static function getRund()
    {
        $post = self::select(['id', 'new_photo as photo', 'brandName', 'modelName', 'styleName', 'kmNum', 'price', 'firstRegDate'])
            ->orderBy(DB::raw('RAND()'))
            ->take(5)
            ->get();

        return $post->toArray();
    }

    public function carModel()
    {
        return $this->hasOne('app\Http\Models\table\CarModel', 'brand_id');
    }

    public function carFile()
    {
        return $this->hasMany(\App\Http\Models\table\CarFiles::class, 'carId');
    }

    protected static function getPageData($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            $data = $data->toArray();
        } else {
            throw new Exception('非法的分页数据@500', 9004);
        }

        return [$data['total'], $data['data']];
    }

}