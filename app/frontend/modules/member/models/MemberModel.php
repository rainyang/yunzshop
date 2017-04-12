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

use app\backend\modules\member\models\MemberRelation;
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
            ->where('uid', $uid);
    }

    /**
     * 我的上级 3级
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyAgentsParentInfo($uid)
    {
        return self::select(['uid'])
            ->uniacid()
            ->with(['yzMember' => function ($query) {
                return $query->select(['member_id', 'parent_id'])
                    ->with(['hasOnePreSelf' => function ($query) {
                        return $query->select(['member_id', 'parent_id'])
                        ->with(['hasOnePreSelf' => function ($query) {
                            return $query->select(['member_id', 'parent_id'])
                            ->with(['hasOnePreSelf' => function ($query) {
                                return $query->select(['member_id', 'parent_id'])->first();
                        }])->first();
                    }])->first();
                }])->first();
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

    /**
     * 用户是否有推广权限
     *
     * @return mixed
     */
    public static function isAgent()
    {
        $uid = \YunShop::app()->getMemberId();

        MemberRelation::checkAgent($uid);

        $member_info = SubMemberModel::getMemberShopInfo($uid);

        return $member_info;
    }

    /**
     * 我的推荐人
     *
     * @return array
     */
    public static function getMyReferral()
    {
        $member_info = self::getMyReferrerInfo(\YunShop::app()->getMemberId())->first();

        $data = [];

        if (!empty($member_info)) {
            $member_info = $member_info->toArray();

            $referrer_info = self::getUserInfos($member_info['yz_member']['parent_id'])->first();

            if (!empty($referrer_info)) {
                $info = $referrer_info->toArray();
                $data = [
                    'uid' => $info['uid'],
                    'avatar' => $info['avatar'],
                    'nickname' => $info['nickname'],
                    'level' => $info['yz_member']['level']['level_name']
                ];
            }
        }

        return $data;
    }

    /**
     * 推广二维码
     *
     * @param string $extra
     * @return mixed
     */
    public static function getAgentQR($extra='')
    {
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/addons/sz_yi/home#/home';
        $url = $url . '?mid=' . \YunShop::app()->getMemberId();

        if (!empty($extra)) {
            $extra = '_' . $extra;
        }

        $extend = 'png';
        $filename = \YunShop::app()->uniacid . '_' . \YunShop::app()->getMemberId() . $extra . '.' . $extend;

        echo QrCode::format($extend)->size(100)->generate($url, storage_path('qr/') . $filename);

        return storage_path('qr/') . $filename;
    }
}