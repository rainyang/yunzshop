<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/26
 * Time: 上午11:32
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class OrderPay
 * @package app\common\models
 * @property int id
 * @property array order_ids
 * @property Collection orders
 * @property int status
 */
class OrderPay extends BaseModel
{
    public $table = 'yz_order_pay';
    protected $guarded = ['id'];
    protected $search_fields = ['pay_sn'];
    protected $casts = ['order_ids' => 'json'];

    /**
     * 根据paysn查询支付方式
     *
     * @param $pay_sn
     * @return mixed
     */
    public function get_paysn_by_pay_type_id($pay_sn)
    {
        return self::select('pay_type_id')
            ->where('pay_sn', $pay_sn)
            ->value('pay_type_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, (new OrderPayOrder)->getTable());
    }
}