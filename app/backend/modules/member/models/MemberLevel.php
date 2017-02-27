<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 上午11:22
 */

namespace app\backend\modules\member\models;


class MemberLevel extends \app\common\models\MemberLevel
{
    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];


    public static function getMemberLevelList()
    {
        $uniacid = \YunShop::app()->uniacid;
        $level_list = MemberLevel::where('uniacid', $uniacid)->get();

        return $level_list;
    }

    public static function deleteMemberLevel($id)
    {
        $status = static::where('id', $id)->delete();
        return $status;
    }
    /**
     * 添加会员等级
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param array $data 会员组信息
     *
     * @return int $id
     **/
    public static function createMemberLevel($data)
    {
        $id = static::insert($data);
        return $id;
    }
}