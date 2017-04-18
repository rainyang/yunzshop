<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 下午6:04
 */

namespace app\backend\modules\member\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class MemberGroup extends \app\common\models\MemberGroup
{
    use SoftDeletes;

    public $guarded = [''];

    //关联 member 数据表 一对多
    public function member()
    {
        return $this->hasMany('app\backend\modules\member\models\MemberShopInfo','group_id','id');
    }

    /*
     * 获取会员分页列表 17.3.31 auto::yitian
     *
     * @param int $pageSize
     *
     * @return object */
    public static function getGroupPageList($pageSize)
    {
        //todo 获取分组内会员数量
        return self::uniacid()

            ->paginate($pageSize);
    }

    /**
     *  Get membership information through member group ID
     *
     * @param int $groupId
     *
     * @return array */
    public static function getMemberGroupByGroupId($groupId)
    {
        return  MemberGroup::where('id', $groupId)->first();
    }
    /**
     * Get a list of members of the current public number
     *
     * @param int $uniacid
     *
     * @return array */
    public static function getMemberGroupList()
    {
        $memberGroup = MemberGroup::select('id', 'group_name', 'uniacid')
            ->uniacid()
            ->with(['member' => function($query){
                return $query->select(['member_id','group_id'])->where('uniacid', \YunShop::app()->uniacid);
            }])
            ->get()
            ->toArray();
        return $memberGroup;
    }

    /**
     * 获取指定"GroupId"下的关联用户数据
     * @param $groupId
     * @return mixed
     */
    public static function getMembersByGroupId($groupId)
    {
        $memberGroup = static::uniacid()
                    ->select('id', 'group_name', 'uniacid')
                    ->where('id', '=', $groupId)
                    ->with(['member' => function($query){
                        return $query->select(['member_id','group_id'])->where('uniacid', \YunShop::app()->uniacid);
                    }])
                    ->first();
        return $memberGroup;
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

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'group_name'    => '分组名不能为空',
            'uniacid'  => '数据获取不完整，请刷新重试',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'group_name'    => 'required',
            'uniacid'       => 'required'
        ];
    }
}
