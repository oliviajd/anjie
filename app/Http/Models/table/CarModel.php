<?php

namespace App\Http\Models\table;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'car_model';
    public $timestamps = false;
    protected $primaryKey = 'id';

    public static function getModelByBrandId($brandId)
    {
        $result = self::where('brand_id',$brandId)->select(['id AS modelId', 'name', 'factory', 'icon', 'name'])->get();

        return $result->toArray();

    }


}