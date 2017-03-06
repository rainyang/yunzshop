<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午1:55
 */

namespace app\backend\modules\member\models;

class Member extends \app\common\models\Member
{

    /**
     * 主从表1:1
     *
     * @return mixed
     */
    public function yzMember()
    {
        return $this->hasOne('app\backend\modules\member\models\MemberShopInfo','member_id','uid');
    }

    /**
     * 获取会员列表
     *
     * @param $pageSize
     * @return mixed
     */
    public static function getMembers($pageSize)
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->with(['yzMember'=>function($query){
                return $query->select(['member_id','agent_id', 'is_agent', 'group_id','level_id', 'is_black'])
                    ->with(['group'=>function($query1){
                        return $query1->select(['id','group_name']);
                    },'level'=>function($query2){
                        return $query2->select(['id','level_name']);
                    }, 'agent'=>function($query3){
                        return $query3->select(['uid', 'avatar', 'nickname']);
                    }]);
            }, 'hasOneFans' => function($query4) {
                return $query4->select(['uid', 'follow as followed']);
            }])
            ->paginate($pageSize)
            ->toArray();
    }

    /**
     * 会员－订单一对一关系
     *
     * @return mixed
     */
    public function hasOneOrder()
    {
        //return $this->hasOne('app\backend\modules\order\models\order','member_id','uid');
    }

    /**
     * 会员－粉丝一对一关系
     *
     * @return mixed
     */
    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans','uid','uid');
    }

    /**
     * 获取会员信息
     *
     * @param $id
     * @return mixed
     */
    public static function getMemberInfoById($id)
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->where('uid', $id)
            ->with(['yzMember'=>function($query){
                return $query->select(['member_id','agent_id', 'is_agent', 'group_id','level_id', 'is_black', 'alipayname', 'alipay', 'content']);
            }, 'hasOneFans' => function($query2) {
                return $query2->select(['uid', 'follow as followed']);
            }
            ])
            ->first()
            ->toArray();
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
            ->where('uid', $id)
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
               ->where('uid', $id)
               ->delete();
    }

    /**
     * 检索会员信息
     *
     * @param $pageSize
     * @return mixed
     */
    public static function searchMembers($pageSize, $parame)
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->where('uid', $parame['mid'])
            ->where('nickname', 'like', '%' . $parame['realname'] . '%')
            ->orWhere('realname', 'like', '%' . $parame['realname'] . '%')
            ->orWhere('mobile', 'like', $parame['realname'] . '%')
            ->whereHas('yzMember', function($q) use ($parame){
                $q->where('group_id', $parame['groupid'])->where('level_id',$parame['level'])->where('is_black', $parame['isblack']);

            })
            ->whereHas('hasOneFans', function ($q2) use ($parame) {
                $q2->where('follow', $parame['followed']);
            })
            ->with(['yzMember'=>function($query){
                return $query->select(['member_id','agent_id', 'is_agent', 'group_id','level_id', 'is_black'])
                    ->with(['group'=>function($query1){
                        return $query1->select(['id','group_name']);
                    },'level'=>function($query2){
                        return $query2->select(['id','level_name']);
                    }, 'agent'=>function($query3){
                        return $query3->select(['uid', 'avatar', 'nickname']);
                    }]);
            }, 'hasOneFans' => function($query4) {
                return $query4->select(['uid', 'follow as followed']);
            }])
            ->paginate($pageSize)
            ->toArray();
    }
}