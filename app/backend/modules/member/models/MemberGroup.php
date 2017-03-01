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
    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];
    /**
     *  获取会员组信息通过会员组ID
     * @Author::yitian 2017-02-03 qq:751818588
     * @access public static
     *
     * @param int $groupId
     *
     * @return array
     * */
    public static function getMemberGroupByGroupID($groupId)
    {
        $group = MemberGroup::where('id', $groupId)->first()->toArray();

        return $group;
    }
    /**
     * 获取当前公众号会员分组列表
     * @Author::yitian 2017-02-23 qq:751818588
     * @access public static
     * @param int $uniacid 公众号id
     *
     * @return object
     **/
    public static function getMemberGroupList($uniacid)
    {
        $group_list = MemberGroup::where('uniacid', $uniacid)->get();
        return $group_list;
    }
    /**
     * 添加会员分组列表
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public static
     * @param array $data 会员组信息
     *
     * @return int $result
     **/
    public static function createMemberGroup($data)
    {
        $result = static::insert($data);
        return $result;
    }
    /**
     * 修改会员分组列表通过会员组ID
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public static
     * @param int $group_id 会员组ID
     *
     * @return
     **/
    public static function updateMemberGroupNameByGroupId($groupId, $groupName)
    {
        $result = static::where('id', $groupId)->update(['group_name' => $groupName]);
        return $result;
    }
    /**
     * 删除会员分组列表
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public static
     * @param int $group_id 会员组ID
     *
     * @return
     **/
    public static function deleteMemberGroup($group_id)
    {
        $status = static::where('id', $group_id)->delete();
        return $status;
    }
}
