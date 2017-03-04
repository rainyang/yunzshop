<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午1:55
 */

namespace app\backend\modules\member\models;


class Member extends \app\common\models\Member
{
    public static function getMemberlist()
    {
        $memberList = Member::where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        return $memberList;
    }

    public function yzMember()
    {
        return $this->hasOne('app\backend\modules\member\models\MemberShopInfo','member_id','uid');
    }

    public static function getMembers($pageSize)
    {
        return self::select(['uid','nickname'])
            ->uniacid()
            ->with(['yzMember'=>function($query){
                return $query->select(['member_id','agentid', 'group_id','level_id'])
                    ->with(['group'=>function($query1){
                        return $query1->select(['id','group_name']);
                    },'level'=>function($query2){
                        return $query2->select(['id','level_name']);
                    }, 'agent'=>function($query3){
                        return $query3->select(['uid', 'nickname']);
                    }]);
            }])
            ->paginate($pageSize)
            ->toArray();
    }


}