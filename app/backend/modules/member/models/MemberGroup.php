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
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

    /**
     * 获取当前公众号会员分组列表
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
    /**
     * 添加会员分组列表
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     * @param array $data 会员组信息
     *
     * @return int $id
     **/
    public static function createMemberGroup($data)
    {
        $id = static::insert($data);
        return $id;
    }
    /**
     * 修改会员分组列表
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     * @param int $group_id 会员组ID
     *
     * @return
     **/
    public function updateMemberGroup($group_id)
    {

    }
    /**
     * 删除会员分组列表
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     * @param int $group_id 会员组ID
     *
     * @return
     **/
    public static function deleteMemberGroup($group_id)
    {
        $id = static::where('id', $group_id)->delete();
        return $id;
    }
}
