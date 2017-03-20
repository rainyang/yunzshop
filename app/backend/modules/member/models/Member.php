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
     * @param $keyWord
     *
     */
    public static function getMemberByName($keyWord)
    {
        return self::where('realname', 'like', $keyWord . '%')
            ->orWhere('nick_name', 'like', $keyWord . '%')
            ->orWhere('mobile', 'like', $keyWord . '%')
            ->get();
    }
    /**
     * 获取会员列表
     *
     * @return mixed
     */
    public static function getMembers()
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->with(['yzMember'=>function($query){
                return $query->select(['member_id','agent_id', 'is_agent', 'group_id','level_id', 'is_black'])->uniacid()
                    ->with(['group'=>function($query1){
                        return $query1->select(['id','group_name'])->uniacid();
                    },'level'=>function($query2){
                        return $query2->select(['id','level_name'])->uniacid();
                    }, 'agent'=>function($query3){
                        return $query3->select(['uid', 'avatar', 'nickname'])->uniacid();
                    }]);
            }, 'hasOneFans' => function($query4) {
                return $query4->select(['uid', 'follow as followed'])->uniacid();
            }, 'hasOneOrder' => function ($query5) {
                return $query5->selectRaw('member_id, count(member_id) as total, sum(price) as sum')
                              ->uniacid()
                              ->where('status', 3)
                              ->groupBy('member_id');
            }]);
    }

    /**
     * 会员－订单1:1关系
     *
     * @return mixed
     */
    public function hasOneOrder()
    {
        return $this->hasOne('app\backend\modules\order\models\order','member_id','uid');
    }

    /**
     * 会员－粉丝1:1关系
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
                return $query->select(['member_id','agent_id', 'is_agent', 'group_id','level_id', 'is_black', 'alipayname', 'alipay', 'content'])->uniacid();
            }, 'hasOneFans' => function($query2) {
                return $query2->select(['uid', 'follow as followed'])->uniacid();
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
     * @param $parame
     * @return mixed
     */
    public static function searchMembers($parame)
    {
        $result = self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid();

        if (!empty($parame['mid'])) {
            $result = $result->where('uid', $parame['mid']);
        }

        if (!empty($parame['realname'])) {
            $result = $result->where(function ($w) use ($parame) {
               $w->where('nickname', 'like', '%' . $parame['realname'] . '%')
                    ->orWhere('realname', 'like', '%' . $parame['realname'] . '%')
                    ->orWhere('mobile', 'like', $parame['realname'] . '%');
            });
        }

        if (!empty($parame['groupid']) || !empty($parame['level']) || !empty($parame['isblack'])) {
            $result = $result->whereHas('yzMember', function($q) use ($parame){
                if (!empty($parame['groupid'])) {
                    $q = $q->where('group_id', $parame['groupid']);
                }

                if (!empty($parame['level'])) {
                    $q = $q->where('level_id',$parame['level']);
                }

                if (!empty($parame['isblack'])) {
                    $q->where('is_black', $parame['isblack']);
                }
            });
        }

        if ($parame['followed'] != '') {
            $result = $result->whereHas('hasOneFans', function ($q2) use ($parame) {
                $q2->where('follow', $parame['followed']);
            });
        }

        $result = $result->with(['yzMember'=>function($query){
                return $query->select(['member_id','agent_id', 'is_agent', 'group_id','level_id', 'is_black'])
                    ->with(['group'=>function($query1){
                        return $query1->select(['id','group_name'])->uniacid();
                    },'level'=>function($query2){
                        return $query2->select(['id','level_name'])->uniacid();
                    }, 'agent'=>function($query3){
                        return $query3->select(['uid', 'avatar', 'nickname'])->uniacid();
                    }]);
            }, 'hasOneFans' => function($query4) {
                return $query4->select(['uid', 'follow as followed'])->uniacid();
            }]);

        return $result;
    }
}