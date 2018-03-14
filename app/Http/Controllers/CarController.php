<?php

namespace App\Http\Controllers;

use App\Http\Models\business\Car;
use App\Http\Models\common\Constant;
use App\Http\Models\business\System;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/9/11
 * Time: 15:16
 */
class CarController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_car = new Car();
        $this->_system = new System();
    }

    public function getBrands()
    {
        $rs = $this->_car->getBrands();

        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    public function getCarLists(Request $request)
    {
        $p = $request->input('p', 1);
        $n = $request->input('n', 10);
        $col = $request->input('col', 4);
        $param['maxPrice'] = $request->input('maxPrice', 0);
        $param['minPrice'] = $request->input('minPrice', 0);
        $param['minOld'] = $request->input('min_old', 0);
        $param['maxOld'] = $request->input('max_old', 0);
        $param['city'] = $request->input('city', 0);
        $param['carType'] = $request->input('car_type', []);
        $param['minKmNum'] = $request->input('minKmNum', 0);
        $param['maxKmNum'] = $request->input('maxKmNum', 0);
        $param['emissionStandard'] = $request->input('emissionStandard', []);
        $param['color'] = $request->input('color', 0);
        $param['name'] = $request->input('name');
        $param['theme'] = $request->input('theme',0);
        $param['modelId'] = $request->input('model_id',0);
        $param['brandId'] = $request->input('brand_id',0);

        $result = $this->_car->getCarLists($p, $n, $param, $col);

        if ($result) {
            return $this->_common->output($result, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }

    }

    public function getCarDetail(Request $request)
    {
        $id = $request->input('id', 1);

        $rs = $this->_car->getCarDetail($id);

        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }

    public function getCarRecommend()
    {
        $rs = $this->_car->getRecommend();

        if ($rs !== false) {
            return $this->_common->output($rs, Constant::ERR_SUCCESS_NO, Constant::ERR_SUCCESS_MSG);    //成功
        } else {
            return $this->_common->output(false, Constant::ERR_FAILED_NO, Constant::ERR_FAILED_MSG);    //如果返回的是false
        }
    }


}