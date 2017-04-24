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
        $operator = [];
        if ($status == 0) {
            $operator['operator'] = '=';
            $operator['status'] = 0;
        } else {
            $operator['operator'] = '>';
            $operator['status'] = 0;
        }
        return function ($query) use ($operator) {
            return $query->with('hasOneComment')->where('comment_status', $operator['operator'], $operator['status']);
        };
    }

    public static function getMyCommentList($uid, $status)
    {
        $operator = [];
        if ($status == 0) {
            $operator['operator'] = '=';
            $operator['status'] = 0;
        } else {
            $operator['operator'] = '>';
            $operator['status'] = 0;
        }
        return self::whereHas('hasManyOrderGoods', function($query) use ($operator){
            return $query->where('comment_status', $operator['operator'], $operator['status']);
        })
            ->with([
                'hasManyOrderGoods' => self::orderGoodsBuilder($status)
            ])->where('uid', $uid)->orderBy('id', 'desc')->get();
    }
}