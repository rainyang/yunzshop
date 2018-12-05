<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/11/20
 * Time: 22:55
 */

namespace app\backend\modules\member\models;


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

}