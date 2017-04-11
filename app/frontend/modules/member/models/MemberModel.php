<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:53
 */

/**
 * 会员表
 */
namespace app\frontend\modules\member\models;

use app\common\models\Member;

class MemberModel extends Member
{
    protected $guarded = ['credit1', 'credit2', 'credit3', 'credit4', 'credit5'];

    protected $fillable = ['uniacid', 'mobile', 'groupid', 'createtime', 'nickname', 'avatar', 'gender'
        , 'salt', 'password'];

    protected $attributes = ['bio' => '', 'resideprovince' => '', 'residecity' => '', 'nationality' => '', 'interest' => ''];

    /**
     * 获取用户uid
     *
     * @param $uniacid
     * @param $mobile
     * @return mixed
     */
    public static function getId($uniacid, $mobile)
    {
        return self::select('uid')
            ->where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->get()
            ->toArray();
    }

    /**
     * 添加数据并返回id
     *
     * @param $data
     * @return mixed
     */
    public static function insertData($data)
    {
        return self::insertGetId($data);
    }

    /**
     * 检查手机号是否存在
     *
     * @param $uniacid
     * @param $mobile
     * @return mixed
     */
    public static function checkMobile($uniacid, $mobile)
    {
        return self::where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->first()
            ->toArray();
    }

    /**
     * 获取用户信息
     *
     * @param $uniacid
     * @param $mobile
     * @param $password
     * @return mixed
     */
    public static function getUserInfo($uniacid, $mobile, $password)
    {
        return self::where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->where('password', $password);
    }

    /**
     * 更新数据
     *
     * @param $uid
     * @param $data
     * @return mixed
     */
    public static function updataData($uid, $data)
    {
        return self::uniacid()
            ->where('uid', $uid)
            ->update($data);
    }

    /**
     * 我的推荐人信息
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyReferrerInfo($uid)
    {
        return self::select(['uid'])->uniacid()
            ->where('uid', $uid)
            ->with([
                'yzMember' => function ($query) {
                    return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'alipayname', 'alipay'])
                        ->where('is_black', 0)
                        ->with(['level'=>function($query2){
                            return $query2->select(['id','level_name'])->uniacid();
                        }]);
                }
            ]);
    }

    /**
     * 我的下线信息 1级
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyAgentInfo($uid)
    {
        return self::uniacid()
            ->whereHas('yzMember', function($query) use ($uid){
                         $query->where('parent_id', $uid);
            })
            ->with(['hasOneOrder' => function ($query) {
                return $query->selectRaw('uid, count(uid) as total, sum(price) as sum')
                    ->uniacid()
                    ->where('status', 3)
                    ->groupBy('uid');
            }]);
    }

    /**
     * 我的下线信息 3级
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyAgentsInfo($uid)
    {
        return self::uniacid()
            ->with(['hasManyYzMember' => function ($query) {

                return $query->with(['hasManySelf' => function ($query) {

                    return $query->with(['hasManySelf' => function ($query) {

                        return $query->get();
                    }])->get();
                }])->get();
            }])
            ->with(['hasOneOrder' => function ($query) {
                return $query->selectRaw('uid, count(uid) as total, sum(price) as sum')
                    ->uniacid()
                    ->where('status', 3)
                    ->groupBy('uid');
            }])
            ->where('uid', $uid);
    }

    /**
     *
     * @return mixed
     */
    public function hasManyYzMember()
    {
        return $this->hasMany('app\common\models\MemberShopInfo', 'parent_id', 'uid');
    }
}