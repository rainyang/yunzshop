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

    public $timestamps = false;
    public $guarded = [''];
    /**
     *  Get membership information through member group ID
     *
     * @param int $groupId
     *
     * @return array
     * */
    public static function getMemberGroupByGroupID($groupId)
    {
        return  MemberGroup::where('id', $groupId)->first()->toArray();
    }
    /**
     * Get a list of members of the current public number
     *
     * @param int $uniacid
     *
     * @return array
     **/
    public static function getMemberGroupList($uniacid)
    {
        return  MemberGroup::where('uniacid', $uniacid)->get()->toArray();
    }
    /**
     * Add member list
     *
     * @param array $data
     *
     * @return 1 or 0
     **/
    public static function createMemberGroup($data)
    {
        return  static::insert($data);
    }
    /**
     * Modify membership list by member group ID
     *
     * @param int $groupId
     *
     * @return 1 or 0
     **/
    public static function updateMemberGroupNameByGroupId($groupId, $groupName)
    {
        return  static::where('id', $groupId)->update(['group_name' => $groupName]);
    }
    /**
     * Delete member list
     *
     * @param int $groupId
     *
     * @return 1 or 0
     **/
    public static function deleteMemberGroup($groupId)
    {
        return static::where('id', $groupId)->delete();
    }
}
