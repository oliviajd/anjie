<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $customer_name 客户姓名
 * @property $customer_telephone 客户电话
 * @property $customer_user_phone 按揭联系人
 * @property $customer_car_id 垫资车辆的id
 * @property $customer_order_num 融资单号
 * @property $customer_status 处理状态
 * @property $add_time 申请时间
 * @property $product_brand 车辆品牌
 * @property $product_price 指导价格
 * @property $product 是否选择了车辆
 * @property $work_id 工作流id
 * @property $user_id 提交人的用户id
 * @property $user_type 类型
 * @property $to_user_id 匹配业务员的id
 * @property $product_name 产品类型
 * @property $merchant_name 客户来源
 */

class Jcr_apply extends Model
{
    protected  $table='jcr_apply';
    public $primaryKey='id';
    public $timestamps=false;
}
