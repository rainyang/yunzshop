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
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\models\MemberShopInfo;
use app\common\models\Setting;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use app\common\models\Order;

class MemberModel extends Member
{
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
            ->first();
    }

    /**
     * 添加数据并返回id
     *
     * @param $data
     * @return mixed
     */
    public static function insertData($userinfo, $data)
    {
        $member_model = new MemberModel();

        $member_model->uniacid = $data['uniacid'];
        $member_model->email = '';
        $member_model->groupid = $data['groupid'];
        $member_model->createtime = time();
        $member_model->nickname = stripslashes($userinfo['nickname']);
        $member_model->avatar = $userinfo['headimgurl'];
        $member_model->gender = $userinfo['sex'];
        $member_model->nationality = $userinfo['country'];
        $member_model->resideprovince = $userinfo['province'] . '省';
        $member_model->residecity = $userinfo['city'] . '市';
        $member_model->salt = '';
        $member_model->password = '';

        if ($member_model->save()) {
            return $member_model->uid;
        } else {
            return false;
        }
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
            ->first();
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

        if (!empty($uid)) {
            MemberRelation::checkAgent($uid);

            $member_info = SubMemberModel::getMemberShopInfo($uid);

            if ($member_info && $member_info->is_agent == 1 && $member_info->status == 2) {
                return true;
            }
        }

        return false;

    }

    /**
     * 我的推荐人
     *
     * @return array
     */
    public static function getMyReferral()
    {
        $member_info = self::getMyReferrerInfo(\YunShop::app()->getMemberId())->first();

        $set = \Setting::get('shop.member');

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
                    'level' => $info['yz_member']['level']['level_name'],
                    'is_show' => $set['is_referrer']
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
        $url = Url::absoluteApp('/home');
        $url = $url . '?mid=' . \YunShop::app()->getMemberId();

        if (!empty($extra)) {
            $extra = '_' . $extra;
        }

        $extend = 'png';
        $filename = \YunShop::app()->uniacid . '_' . \YunShop::app()->getMemberId() . $extra . '.' . $extend;
        $path = storage_path('app/public/qr/');

        echo QrCode::format($extend)->size(100)->generate($url,  $path . $filename);

        return request()->getSchemeAndHttpHost() . '/' . substr($path, strpos($path, 'addons')) . $filename;
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


        $order_info = \app\frontend\modules\order\models\Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE]);

        $member_info['order'] = $order_info;

        $member_info['is_agent'] = self::isAgent();
        $member_info['referral'] = self::getMyReferral();

        self::createDir(storage_path('app/public/qr'));
        self::createDir(storage_path('app/public/avatar'));

        $member_info['qr'] = self::getAgentQR();
        $member_info['avatar_dir'] =  request()->getSchemeAndHttpHost() . '/addons/yun_shop/storage/app/public/avatar/';

        $shop = \Setting::get('shop.shop');
        $member_info['copyright'] = $shop['copyright'] ? $shop['copyright'] : '';
        $member_info['credit'] = [
            'text' => $shop['credit'] ? $shop['credit'] : '余额',
            'data' => $member_info['credit2']
            ];
        $member_info['integral'] = [
            'text' => $shop['credit1'] ? $shop['credit1'] : '积分',
            'data' => $member_info['credit1']
            ];

        return $member_info;
    }

    function createDir($dest)
    {
        if (!is_dir($dest)) {
            (@mkdir($dest, 0777, true));
        }
    }
}