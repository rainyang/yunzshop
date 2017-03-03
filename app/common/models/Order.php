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
    public function hasManyOrderGoods()
    {
        return $this->hasMany('\app\common\models\OrderGoods', 'order_id', 'id');
    }
    public function getStatusService(){
        if(!isset($this->StatusService)){
            $this->StatusService = StatusServiceFactory::createStatusService($this->status);
        }
        return $this->StatusService;
    }
    public function getStatusNameAttribute()
    {
        return $this->getStatusService()->getStatusName($this->status);
    }
    public function getButtonModelsAttribute()
    {
        return $this->getStatusService()->getButtonModels($this->status);
    }
}