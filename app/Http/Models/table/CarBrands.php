<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;

class CarBrands extends Model
{
    protected $table = 'car_brands';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    public function getBrands()
    {
//        $sql = "select car_brands.id AS brandsId, car_brands.brands_cn, car_model.name, car_model.factory, car_model.icon, car_model.id AS modelId from car_brands left join car_model on car_brands.id = car_model.brand_id";
//        $rs = $this->_pdo->fetchAll($sql, array());
        $result = self::select(['id AS brandsId', 'brands_cn'])->get();

        $result = $result->toArray();

        foreach ($result as $k => $v) {
            $model = CarModel::getModelByBrandId($v['brandsId']);
            $new_model = [];
            $factory = '';
            foreach ($model as $key => $value) {
                if ($factory != $value['factory']) {
                    $factory = $value['factory'];
                }
                $new_model[$factory][] = ['modelId' => $value['modelId'], 'name' => $value['name']];
                $result[$k]['icon'] = $value['icon'];
            }
            $result[$k]['model'] = $new_model;
        }


        return $result;
    }


    public function carModel()
    {
        return $this->hasOne('app\Http\Models\table\CarModel', 'brand_id');
    }

}