<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/11/20
 * Time: 22:55
 */

namespace app\backend\modules\member\models;


use app\backend\modules\charts\models\Order;
use app\backend\modules\charts\modules\team\models\MemberMonthOrder;
use Illuminate\Support\Facades\DB;

class MemberChildren extends \app\common\models\member\MemberChildren
{
    public function scopeChildren($query, $request)
    {
        $query->where('member_id', $request->id)->with([
            'hasOneMember' => function($q) {
                $q->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime', 'credit1', 'credit2']);
            },
            'hasOneFans',
            'hasOneChild' => function($q) {
                $q->selectRaw('count(child_id) as first, member_id')->where('level', 1)->groupBy('member_id');
            }
        ]);

        if ($request->level) {
            $query->where('level', $request->level);
        } else {
            $query->where('level', 1);
        }

        if ($request->member_id) {
            $query->where('parent_id', $request->member_id);
        }

        if ($request->member) {
            $query->whereHas('hasOneMember', function ($q) use ($request) {
                $q->searchLike($request->member);
            });
        }

        if ($request->followed != '') {
            $query->whereHas('hasOneFans', function ($q) use ($request) {
                $q->where('follow', $request->followed);
            });
        }
        return $query;

    }

    public static function getTeamCount()
    {
        $teamModel=self::uniacid();

        if (!empty($search['member'])) {
            $teamModel->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }
        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $teamModel->whereBetween('created_at', $range);
            }
        }
        $teamModel->with([
            'hasOneChild' => function($q) {
                $q->selectRaw('count(child_id) as first, member_id')->where('level', 1)->groupBy('member_id');
            }]);

        return $teamModel;

    /*    $model = static::uniacid();
        $model->select('yz_member_children.member_id, yz_member_children.level,yz_member_children.child_id, count(yz_order.price)');
        $model->join('yz_order', function ($join){
            $join->on('yz_member_children.child_id', '=', 'yz_order.uid');
        });
        $model->groupBy('yz_member_children.member_id');
        $model->where(function ($where) {
            return $where->orWhere('yz_member_children.level', 1)->orWhere('yz_member_children.level', 2);
        });
        return $model;*/
    }

    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'child_id');
    }

    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'child_id');
    }

    public function hasOneChild()
    {
        return $this->hasOne(self::class, 'member_id', 'child_id');
    }

    public function hasManyOrder()
    {
        return $this->hasMany('\app\common\models\Order','uid','child_id');
    }

    public function hasManyMonth()
    {
        return $this->hasMany('\app\common\models\member\MemberMonthOrder','member_id','child_id');
    }

}