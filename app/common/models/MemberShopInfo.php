<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午4:18
 */

namespace app\common\models;


use app\backend\models\BackendModel;

class MemberShopInfo extends BackendModel
{
    protected $table = 'yz_member';

    public $timestamps = false;

    public $primaryKey = 'member_id';

    /**
     * 获取用户信息
     *
     * @param $memberId
     * @return mixed
     */
    public static function getMemberShopInfo($memberId)
    {
        return self::select('*')->where('member_id', $memberId)
            ->uniacid()
            ->first(1);
    }

    /**
     * 获取我的下线
     *
     * @return mixed
     */
    public static function getAgentCount()
    {
        return self::uniacid()
             ->where('parent_id', \YunShop::app()->getMemberId())
             ->where('is_black', 0)
             ->count();
    }

    /**
     * 获取指定推荐人的下线
     *
     * @param $uids
     * @return mixed
     */
    public static function getAgentAllCount($uids)
    {
        return self::selectRaw('parent_id, count(member_id) as total')
            ->uniacid()
            ->whereIn('parent_id', $uids)
            ->where('is_black', 0)
            ->groupBy('parent_id')
            ->get();
    }

    public function hasManySelf()
    {
        return $this->hasMany('app\common\models\MemberShopInfo', 'parent_id', 'member_id');
    }

    public function hasOnePreSelf()
    {
        return $this->hasOne('app\common\models\MemberShopInfo', 'member_id', 'parent_id');
    }

    /**
     * 用户是否为黑名单用户
     *
     * @param $member_id
     * @return bool
     */
    public static function isBlack($member_id)
    {
        $member_model = self::getMemberShopInfo($member_id);

        if (1 == $member_model->is_black) {
            return true;
        } else {
            return false;
        }
    }

}
