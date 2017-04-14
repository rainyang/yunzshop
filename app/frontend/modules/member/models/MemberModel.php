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
use app\common\models\MemberShopInfo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use app\common\models\Order;

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
        $url = request()->getSchemeAndHttpHost() . '/addons/yun_shop/#/home';
        $url = $url . '?mid=' . \YunShop::app()->getMemberId();

        if (!empty($extra)) {
            $extra = '_' . $extra;
        }

        $extend = 'png';
        $filename = \YunShop::app()->uniacid . '_' . \YunShop::app()->getMemberId() . $extra . '.' . $extend;

        echo QrCode::format($extend)->size(100)->generate($url, storage_path('qr/') . $filename);

        return storage_path('qr/') . $filename;
    }

    /**
     * 我推荐的人
     * @return array
     */
    public static function getMyAgent()
    {
        $agent_ids = [];
        $data = [];

        $agent_info = MemberModel::getMyAgentInfo(\YunShop::app()->getMemberId());
        $agent_model = $agent_info->get();

        if (!empty($agent_model)) {
            $agent_data = $agent_model->toArray();

            foreach ($agent_data as $key => $item) {
                $agent_ids[$key] = $item['uid'];
                $agent_data[$key]['agent_total'] = 0;
            }
        } else {
            return '数据为空';
        }

        $all_count = MemberShopInfo::getAgentAllCount($agent_ids);

        foreach ($all_count as $k => $rows) {
            foreach ($agent_data as $key => $item) {
                if ($rows['parent_id'] == $item['uid']) {
                    $agent_data[$key]['agent_total'] = $rows['total'];

                    break 1;
                }
            }
        }

        if ($agent_data) {
            foreach ($agent_data as $item) {
                $data[] = [
                    'uid' => $item['uid'],
                    'avatar' => $item['avatar'],
                    'nickname' => $item['nickname'],
                    'order_total' => $item['has_one_order']['total'],
                    'order_price' => $item['has_one_order']['sum'],
                    'agent_total' => $item['agent_total'],
                ];
            }
        }

        return $data;
    }

    /**
     * 会员中心返回数据
     *
     * @param $member_info
     * @param $yz_member
     * @return mixed
     */
    public static function userData($member_info, $yz_member)
    {
        if (!empty($yz_member)) {
            $member_info['alipay_name'] = $yz_member['alipay_name'];
            $member_info['alipay'] =  $yz_member['alipay'];
            $member_info['province_name'] =  $yz_member['province_name'];
            $member_info['city_name'] =  $yz_member['city_name'];
            $member_info['area_name'] =  $yz_member['area_name'];
            $member_info['province'] =  $yz_member['province'];
            $member_info['city'] =  $yz_member['city'];
            $member_info['area'] =  $yz_member['area'];
            $member_info['address'] =  $yz_member['address'];

            if (!empty( $yz_member['group'])) {
                $member_info['group_id'] =  $yz_member['group']['id'];
                $member_info['group_name'] =  $yz_member['group']['group_name'];
            }

            if (!empty( $yz_member['level'])) {
                $member_info['level_id'] =  $yz_member['level']['id'];
                $member_info['level_name'] =  $yz_member['level']['level_name'];
            }
        }

        if (!empty($member_info['birthyear'] )) {
            $member_info['birthday'] = $member_info['birthyear'] . '-'. $member_info['birthmonth'] . '-' .$member_info['birthday'];
        } else {
            $member_info['birthday'] = '1970-01-01';
        }


        $order_info = Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE]);

        $member_info['order'] = $order_info;

        $member_info['is_agent'] = MemberModel::isAgent()->toArray();
        $member_info['referral'] = MemberModel::getMyReferral();
        $member_info['qr'] = MemberModel::getAgentQR();

        return $member_info;
    }
}