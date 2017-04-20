<?php

namespace app\backend\modules\coupon\models;


class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    //类型转换
    protected $casts = [
        'goods_ids' => 'json',
        'category_ids' => 'json',
        'goods_names' => 'json',
        'categorynames' => 'json',
    ];

    //默认值
    protected $attributes = [
        'goods_ids' => '[]',
        'category_ids' => '[]',
        'display_order' => 0,
    ];

    /**
     *  定义字段名
     * 可使
     * @return array */
    public function atributeNames() {
        return [
            'display_order'=> '排序',
            'name'=> '优惠券名称',
            'enough'=> '使用条件(消费金额前提)',
            'time_days'=> '使用时间限制',
            'deduct'=> '立减',
            'discount'=> '折扣',
            'get_max'=> '每个人的限领数量',
            'credit'=> '领取时消耗的积分',
            'money'=> '领取时消耗的余额',
            'total' => '发放总数',
            'resp_title' => '推送标题',
            'resp_desc' => '推送说明',
            'resp_url' => '推送链接',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {
        return [
            'display_order' => 'nullable|integer',
            'name' => 'required',
            'enough' => 'nullable|integer',
            'time_days' => 'nullable|integer',
            'deduct' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'get_max' => 'nullable|numeric',
            'credit' => 'nullable|integer',
            'money' => 'nullable|numeric',
            'total' => 'nullable|integer',
            'resp_title' => 'nullable|string',
            'resp_desc' => 'nullable|string',
            'resp_url' => 'nullable|url',
        ];
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public static function getCouponsByName($keyword)
    {
        return static::uniacid()->select('id', 'name')
            ->where('name', 'like', '%' . $keyword . '%')
            ->get();
    }
}
