<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午4:16
 */

namespace app\backend\modules\member\models;



class MemberShopInfo extends \app\common\models\MemberShopInfo
{
    public function group()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberGroup');
    }

    public function level()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberLevel');
    }

    public function agent()
    {
        return $this->belongsTo('app\backend\modules\member\models\Member', 'agent_id', 'uid');
    }

    /**
     * 更新会员信息
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public static function updateMemberInfoById($data, $id)
    {
        return self::uniacid()
            ->where('member_id', $id)
            ->update($data);
    }

    /**
     * 删除会员信息
     *
     * @param $id
     */
    public static function  deleteMemberInfoById($id)
    {
        return self::uniacid()
            ->where('member_id', $id)
            ->delete();
    }

    /**
     * 设置会员黑名单
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public static function setMemberBlack($id, $data)
    {
        return self::uniacid()
            ->where('member_id', $id)
            ->update($data);
    }
}
