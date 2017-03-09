<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;


use app\frontend\modules\order\services\status\StatusServiceFactory;

class Order extends BaseModel
{
    public $table = 'yz_order';
    private $StatusService;
    protected $appends = ['status_name', 'button_models'];
    protected $search_fields = ['id', 'order_sn'];

    public static function getOrder($order_id, $uniacid)
    {
        return self::where('id', $order_id)
            ->where('uniacid', $uniacid)
            ->first()
            ->toArray();
    }

    public function scopeWaitPay($query)
    {
        //AND o.status = 0 and o.paytype<>3
        return $query->where(['status' => 0]);
    }

    public function scopeWaitSend($query)
    {
        //AND ( o.status = 1 or (o.status=0 and o.paytype=3) )
        return $query->where(['status' => 1]);
    }

    public function scopeWaitReceive($query)
    {
        return $query->where(['status' => 2]);
    }

    public function scopeCompleted($query)
    {
        return $query->where(['status' => 3]);
    }

    public function scopeCancelled($query)
    {
        return $query->where(['status' => -1]);
    }

    public function hasManyOrderGoods()
    {
        return $this->hasMany('\app\common\models\OrderGoods', 'order_id', 'id');
    }

    public function belongsToMember()
    {
        return $this->belongsTo('\app\common\models\Member', 'member_id', 'uid');
    }
    //订单配送方式
    public function hasOneDispatchType()
    {
        return $this->hasOne('\app\common\models\DispatchType', 'id', 'dispatch_type_id');
    }

    //订单备注
    public function hasOneOrderRemark()
    {
        return $this->hasOne('\app\common\models\order\Remark', 'order_id', 'id');
    }
    public function hasOnePayType()
    {
        return $this->hasOne('\app\common\models\PayType', 'id', 'pay_type_id');
    }

    //订单快递
    public function hasOneOrderExpress()
    {
        return $this->hasOne('\app\common\models\order\Express', 'order_id', 'id');
    }

    public function getStatusService()
    {
        if (!isset($this->StatusService)) {
            $this->StatusService = StatusServiceFactory::createStatusService($this);
        }
        return $this->StatusService;
    }

    public function hasOneAddress()
    {
        return $this->hasOne('\app\common\models\order\Address', 'order_id', 'id');
    }

    public function getStatusNameAttribute()
    {
        return $this->getStatusService()->getStatusName();
    }

    public function getButtonModelsAttribute()
    {
        return $this->getStatusService()->getButtonModels();
    }

}