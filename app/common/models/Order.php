<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;


use app\frontend\modules\order\services\status\StatusServiceFactory;
use Illuminate\Support\Facades\Schema;

class Order extends BaseModel
{
    public $table = 'yz_order';
    private $StatusService;
    protected $appends = ['status_name', 'button_models'];
    public static function getOrder($order_id, $uniacid)
    {
        return self::where('id', $order_id)
            ->where('uniacid', $uniacid)
            ->first()
            ->toArray();
    }


    /**
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeWhereForSearch($query,$params)
    {
        $searchable = ['id','member_id','order_sn'];
        $time_ranges = ['create_time','finish_time','pay_time','send_time','cancel_time'];
        foreach ($params as $key => $param){
            if(!in_array($key,$searchable)){
                continue;
            }
            $query->where($key,'like','%'.$param.'%');
        }
        foreach ($params as $key => $param){
            if(!in_array($key,$time_ranges)){
                continue;
            }
            $query->whereBetween($key,$param);
        }
        return $query;
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

    public function hasOneOrderDispatch()
    {
        return $this->hasOne('\app\common\models\OrderDispatch', 'order_id', 'id');
    }
    //订单评价
    public function hasOneOrderRemark()
    {
        return $this->hasOne('\app\common\models\order\Remark', 'order_id', 'id');
    }

    public function getStatusService()
    {
        if (!isset($this->StatusService)) {
            $this->StatusService = StatusServiceFactory::createStatusService($this);
        }
        return $this->StatusService;
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