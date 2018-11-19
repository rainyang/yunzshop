<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/11/19
 * Time: 10:12
 */

namespace app\backend\modules\member\models;


class MemberParent extends \app\common\models\member\MemberParent
{


    public static function getParentByMemberId($member_id)
    {
        return self::where('member_id', $member_id)->with(['hasOneMember','hasOneFans']);
    }


    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'parent_id');
    }

    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'parent_id');
    }

}