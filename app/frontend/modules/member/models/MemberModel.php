<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Merchant\common\models\MerchantLevel;
use Yunshop\Micro\common\models\MicroShopLevel;
use Yunshop\TeamDividend\models\TeamDividendLevelModel;

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
                    return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'alipayname', 'alipay', 'status', 'inviter'])
                        ->where('is_black', 0)
                        ->with(['level'=>function($query2){
                            return $query2->select(['id','level_name'])->uniacid();
                        }]);
                }
            ]);
    }

    /**
     * 获取我的下线
     *
     * @return mixed
     */
    public static function getAgentCount($uid)
    {
        return self::uniacid()
            ->whereHas('yzMember', function($query) use ($uid){
                $query->where('parent_id', $uid);
            })
            ->count();
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

    public static function getMyAllAgentsInfo($uid, $level)
    {
        return self::uniacid()
            ->whereHas('yzMember', function($query) use ($uid, $level){
                $query->whereRaw('FIND_IN_SET(?, relation)' . ($level != 0 ? ' = ?' : ''), [$uid, $level]);
            })
            ->with(['yzMember' => function ($query) {
                return $query->select('member_id', 'is_agent', 'status');
            }, 'hasOneOrder' => function ($query) {
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
            //MemberRelation::checkAgent($uid);

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
            if (isset($set) && $set['headimg']) {
                $avatar = replace_yunshop(tomedia($set['headimg']));
            } else {
                $avatar = Url::shopUrl('static/images/photo-mr.jpg');
            }

            $member_info = $member_info->toArray();

            $referrer_info = self::getUserInfos($member_info['yz_member']['parent_id'])->first();

            if ($member_info['yz_member']['inviter'] == 1) {
                if (!empty($referrer_info)) {
                    $info = $referrer_info->toArray();
                    $data = [
                        'uid' => $info['uid'],
                        'avatar' => $info['avatar'],
                        'nickname' => $info['nickname'],
                        'level' => $info['yz_member']['level']['level_name'],
                        'is_show' => $set['is_referrer']
                    ];
                } else {
                    $data = [
                        'uid' => '',
                        'avatar' => $avatar,
                        'nickname' => '总店',
                        'level' => '',
                        'is_show' => $set['is_referrer']
                    ];
                }
            } else {
                $data = [
                    'uid' => '',
                    'avatar' => $avatar,
                    'nickname' => '暂无',
                    'level' => '',
                    'is_show' => $set['is_referrer']
                ];
            }
        }

        return $data;
    }


    /**
     * 我的推荐人 v2
     *
     * @return array
     */
    public static function getMyReferral_v2()
    {
        $builder     = self::getMyReferrerInfo(\YunShop::app()->getMemberId());
        $member_info = self::getMemberRole($builder)->first();

        $member_role = self::convertRoleText($member_info);

        $set = \Setting::get('shop.member');

        $data = [];

        if (!empty($member_info)) {
            if (isset($set) && $set['headimg']) {
                $avatar = replace_yunshop(tomedia($set['headimg']));
            } else {
                $avatar = Url::shopUrl('static/images/photo-mr.jpg');
            }

            $member_info = $member_info->toArray();

            $referrer_info = self::getUserInfos($member_info['yz_member']['parent_id'])->first();

            if ($member_info['yz_member']['inviter'] == 1) {
                if (!empty($referrer_info)) {
                    $info = $referrer_info->toArray();
                    $data = [
                        'uid' => $info['uid'],
                        'avatar' => $info['avatar'],
                        'nickname' => $info['nickname'],
                        'level' => $info['yz_member']['level']['level_name'],
                        'is_show' => $set['is_referrer'],
                        'role'   => $member_role
                    ];
                } else {
                    $data = [
                        'uid' => '',
                        'avatar' => $avatar,
                        'nickname' => '总店',
                        'level' => '',
                        'is_show' => $set['is_referrer'],
                        'role'   => $member_role
                    ];
                }
            } else {
                $data = [
                    'uid' => '',
                    'avatar' => $avatar,
                    'nickname' => '暂无',
                    'level' => '',
                    'is_show' => $set['is_referrer'],
                    'role'   => $member_role
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
        $url = $url . '&mid=' . \YunShop::app()->getMemberId();

        if (!empty($extra)) {
            $extra = '_' . $extra;
        }

        $extend = 'png';
        $filename = \YunShop::app()->uniacid . '_' . \YunShop::app()->getMemberId() . $extra . '.' . $extend;
        $path = storage_path('app/public/qr/');

        echo QrCode::format($extend)->size(400)->generate($url,  $path . $filename);

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
     * 我推荐的人v2
     * @return array
     */
    public static function getMyAgent_v2()
    {
        $pageSize = 5;
        $data[] = [
            'total' => 0,
            'data' => []
        ];

        $agent_level = [1];

        $relation_base = \Setting::get('relation_base');

        if (!is_null($relation_base['relation_level'])) {
            $agent_level = $relation_base['relation_level'];
        }

        $agent_level_first_info = [];
        $agent_level_second_info = [];
        $agent_level_third_info = [];

        foreach ($agent_level as $val) {
            switch ($val) {
                case 1:
                    $builder = MemberModel::getMyAllAgentsInfo(\YunShop::app()->getMemberId(), 1);
                    $agent_info = self::getMemberRole($builder)->paginate($pageSize);

                    $agent_level_first_info = self::fetchAgentInfo($agent_info->items());

                    if (!empty($agent_level_first_info)) {
                        $data[0]['data'][] = [
                            'level' => 1,
                            'data' => $agent_level_first_info->toArray(),
                            'total' => count($agent_level_first_info)
                        ];
                    } else {
                        $data[0]['data'][] = [
                            'level' => 1,
                            'data' => [],
                            'total' => 0
                        ];
                    }

                    break;
                case 2:
                    $builder = MemberModel::getMyAllAgentsInfo(\YunShop::app()->getMemberId(),2);
                    $agent_info = self::getMemberRole($builder)->paginate($pageSize);

                    $agent_level_second_info = self::fetchAgentInfo($agent_info->items());

                    if (!empty($agent_level_second_info)) {
                        $data[0]['data'][] = [
                            'level' => 2,
                            'data' => $agent_level_second_info->toArray(),
                            'total' => count($agent_level_second_info)
                        ];
                    } else {
                        $data[0]['data'][] = [
                            'level' => 2,
                            'data' => [],
                            'total' => 0
                        ];
                    }

                    break;
                case 3:
                    $builder = MemberModel::getMyAllAgentsInfo(\YunShop::app()->getMemberId(),3);
                    $agent_info = self::getMemberRole($builder)->paginate($pageSize);

                    $agent_level_third_info = self::fetchAgentInfo($agent_info->items());

                    if (!empty($agent_level_third_info)) {
                        $data[0]['data'][] = [
                            'level' => 3,
                            'data' => $agent_level_third_info->toArray(),
                            'total' => count($agent_level_third_info)
                        ];
                    } else {
                        $data[0]['data'][] = [
                            'level' => 3,
                            'data' => [],
                            'total' => 0
                        ];
                    }

                    break;
            }
        }

        $data[0]['total'] = count($agent_level_first_info) + count($agent_level_second_info) + count($agent_level_third_info);

        //TODO search
        if (\YunShop::request()->keyword) {
            $data = self::searchMemberRelation($data);
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
            $member_info['alipay_name'] = $yz_member['alipayname'];
            $member_info['alipay'] =  $yz_member['alipay'];
            $member_info['province_name'] =  $yz_member['province_name'];
            $member_info['city_name'] =  $yz_member['city_name'];
            $member_info['area_name'] =  $yz_member['area_name'];
            $member_info['province'] =  $yz_member['province'];
            $member_info['city'] =  $yz_member['city'];
            $member_info['area'] =  $yz_member['area'];
            $member_info['address'] =  $yz_member['address'];
            $member_info['wechat'] =  $yz_member['wechat'];

            if (!empty( $yz_member['group'])) {
                $member_info['group_id'] =  $yz_member['group']['id'];
                $member_info['group_name'] =  $yz_member['group']['group_name'];
            }

            if (!empty( $yz_member['level'])) {
                $member_info['level_id'] =  $yz_member['level']['id'];
                $member_info['level_name'] =  $yz_member['level']['level_name'];
            } else {
                $set = \Setting::get('shop.member');
                $member_info['level_id'] =  0;
                $member_info['level_name'] =  $set['level_name'] ? $set['level_name'] : '普通会员';
            }
        }

        if (!empty($member_info['birthyear'] )) {
            $member_info['birthday'] = date('Y-m-d', strtotime($member_info['birthyear'] . '-'. $member_info['birthmonth'] . '-' .$member_info['birthday']));
        } else {
            $member_info['birthday'] = date('Y-m-d', time());
        }

        $order_info = \app\frontend\models\Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE,Order::REFUND]);

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
            'text' => !empty($shop['credit']) ? $shop['credit'] : '余额',
            'data' => $member_info['credit2']
            ];
        $member_info['integral'] = [
            'text' => !empty($shop['credit1']) ? $shop['credit1'] : '积分',
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

    public static function convertRoleText($member_modle)
    {
         $member_role = '';

         if (!is_null($member_modle)) {
             if (!is_null($member_modle->hasOneAgent)) {
                 $member_role .= '分销商&';
             }

             if (!is_null($member_modle->hasOneTeamDividend)) {
                 $member_role .= '经销商&';
             }

             if (!is_null($member_modle->hasOneAreaDividend)) {
                 $member_role .= '区域代理&';
             }

             if (!is_null($member_modle->hasOneMerchant)) {
                 if (0 == $member_modle->hasOneMerchant->is_center) {
                     $member_role .= '招商员&';
                 }
             }

             if (!is_null($member_modle->hasOneMerchantCenter)) {
                 if (1 == $member_modle->hasOneMerchant->is_center) {
                     $member_role .= '招商中心&';
                 }
             }

             if (!is_null($member_modle->hasOneMicro)) {
                 $member_role .= '微店店主&';
             }

             if (!is_null($member_modle->hasOneSupplier)) {
                 $member_role .= '供应商&';
             }
         }

         if (!empty($member_role)) {
             $member_role = rtrim($member_role, '&');
         }

         return $member_role;
    }

    public static function fetchAgentInfo($agent_info)
    {
        if (empty($agent_info)) {
            return [];
        }

        return collect($agent_info)->map(function($item) {
            $is_agent          = 0;
            $order_price       = 0;
            $agent_total       = 0;
            $agent_order_price = 0;

            $role              = self::convertRoleText($item);
            $role_type         = self::setRoleLevel($item);

            $child_agent = MemberModel::getMyAllAgentsInfo($item->uid, 1)->get();

            if (!is_null($child_agent)) {
                $agent_total = count($child_agent);

                foreach ($child_agent as $rows) {
                    $agent_order_price += $rows->hasOneOrder->sum;
                }
            }

            if (!is_null($item->yzMember)) {
                if (1 == $item->yzMember->is_agent && 2 == $item->yzMember->status) {
                    $is_agent = 1;
                }
            }

            if (!is_null($item->hasOneOrder)) {
                $order_price = $item->hasOneOrder->sum;
            }

            return [
                'id' => $item->uid,
                'is_agent' => $is_agent,
                'nickname' => $item->nickname,
                'avatar'   => $item->avatar,
                'order_price' => $order_price,
                'agent_total' => $agent_total,
                'agent_order_price' => $agent_order_price,
                'role' => $role,
                'role_type' => $role_type
            ];
        });
    }

    public static function searchMemberRelation($data)
    {
        $keyword = \YunShop::request()->keyword;
        $level   = \YunShop::request()->level;
        $filter  = ['招商员', '供应商']; //没有等级

        $coll = collect($data[0]['data'])->map(function ($item) use ($keyword, $level, $filter) {
            return collect($item)->mapWithKeys(function ($item, $key) use ($keyword, $level, $filter) {
                if ($key == 'level') {
                    $res['level'] = $item;
                }

                if ($key == 'data') {
                    $res['data'] = collect($item)->filter(function ($item) use ($keyword, $level, $filter) {
                        $role_level = false;

                        if (!empty($item['role_type'])) {
                            foreach ($item['role_type'] as $rows) {
                                foreach ($rows as $key => $val) {
                                    if (in_array($keyword, $filter) || ($key == $keyword && $val == $level)) {
                                        $role_level = true;
                                    }

                                    break 2;
                                }
                            }
                        }

                        return preg_match("/{$keyword}/", $item['role']) && $role_level;
                    });

                    $res['data'] = array_values($res['data']->toArray());

                    $res['total'] = count($res['data']);
                }

                return $res;
            });
        });

        if (!$coll->isEmpty()) {
            $total = 0;

            foreach ($coll as $rows) {
                $total += $rows['total'];
            }

            $result[0] = [
                'total' => $total,
                'data'  => $coll->toArray()
            ];
        }

        return $result;
    }

    public static function setRoleLevel($member_modle)
    {
        $role_type = [];

        if (!is_null($member_modle)) {
            if (!is_null($member_modle->hasOneAgent)) {
                array_push($role_type, ['分销商'=>$member_modle->hasOneAgent->agent_level_id]);
            }

            if (!is_null($member_modle->hasOneTeamDividend)) {
                array_push($role_type, ['经销商'=>$member_modle->hasOneTeamDividend->level]);
            }

            if (!is_null($member_modle->hasOneAreaDividend)) {
                array_push($role_type, ['区域代理'=>$member_modle->hasOneAreaDividend->agent_level]);
            }

            if (!is_null($member_modle->hasOneMerchant)) {
            }

            if (!is_null($member_modle->hasOneMerchantCenter)) {
                if (1 == $member_modle->hasOneMerchant->is_center) {
                    array_push($role_type, ['招商中心'=>$member_modle->hasOneMerchantCenter->level_id]);
                }
            }

            if (!is_null($member_modle->hasOneMicro)) {
                array_push($role_type, ['微店店主'=>$member_modle->hasOneMicro->level_id]);
            }

            if (!is_null($member_modle->hasOneSupplier)) {
            }
        }

        return $role_type;
    }

    public static function filterMemberRoleAndLevel()
    {
        $data = [];

        $agent_level = AgentLevel::uniacid()->get();

        if (!$agent_level->isEmpty()) {
            $agent_level = collect($agent_level)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'level_name' => $item->name
                ];
            });

            array_push($data, ['role' => '分销商', 'level' =>$agent_level->all()]);
        } else {
            array_push($data, ['role' => '分销商', 'level' =>[]]);
        }

        $teamdividend_level = TeamDividendLevelModel::uniacid()->get();

        if (!$teamdividend_level->isEmpty()) {
            array_push($data, ['role' => '经销商', 'level' =>$teamdividend_level->toArray()]);
        } else {
            array_push($data, ['role' => '经销商', 'level' =>[]]);
        }

        array_push($data, ['role' => '区域代理', 'level' =>[
            ['id' =>1, 'level_name'=>'省代理'],['id' =>2, 'level_name'=>'市代理'],['id' =>3, 'level_name'=>'区代理'],['id'=>4, 'level_name'=>'街道代理']
        ]]);

        array_push($data, ['role' => '招商员', 'level' =>[]]);

        $merchant_level = MerchantLevel::uniacid()->get();

        if (!$merchant_level->isEmpty()) {
            array_push($data, ['role' => '招商中心', 'level' =>$merchant_level->toArray()]);
        } else {
            array_push($data, ['role' => '招商中心', 'level' =>[]]);
        }

        $microShop_level = MicroShopLevel::uniacid()->get();
        if (!$microShop_level->isEmpty()) {
            array_push($data, ['role' => '微店店主', 'level' =>$microShop_level->toArray()]);
        } else {
            array_push($data, ['role' => '微店店主', 'level' =>[]]);
        }

        array_push($data, ['role' => '供应商', 'level' =>[]]);

        return $data;
    }
}