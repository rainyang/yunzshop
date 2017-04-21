<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/6
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\models;


use app\common\models\OrderGoods;
use app\frontend\models\Member;

class Order extends \app\common\models\Order
{
    protected $hidden = [
        'uniacid',
        'create_time',
        'is_deleted',
        'is_member_deleted',
        'finish_time',
        'pay_time',
        ',send_time',
        'send_time',
        'uid',
        'cancel_time',
        'created_at',
        'updated_at',
        'deleted_at'
    ]; //在 Json 中隐藏的字段
    protected $appends = ['status_name', 'pay_type_name', 'button_models'];

    public function belongsToMember()
    {
        return $this->belongsTo(Member::class, 'uid', 'uid');
    }

    public function belongsToOrderGoods()
    {
        return $this->belongsTo(OrderGoods::class, 'id', 'order_id');
    }

    public function scopeOrders($query)
    {
        return $query->with('hasManyOrderGoods');
    }

    public function scopeGetOrderCountGroupByStatus($query, $status = [])
    {
        $query->where('uid', \YunShop::app()->getMemberId());
        return parent::scopeGetOrderCountGroupByStatus($query, $status);
    }

    public function orderGoodsBuilder($status)
    {
        return function ($query) use ($status) {
            return $query->where('comment_status', $status);
        };
    }

    public static function getMyCommentList($uid, $status)
    {
        return Order::with([
            'hasManyOrderGoods' => self::orderGoodsBuilder($status)
        ])->where('uid', $uid)->where('comment_status', $status)->get();
    }
}