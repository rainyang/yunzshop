<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 上午11:01
 */

namespace app\common\models;


class OrderAddress extends BaseModel
{
    public $table = 'yz_order_address';
    protected $guarded = ['id'];
    public $province;
    public $city;
    public $district;
    /**
     *  定义字段名
     * 可使
     * @return array */
    public function atributeNames() {
        return [
            'address'=> '收货详细地址',
            'mobile'=> '收货电话',
            'realname'=> '收货人姓名',
            'province_id'=> '收货省份',
            'city_id'=> '收货城市',
            'district_id'=> '收货地区',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {

        $rule =  [
            //具体unique可看文档 https://laravel.com/docs/5.4/validation#rule-unique
            'address'=> 'required',
            'mobile'=> 'required',
            'realname'=> 'required',
            'province_id'=> 'required',
            'city_id'=> 'required',
            'district_id'=> 'required',
        ];

        return $rule;
    }
}