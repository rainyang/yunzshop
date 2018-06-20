<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/26
 * Time: 上午11:32
 */

namespace app\common\models;

use app\common\traits\HasFlowTrait;
use app\common\traits\HasProcessTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class OrderPay
 * @package app\common\models
 * @property int id
 * @property int uid
 * @property int status
 * @property string pay_sn
 * @property int pay_type_id
 * @property Carbon pay_time
 * @property Carbon refund_time
 * @property float amount
 * @property array order_ids
 * @property Collection orders
 * @property PayType payType
 * @property string pay_type_name
 * @property string status_name
 */
class OrderPay extends BaseModel
{
    use HasProcessTrait;

    public $table = 'yz_order_pay';
    protected $guarded = ['id'];
    protected $search_fields = ['pay_sn'];
    protected $casts = ['order_ids' => 'json'];
    protected $dates = ['pay_time', 'refund_time'];
    protected $appends = ['status_name', 'pay_type_name'];
    protected $attributes = [
        'status' => 0,
        'pay_type_id' => 0,
    ];
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_REFUNDED = 2;

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

    public function getStatusNameAttribute()
    {
        return [
            self::STATUS_UNPAID => '未支付',
            self::STATUS_PAID => '已支付',
            self::STATUS_REFUNDED => '已退款',
        ][$this->status];
    }

    public function getPayTypeNameAttribute()
    {
        return $this->payType->name;
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, (new OrderPayOrder)->getTable(), 'order_pay_id', 'order_id');
    }

    public function payType()
    {
        return $this->belongsTo(PayType::class);
    }
}