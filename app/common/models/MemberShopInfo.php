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
}
