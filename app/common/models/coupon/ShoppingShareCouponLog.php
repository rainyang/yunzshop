<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 10:50
 */

namespace app\common\models\coupon;


use app\common\models\BaseModel;
use app\common\models\Member;
use app\framework\Database\Eloquent\Builder;

class ShoppingShareCouponLog extends BaseModel
{
    public $table = 'yz_shopping_share_coupon_log';

    protected $guarded = ['id'];


    public static function getList($search)
    {
        $model = self::uniacid();
        if ($search['coupon_name']) {
            $model->where('coupon_name', $search['coupon_name']);
        }

        if ($search['share_uid']) {
            $model->shareUid($search['share_uid']);
        }

        if ($search['share_uid']) {
            $model->receiveUid($search['share_uid']);
        }


        if ($search['share_name']) {
            $model->whereHas('shareMember', function ($member) use ($search) {
                $member = $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['share_name'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['share_name'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['share_name'] . '%');
                return $member;
            });
        }

        if ($search['receive_name']) {
            $model->whereHas('receiveMember', function ($member) use ($search) {
                $member = $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['receive_name'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['receive_name'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['receive_name'] . '%');
                return $member;
            });
        }


        if ($search['time_search']) {
            $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
            $model->whereBetween('created_at', $range);
        }

        $model->with('shareMember', 'receiveMember');

        return $model;
    }


    //分享者
    public function scopeShareUid(Builder $query, $uid)
    {
        return $query->where('share_uid', $uid);
    }

    //领取者
    public function scopeReceiveUid(Builder $query, $uid)
    {
        return $query->where('receive_uid', $uid);
    }


    public function shareMember()
    {
        return $this->belongsTo(Member::class, 'share_uid', 'uid');
    }

    public function receiveMember()
    {
        return $this->belongsTo(Member::class, 'receive_uid', 'uid');
    }
}