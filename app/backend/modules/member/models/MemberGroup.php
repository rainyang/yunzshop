<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 下午6:04
 */

namespace app\backend\modules\member\models;


class MemberGroup extends \app\common\models\MemberGroup
{
    /**
     * 获取会员分组列表
     * @Author::yitian 2017-02-23 qq:751818588
     * @access public
     * @param int $uniacid 公众号id
     *
     * @return object
     **/
    public static function getMemberGroupList($uniacid)
    {
        $group_list = MemberGroup::where('uniacid', $uniacid)->get();
        return $group_list;
    }
}
