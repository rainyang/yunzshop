<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 下午6:01
 */

namespace app\common\models;



class MemberGroup extends BaseModel
{
    protected $table = 'yz_member_group';
    /**
     * Get member group information by groupId
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    protected static function getMemberGroupByGroupID($groupId)
    {
        return static::where('id', $groupId)->first(1)->toArray();
    }

}
