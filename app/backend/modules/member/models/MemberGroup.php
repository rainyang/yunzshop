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

    //public $timestamps = false;
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
        return  MemberGroup::where('id', $groupId)->first();
    }
    /**
     * Get a list of members of the current public number
     *
     * @param int $uniacid
     *
     * @return array
     **/
    public static function getMemberGroupList()
    {
        $memberGroup = MemberGroup::select('id', 'group_name', 'uniacid')
            ->uniacid()
            ->with(['member' => function($query){
                return $query->select(['group_id'])->where('uniacid', \YunShop::app()->uniacid);
            }])
            ->get()
            ->toArray();
        return $memberGroup;
        //return  MemberGroup::where('uniacid', $uniacid)->get()->toArray();
    }

    public function member()
    {
        return $this->hasMany('app\backend\modules\member\models\MemberShopInfo','group_id','id');
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
