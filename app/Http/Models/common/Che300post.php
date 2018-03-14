<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;


if (getenv('APP_ENV') == 'local') {
    define('CHE300_SERVICE_URL', env('CHE300_SERVICE_URL'));
    define('CHE300_TOKEN', env('CHE_TOKEN'));
} elseif (getenv('APP_ENV') == 'testing') {
    define('CHE300_SERVICE_URL', env('CHE300_SERVICE_URL'));
    define('CHE300_TOKEN', env('CHE_TOKEN'));
} elseif (getenv('APP_ENV') == 'production') {
    define('CHE300_SERVICE_URL', env('CHE300_SERVICE_URL'));
    define('CHE300_TOKEN', env('CHE_TOKEN'));
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 车300的接口
 *
 * @author win7
 */
class Che300post
{

    /**
     * 城市列表
     * @param 
     * </p>
     * @return 
     * 
     */
    public function getAllCity() 
    {
        $param = array(
            'token' => CHE300_TOKEN,
        );
        $r = curl_post_che300(CHE300_SERVICE_URL . '/getAllCity', $param);
        return $r;
    }
    /**
     * 品牌列表
     * @param 
     * </p>
     * @return 
     * 
     */
    public function getCarBrandList() 
    {
        $param = array(
            'token' => CHE300_TOKEN,
        );
        $r = curl_post_che300(CHE300_SERVICE_URL . '/getCarBrandList', $param);
        return $r;
    }
    /**
     * 车系列表
     * @param 
     * </p>
     * @return 
     * 
     */
    public function getCarSeriesList($brandId) 
    {
        $param = array(
            'token' => CHE300_TOKEN,
            'brandId' => $brandId,
        );
        $r = curl_post_che300(CHE300_SERVICE_URL . '/getCarSeriesList', $param);
        return $r;
    }
    /**
     * 车型列表
     * @param 
     * </p>
     * @return 
     * 
     */
    public function getCarModelList($seriesId) 
    {
        $param = array(
            'token' => CHE300_TOKEN,
            'seriesId' => $seriesId,
        );
        $r = curl_post_che300(CHE300_SERVICE_URL . '/getCarModelList', $param);
        return $r;
    }
    /**
     * 基于VIN码获取车型
     * @param 
     * </p>
     * @return 
     * 
     */
    public function identifyModelByVIN($vin) 
    {
        $param = array(
            'token' => CHE300_TOKEN,
            'vin' => $vin,
        );
        $r = curl_post_che300(CHE300_SERVICE_URL . '/identifyModelByVIN', $param);
        return $r;
    }
    /**
     * 车辆估值接口
     * @param 
     * </p>
     * @return 
     * 
     */
    public function getUsedCarPrice($modelId, $zone, $regDate, $mile) 
    {
        $param = array(
            'token' => CHE300_TOKEN,
            'modelId' => $modelId,
            'zone' => $zone,
            'regDate' => $regDate,
            'mile' => $mile,
        );
        $r = curl_post_che300(CHE300_SERVICE_URL . '/getUsedCarPrice', $param);
        return $r;
    }
}
function curl_post_che300($post_url, $post_data) 
{
    $post_url = $post_url . '?'. http_build_query($post_data);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $post_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
