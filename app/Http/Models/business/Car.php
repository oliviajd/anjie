<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/9/11
 * Time: 15:37
 */

namespace App\Http\Models\business;

use App\Http\Models\table\CarBrands;
use App\Http\Models\table\CarRecommend;
use App\Http\Models\table\CarSource;
use App\Http\Models\common\Common;
use App\Http\Models\table\T_address_city;
use App\Http\Models\table\T_address_province;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{

    protected $_carbrands = null;
    protected $_t_address_province = null;
    protected $_t_address_city = null;
    protected $_common = null;

    public function __construct()
    {
        parent::__construct();
        $this->_common = new Common();
        $this->_carbrands = new CarBrands();
        $this->_t_address_province = new T_address_province();
        $this->_t_address_city = new T_address_city();
    }

    public function getBrands()
    {
        $result = $this->_carbrands->getBrands();

        return $result;
    }

    public function getCarLists($p, $n, $param, $col)
    {

        $result = CarSource::listSource($p, $n, $param, $col);

        return $result;
    }

    public function getRecommend()
    {
        $rem = CarRecommend::getRecommend();
        if ($rem) {
            $recommend_id = array_column($rem, 'recommend_id');
            $result = CarSource::getSource($recommend_id);
            foreach ($result as $k => $v) {
                $result[$k]['firstRegDate'] = date('Y-m', ($v['firstRegDate'] / 1000));
                $result[$k]['price'] = sprintf("%.2f", $v['price'] / 10000);
                $result[$k]['kmNum'] = sprintf("%.2f", $v['kmNum'] / 10000);
            }
        } else {
            CarRecommend::deleteRecommend();
            $result = CarSource::getRund();
            foreach ($result as $k => $v) {
                $result[$k]['firstRegDate'] = date('Y-m', ($v['firstRegDate'] / 1000));
                $result[$k]['price'] = sprintf("%.2f", $v['price'] / 10000);
                $result[$k]['kmNum'] = sprintf("%.2f", $v['kmNum'] / 10000);
                CarRecommend::insert(['recommend_id' => $v['id']]);
            }
        }

        return $result;

    }

    public function getCarDetail($id)
    {
        $res = CarSource::find($id);

        if (!$res) {
            return false;
        }

        $res->carFile;
        $data = $res->toArray();

        $result['brandName'] = $data['brandName'];
        $result['modelName'] = $data['modelName'];
        $result['styleName'] = $data['styleName'];
        $result['firstRegDate'] = date('Y-m', ($data['firstRegDate'] / 1000));
        $result['price'] = sprintf("%.2f", $data['price'] / 10000);
        $result['kmNum'] = sprintf("%.2f", $data['kmNum'] / 10000);
        $result['originalPrice'] = sprintf("%.2f", $data['originalPrice'] / 10000);
        $result['gearbox'] = $data['gearbox'];
        $result['emission_standard'] = $data['emission_standard'];
        $result['color'] = $data['color'];
        $result['content'] = $data['content'];
        $result['cc'] = $data['cc'];
        $result['code'] = $data['code'];
        $city = $this->_t_address_city->getCityByCode($result['code']);
        $result['city'] = $city['name'];
        $province = $this->_t_address_province->getProvinceByCode($city['provinceCode']);
        $result['province'] = $province['name'];


        foreach ($data['car_file'] as $v) {
            $file[] = $v['new_file'];
        }
        $result['photo'] = $file;

        return $result;

    }

}