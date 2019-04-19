<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\backend\modules\charts\modules\phone\models\PhoneAttribution;
use app\backend\modules\charts\modules\phone\services\PhoneAttributionService;
use app\backend\modules\member\models\MemberRelation;
use app\backend\modules\order\models\Order;
use app\common\components\ApiController;
use app\common\exceptions\MemberNotLoginException;
use app\common\facades\Setting;
use app\common\helpers\Cache;
use app\common\helpers\Client;
use app\common\helpers\ImageHelper;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\models\Area;
use app\common\models\Goods;
use app\common\models\McMappingFans;
use app\common\models\member\MemberInvitationCodeLog;
use app\common\models\member\MemberInviteGoodsLogController;
use app\common\models\MemberShopInfo;
use app\common\services\alipay\OnekeyLogin;
use app\common\services\plugin\huanxun\HuanxunSet;
use app\common\services\popularize\PortType;
use app\common\services\Session;
use app\frontend\models\Member;
use app\frontend\models\OrderListModel;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\member\services\MemberService;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;
use Yunshop\AlipayOnekeyLogin\services\SynchronousUserInfo;
use Yunshop\Commission\models\Agents;
use Yunshop\Kingtimes\common\models\Distributor;
use Yunshop\Kingtimes\common\models\Provider;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\services\CreatePosterService;
use Yunshop\TeamDividend\models\YzMemberModel;
use Yunshop\Designer\models\Designer;

class MemberController extends ApiController
{
    protected $publicAction = [
        'guideFollow',
        'wxJsSdkConfig',
        'memberFromHXQModule',
        'dsAlipayUserModule',
        'isValidatePage'
    ];
    protected $ignoreAction = [
        'guideFollow',
        'wxJsSdkConfig',
        'memberFromHXQModule',
        'dsAlipayUserModule',
        'isValidatePage'
    ];

    /**
     * 获取用户信息
     *
     */
    public function getUserInfo()
    {
        $member_id = \YunShop::app()->getMemberId();
        $v         = request('v');

        $this->chkAccount();

        if (!empty($member_id)) {


            $member_info = MemberModel::getUserInfos($member_id)->first();

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();

                $data = MemberModel::userData($member_info, $member_info['yz_member']);

                $data = MemberModel::addPlugins($data);

                //隐藏爱心值插件入口
                $love_show = PortType::popularizeShow(\YunShop::request()->type);
                if (isset($data['love']) && (!$love_show)) {
                    $data['love']['status'] = false;
                }

                $data['income'] = MemberModel::getIncomeCount();

                $data['relation_switch'] = (1 == $member_info['yz_member']['is_agent'] && 2 == $member_info['yz_member']['status'])
                    ? 1 : 0;

                //个人中心的推广二维码
                if ($data['relation_switch']) {
                    $data['poster'] = $this->getPoster($member_info['yz_member']['is_agent']);
                }

                //文章营销
                $articleSetting = Setting::get('plugin.article');
                if ($articleSetting['enabled'] == 1) {
                    $data['article_title'] = $articleSetting['center'] ? html_entity_decode($articleSetting['center']) : '文章营销';
                }

                //自定义表单
                $data['myform'] = (new MemberService())->memberInfoAttrStatus();

                $data['avatar'] = $data['avatar'] ? yz_tomedia($data['avatar']) : yz_tomedia(\Setting::get('shop.member.headimg'));

                //修复微信头像地址
                $data['avatar'] = ImageHelper::fix_wechatAvatar($data['avatar']);

                //IOS时，把微信头像url改为https前缀
                $data['avatar'] = ImageHelper::iosWechatAvatar($data['avatar']);

                $withdraw_status = Setting::get('shop_app.pay.withdraw_status');
                if (isset($withdraw_status) && $withdraw_status == 0) {
                    $withdraw_status = 0;
                } else {
                    $withdraw_status = 1;
                }
                //是否显示我的推广
                $withdraw_status         = PortType::popularizeShow(\YunShop::request()->type);
                $data['withdraw_status'] = $withdraw_status;

                if (!is_null($v)) {
                    $set = \Setting::get('shop.member');

                    $data['inviteCode']['status'] = $set['is_invite'] ?: 0;

//                    $data['inviteCode']['required'] =$set['required'] ?: 0;


                    if (is_null($member_info['yz_member']['invite_code']) || empty($member_info['yz_member']['invite_code'])) {
                        $data['inviteCode']['code'] = MemberModel::getInviteCode($member_id);
                    } else {
                        $data['inviteCode']['code'] = $member_info['yz_member']['invite_code'];
                    }
                } else {
                    $data['inviteCode'] = 0;
                }

                //查看聚合支付是否开启
                if (app('plugins')->isEnabled('yop-pay')) {
                    $data['yop'] = 1;
                }else{
                    $data['yop'] = 0;
                }

                $data['is_open_hotel'] = app('plugins')->isEnabled('hotel') ? 1 : 0;

                return $this->successJson('', $data);
            } else {
                return $this->errorJson('[' . $member_id . ']用户不存在');
            }

        } else {
            return $this->errorJson('缺少访问参数');
        }

    }


    /**
     * 检查会员推广资格
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMemberRelationInfo()
    {
        $info = MemberRelation::getSetInfo()->first();

        $member_info = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (empty($info)) {
            return $this->errorJson('缺少参数');
        } else {
            $info = $info->toArray();
        }

        if (empty($member_info)) {
            return $this->errorJson('会员不存在');
        } else {
            $data = $member_info->toArray();
        }

        $account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);
        switch ($info['become']) {
            case 0:
            case 1:
                $apply_qualification = 1;
                $mid                 = \app\common\models\Member::getMid();
                $parent_name         = '';

                if (empty($mid)) {
                    $parent_name = '总店';
                } else {
                    $parent_model = MemberModel::getMemberById($mid);

                    if (!empty($parent_model)) {
                        $parent_member = $parent_model->toArray();

                        $parent_name = $parent_member['realname'];
                    }
                }

                $member_model = MemberModel::getMemberById(\YunShop::app()->getMemberId());

                if (!empty($member_model)) {
                    $member = $member_model->toArray();
                }
                break;
            case 2:
                $apply_qualification = 2;
                $cost_num            = Order::getCostTotalNum(\YunShop::app()->getMemberId());

                if ($info['become_check'] && $cost_num >= $info['become_ordercount']) {
                    $apply_qualification = 5;
                }
                break;
            case 3:
                $apply_qualification = 3;
                $cost_price          = Order::getCostTotalPrice(\YunShop::app()->getMemberId());

                if ($info['become_check'] && $cost_price >= $info['become_moneycount']) {
                    $apply_qualification = 6;
                }
                break;
            case 4:
                $apply_qualification = 4;
                $goods               = Goods::getGoodsById($info['become_goods_id']);
                $goods_name          = '';

                if (!empty($goods)) {
                    $goods = $goods->toArray();

                    $goods_name = $goods['title'];
                }

                if ($info['become_check'] && MemberRelation::checkOrderGoods($info['become_goods_id'])) {
                    $apply_qualification = 7;
                }
                break;
            default:
                $apply_qualification = 0;
        }

        $relation = [
            'switched' => $info['status'],
            'become'   => $apply_qualification,
            'become1'  => [
                'shop_name'   => $account['name'],
                'parent_name' => $parent_name,
                'realname'    => $member['realname'],
                'mobile'      => $member['mobile']
            ],
            'become2'  => ['shop_name' => $account['name'], 'total' => $info['become_ordercount'], 'cost' => $cost_num],
            'become3'  => [
                'shop_name' => $account['name'],
                'total'     => $info['become_moneycount'],
                'cost'      => $cost_price
            ],
            'become4'  => [
                'shop_name'  => $account['name'],
                'goods_name' => $goods_name,
                'goods_id'   => $info['become_goods_id']
            ],
            'is_agent' => $data['is_agent'],
            'status'   => $data['status'],
            'account'  => $account['name']
        ];

        return $this->successJson('', $relation);
    }

    /**
     * 会员是否有推广权限
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function isAgent()
    {
        if (MemberModel::isAgent()) {
            $has_permission = 1;
        } else {
            $has_permission = 0;
        }

        return $this->successJson('', ['is_agent' => $has_permission]);
    }

    /**
     * 会员推广二维码
     *
     * @param $url
     * @param string $extra
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgentQR($extra = '')
    {
        if (empty(\YunShop::app()->getMemberId())) {
            return $this->errorJson('请重新登录');
        }

        $qr_url = MemberModel::getAgentQR($extra = '');

        return $this->successJson('', ['qr' => $qr_url]);
    }

    /**
     * 用户推广申请
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAgentApply()
    {
        if (!\YunShop::app()->getMemberId()) {
            return $this->errorJson('请重新登录');
        }
        $sub_member_model = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        $sub_member_model->status     = 1;
        $sub_member_model->apply_time = time();

        if (!$sub_member_model->save()) {
            return $this->errorJson('会员信息保存失败');
        }

        $realname = \YunShop::request()->realname;
        $moible   = \YunShop::request()->mobile;

        $member_mode = MemberModel::getMemberById(\YunShop::app()->getMemberId());

        $member_mode->realname = $realname;
        $member_mode->mobile   = $moible;

        if (!$member_mode->save()) {
            return $this->errorJson('会员信息保存失败');
        }

        return $this->successJson('ok');
    }

    /**
     * 获取我的下线
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyAgentCount()
    {
        return $this->successJson('', ['count' => MemberModel::getAgentCount_v2(\YunShop::app()->getMemberId())]);
    }

    /**
     * 我的推荐人
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyReferral()
    {
        $data = MemberModel::getMyReferral();

        if (!empty($data)) {
            return $this->successJson('', $data);
        } else {
            return $this->errorJson('会员不存在');
        }
    }

    /**
     * 我的推荐人v2
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyReferral_v2()
    {
        $data = MemberModel::getMyReferral_v2();

        //IOS时，把微信头像url改为https前缀
        $data['avatar'] = ImageHelper::iosWechatAvatar($data['avatar']);

        if (!empty($data)) {
            return $this->successJson('', $data);
        } else {
            return $this->errorJson('会员不存在');
        }
    }

    /**
     * 我推荐的人
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyAgent()
    {
        $data = MemberModel::getMyAgent();

        if (!empty($data)) {
            return $this->successJson('', $data);
        } else {
            return $this->errorJson('会员不存在');
        }
    }

    /**
     * 我推荐的人 v2 基本信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyAgent_v2()
    {
        $data = MemberModel::getMyAgent_v2();

        return $this->successJson('', $data);
    }

    /**
     * 我推荐的人 v2 数据
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyAgentData_v2()
    {
        $data = MemberModel::getMyAgentData_v2();

        return $this->successJson('', $data);
    }

    /**
     * 会员中心我的关系
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyRelation()
    {
        $my_referral = MemberModel::getMyReferral();

        $my_agent = MemberModel::getMyAgent();

        $data = [
            'my_referral' => $my_referral,
            'my_agent'    => $my_agent
        ];

        return $this->successJson('', $data);
    }

    /**
     * 通过省份id获取对应的市信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCitysByProvince()
    {
        $id = \YunShop::request()->parent_id;

        $data = Area::getCitysByProvince($id);

        if (!empty($data)) {
            return $this->successJson('', $data->toArray());
        } else {
            return $this->errorJson('查无数据');
        }
    }

    /**
     * 通过市id获取对应的区信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAreasByCity()
    {
        $id = \YunShop::request()->parent_id;

        $data = Area::getAreasByCity($id);

        if (!empty($data)) {
            return $this->successJson('', $data->toArray());
        } else {
            return $this->errorJson('查无数据');
        }
    }

    /**
     * 更新会员资料
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserInfo()
    {
        $birthday = [];
        $data     = \YunShop::request()->data;

        if (isset($data['birthday'])) {
            $birthday = explode('-', $data['birthday']);
        }

        $member_data = [
            'realname'   => $data['realname'],
            'avatar'     => $data['avatar'],
            'gender'     => isset($data['gender']) ? intval($data['gender']) : 0,
            'birthyear'  => isset($birthday[0]) ? intval($birthday[0]) : 0,
            'birthmonth' => isset($birthday[1]) ? intval($birthday[1]) : 0,
            'birthday'   => isset($birthday[2]) ? intval($birthday[2]) : 0
        ];

        if (!empty($data['mobile'])) {
            $member_data['mobile'] = $data['mobile'];
        }

        if (!empty($data['telephone'])) {
            $member_data['telephone'] = $data['telephone'];
        }

        $member_shop_info_data = [
            'alipay'        => $data['alipay'],
            'alipayname'    => $data['alipay_name'],
            'province_name' => isset($data['province_name']) ? $data['province_name'] : '',
            'city_name'     => isset($data['city_name']) ? $data['city_name'] : '',
            'area_name'     => isset($data['area_name']) ? $data['area_name'] : '',
            'province'      => isset($data['province']) ? intval($data['province']) : 0,
            'city'          => isset($data['city']) ? intval($data['city']) : 0,
            'area'          => isset($data['area']) ? intval($data['area']) : 0,
            'address'       => isset($data['address']) ? $data['address'] : '',
            'wechat'        => isset($data['wx']) ? $data['wx'] : '',
        ];

        if (\YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
            $member_model = MemberModel::getMemberById(\YunShop::app()->getMemberId());
            $member_model->setRawAttributes($member_data);

            $member_shop_info_model = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId());
            $member_shop_info_model->setRawAttributes($member_shop_info_data);

            $member_validator           = $member_model->validator($member_model->getAttributes());
            $member_shop_info_validator = $member_shop_info_model->validator($member_shop_info_model->getAttributes());

            if ($member_validator->fails()) {
                $warnings     = $member_validator->messages();
                $show_warning = $warnings->first();

                return $this->errorJson($show_warning);
            }

            if ($member_shop_info_validator->fails()) {
                $warnings     = $member_shop_info_validator->messages();
                $show_warning = $warnings->first();
                return $this->errorJson($show_warning);
            }

            //自定义表单
            $member_form = (new MemberService())->updateMemberForm($data);

            if (!empty($member_form)) {
                $member_shop_info_model->member_form = json_encode($member_form);
            }

            if ($member_model->save() && $member_shop_info_model->save()) {
                if (Cache::has($member_model->uid . '_member_info')) {
                    Cache::forget($member_model->uid . '_member_info');
                }

                $phoneModel = PhoneAttribution::getMemberByID(\YunShop::app()->getMemberId());
                if (!is_null($phoneModel)) {
                    $phoneModel->delete();
                }

                //手机归属地查询插入
                $phoneData         = file_get_contents((new PhoneAttributionService())->getPhoneApi($member_model->mobile));
                $phoneArray        = json_decode($phoneData);
                $phone['uid']      = \YunShop::app()->getMemberId();
                $phone['uniacid']  = \YunShop::app()->uniacid;
                $phone['province'] = $phoneArray->data->province;
                $phone['city']     = $phoneArray->data->city;
                $phone['sp']       = $phoneArray->data->sp;

                $phoneModel = new PhoneAttribution();
                $phoneModel->updateOrCreate(['uid' => \YunShop::app()->getMemberId()], $phone);

                return $this->successJson('用户资料修改成功');
            } else {
                return $this->errorJson('更新用户资料失败');
            }
        } else {
            return $this->errorJson('用户不存在');
        }
    }

    /**
     * 绑定手机号
     *
     */
    public function bindMobile()
    {
        $mobile           = \YunShop::request()->mobile;
        $password         = \YunShop::request()->password;
        $confirm_password = \YunShop::request()->password;
        $uid              = \YunShop::app()->getMemberId();
        $close_invitecode = \YunShop::request()->close;


        $member_model = MemberModel::getMemberById($uid);
        \Log::info('member_model--', $member_model);
        if (\YunShop::app()->getMemberId() && $uid > 0) {
            $check_code = MemberService::checkCode();

            if ($check_code['status'] != 1) {
                return $this->errorJson($check_code['json']);
            }

            if (empty($close_invitecode)) {

                $invitecode = MemberService::inviteCode();

                if ($invitecode['status'] != 1) {
                    return $this->errorJson($invitecode['json']);
                }

                file_put_contents(storage_path("logs/" . date('Y-m-d') . "_invitecode.log"),
                    print_r(\YunShop::app()->getMemberId() . '-' . \YunShop::request()->invite_code . '-bind' . PHP_EOL,
                        1), FILE_APPEND);

                //邀请码
                $parent_id = \app\common\models\Member::getMemberIdForInviteCode();
                if (!is_null($parent_id)) {
                    file_put_contents(storage_path("logs/" . date('Y-m-d') . "_invitecode.log"),
                        print_r(\YunShop::app()->getMemberId() . '-' . \YunShop::request()->invite_code . '-' . $parent_id . '-bind' . PHP_EOL,
                            1), FILE_APPEND);
                    MemberShopInfo::change_relation($uid, $parent_id);
                    
                    //增加邀请码使用记录
                    $codemodel = new \app\common\models\member\MemberInvitationCodeLog;

                    if (!$codemodel->where('member_id', $uid)->where('mid', $parent_id)->first()) {
                        $codemodel->uniacid = \YunShop::app()->uniacid;
                        $codemodel->invitation_code = trim(\YunShop::request()->invite_code);
                        $codemodel->member_id = $uid; //使用者id
                        $codemodel->mid = $parent_id; //邀请人id
                        $codemodel->save();
                    }
                }
            }

            $msg = MemberService::validate($mobile, $password, $confirm_password);

            if ($msg['status'] != 1) {
                return $this->errorJson($msg['json']);
            }

            //手机归属地查询插入
            $phoneData         = file_get_contents((new PhoneAttributionService())->getPhoneApi($mobile));
            $phoneArray        = json_decode($phoneData);
            $phone['uid']      = $uid;
            $phone['uniacid']  = \YunShop::app()->uniacid;
            $phone['province'] = $phoneArray->data->province;
            $phone['city']     = $phoneArray->data->city;
            $phone['sp']       = $phoneArray->data->sp;

            $phoneModel = new PhoneAttribution();
            $phoneModel->updateOrCreate(['uid' => $uid], $phone);

            //同步信息
            $old_member = [];
            if (OnekeyLogin::alipayPluginMobileState()) {
                $old_member = MemberModel::getId(\YunShop::app()->uniacid, $mobile);
            }
            if ($old_member) {
                if ($old_member->uid == $member_model->uid) {
                    \Log::debug('同步的会员uid相同:' . $old_member->uid);
                    return $this->errorJson('手机号已绑定其他用户');
                }

                $bool = $this->synchro($member_model, $old_member);
                if ($bool) {
                    if (Cache::has($member_model->uid . '_member_info')) {
                        Cache::forget($member_model->uid . '_member_info');
                    }
                    return $this->successJson('信息同步成功');
                } else {
                    return $this->errorJson('手机号已绑定其他用户');
                }

            } else {
                $salt                   = Str::random(8);
                $member_model->salt     = $salt;
                $member_model->mobile   = $mobile;
                $member_model->password = md5($password . $salt);
                \Log::info('member_save', $member_model);
                if ($member_model->save()) {

                    if (Cache::has($member_model->uid . '_member_info')) {
                        Cache::forget($member_model->uid . '_member_info');
                    }

                    return $this->successJson('手机号码绑定成功');
                } else {
                    return $this->errorJson('手机号码绑定失败');
                }

            }
        } else {
            return $this->errorJson('手机号或密码格式错误');
        }
    }

    //会员信息同步
    public function synchro($new_member, $old_member)
    {

        $type = \YunShop::request()->type;

        \Log::debug('会员同步type:' . $type);
        $type = empty($type) ? Client::getType() : $type;

        $className = SynchronousUserInfo::create($type);

        if ($className) {
            return $className->updateMember($old_member, $new_member);

        } else {
            return false;
        }
    }

    /**
     * 绑定提现手机号
     *
     */
    public function bindWithdrawMobile()
    {
        $mobile = \YunShop::request()->mobile;

        $member_model = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (\YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
            $check_code = MemberService::checkCode();

            if ($check_code['status'] != 1) {
                return $this->errorJson($check_code['json']);
            }

            $salt                          = Str::random(8);
            $member_model->withdraw_mobile = $mobile;

            if ($member_model->save()) {
                return $this->successJson('手机号码绑定成功');
            } else {
                return $this->errorJson('手机号码绑定失败');
            }
        } else {
            return $this->errorJson('手机号或密码格式错误');
        }
    }

    /**
     * @name 微信JSSDKConfig
     * @author
     *
     * @param int $goods_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function wxJsSdkConfig()
    {
        $url = \YunShop::request()->url;
        $pay = \Setting::get('shop.pay');

        if (!empty($pay['weixin_appid']) && !empty($pay['weixin_secret'])) {
            $app_id = $pay['weixin_appid'];
            $secret = $pay['weixin_secret'];
        } else {
            $account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

            $app_id = $account->key;
            $secret = $account->secret;
        }

        $options = [
            'app_id' => $app_id,
            'secret' => $secret
        ];

        $app = new Application($options);

        $js = $app->js;
        $js->setUrl($url);

        $config = $js->config(array(
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'showOptionMenu',
            'scanQRCode',
            'updateAppMessageShareData',
            'updateTimelineShareData'
        ));
        $config = json_decode($config, 1);

        $info = [];

        if (\YunShop::app()->getMemberId()) {
            $info = Member::getUserInfos(\YunShop::app()->getMemberId())->first();

            if (!empty($info)) {
                $info = $info->toArray();
            }
        }

        $share = \Setting::get('shop.share');

        if ($share) {
            if ($share['icon']) {
                $share['icon'] = replace_yunshop(yz_tomedia($share['icon']));
            }
        }

        $shop = \Setting::get('shop');
//        dd($shop);
        $shop['icon'] = replace_yunshop(yz_tomedia($shop['logo']));
//        if ($shop){
//            $shop['name'] = $shop['shop']['name'];
//        }
        if (!is_null(\Config('customer_service'))) {
            $class    = array_get(\Config('customer_service'), 'class');
            $function = array_get(\Config('customer_service'), 'function');
            $ret      = $class::$function(request()->goods_id);
            if ($ret) {
                $shop['cservice'] = $ret;
            }
        }
        if (is_null($share) && is_null($shop)) {
            $share = [
                'title' => '商家分享',
                'icon'  => '#',
                'desc'  => '商家分享'
            ];
        }
//        if(is_null($share['desc'])){
//            $share['desc'] = "";
//        }
        $data = [
            'config' => $config,
            'info'   => $info,   //商城设置
            'shop'   => $shop,
            'share'  => $share   //分享设置
        ];
        return $this->successJson('', $data);
    }

    /**
     * 申请协议
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyProtocol()
    {
        $protocol = Setting::get('apply_protocol');

        if ($protocol) {
            return $this->successJson('获取数据成功!', $protocol);
        }
        return $this->successJson('未检测到数据!', []);
    }

    /**
     * 上传图片
     *
     * @return string
     */
    public function uploadImg()
    {
        $img = ImageHelper::upload(\YunShop::request()->name);

        return $img;
    }

    /**
     * 推广基本设置
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function AgentBase()
    {
        $info = \Setting::get('relation_base');

        if ($info) {
            return $this->successJson('', [
                'banner' => replace_yunshop(yz_tomedia($info['banner']))
            ]);
        }

        return $this->errorJson('暂无数据', []);
    }

    public function guideFollow(Request $request)
    {

        $member_id = \YunShop::app()->getMemberId();

        if (empty($member_id)) {
            return $this->errorJson('用户未登录', []);
        }
        if($request->type==1) {

            $set = \Setting::get('shop.share');
            $fans_model = McMappingFans::getFansById($member_id);
            $mid = \app\common\models\Member::getMid();
           

            if (!empty($set['follow_url']) && $fans_model->follow === 0) {

                if ($mid != null && $mid != 'undefined' && $mid > 0) {
                    $member_model = Member::getMemberById($mid);

                    $logo = $member_model->avatar;
                    $text = $member_model->nickname;
                } else {
                    $setting = Setting::get('shop');
                    $account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

                    $logo = replace_yunshop(tomedia($setting['shop']['logo']));
                    $text = $account->name;
                }

                return $this->successJson('', [
                   
                    'logo' => $logo,
                    'text' => $text,
                    'url' => $set['follow_url']
                ]);
            }
        }
        return $this->errorJson('暂无数据', []);
    }

    /**
     * 会员中心推广二维码(包含会员是否有生成海报权限)
     *
     * @param $isAgent
     *
     * @return string
     */
    private function getPoster($isAgent)
    {
        if (\YunShop::plugin()->get('poster')) {
            if (\Schema::hasColumn('yz_poster', 'center_show')) {
                $posterModel = Poster::uniacid()->select('id', 'is_open')->where('center_show', 1)->first();
                if (($posterModel && $posterModel->is_open) || ($posterModel && !$posterModel->is_open && $isAgent)) {
                    $file_path = (new CreatePosterService(\YunShop::app()->getMemberId(),
                        $posterModel->id))->getMemberPosterPath();
                    return request()->getSchemeAndHttpHost() . '/' . substr($file_path, strpos($file_path, 'addons'));
                }
            }
        }
        return $this->createPoster();
    }

    //todo 此处海报生成是否可以公用超级海报代码  vs YITIAN
    //合成推广海报
    private function createPoster()
    {
        $width  = 320;
        $height = 540;

        $logo_width  = 40;
        $logo_height = 40;

        $font_size      = 15;
        $font_size_show = 20;

        $member_id = \YunShop::app()->getMemberId();

        $shopInfo = Setting::get('shop.shop');
        $shopName = $shopInfo['name'] ?: '商城'; //todo 默认值需要更新
        $shopLogo = $shopInfo['logo'] ? replace_yunshop(yz_tomedia($shopInfo['logo'])) : base_path() . '/static/images/logo.png'; //todo 默认值需要更新
        $shopImg  = $shopInfo['signimg'] ? replace_yunshop(yz_tomedia($shopInfo['signimg'])) : base_path() . '/static/images/photo-mr.jpg'; //todo 默认值需要更新

        $str_lenght = $logo_width + $font_size_show * mb_strlen($shopName);

        $space = ($width - $str_lenght) / 2;

        $uniacid = \YunShop::app()->uniacid;
        $path    = storage_path('app/public/personalposter/' . $uniacid);
        if (!file_exists($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $md5    = md5($member_id . $shopInfo['name'] . $shopInfo['logo'] . $shopInfo['signimg']); //用于标识组成元素是否有变化
        $extend = '.png';
        $file   = $md5 . $extend;

        if (!file_exists($path . '/' . $file)) {
            $targetImg = imagecreatetruecolor($width, $height);
            $white     = imagecolorallocate($targetImg, 255, 255, 255);
            imagefill($targetImg, 0, 0, $white);

            $imgSource      = imagecreatefromstring(\Curl::to($shopImg)->get());
            $logoSource     = imagecreatefromstring(\Curl::to($shopLogo)->get());
            $qrcode         = MemberModel::getAgentQR();
            $qrSource       = imagecreatefromstring(\Curl::to($qrcode)->get());
            $fingerPrintImg = imagecreatefromstring(file_get_contents(base_path() . '/static/app/images/ewm.png'));
            $mergeData      = [
                'dst_left'   => $space,
                'dst_top'    => 10,
                'dst_width'  => $logo_width,
                'dst_height' => $logo_height,
            ];
            self::mergeImage($targetImg, $logoSource, $mergeData); //合并商城logo图片
            $mergeData = [
                'size' => $font_size,
                'left' => $space + $logo_width + 10,
                'top'  => 37,
            ];
            self::mergeText($targetImg, $shopName, $mergeData);//合并商城名称(文字)
            $mergeData = [
                'dst_left'   => 0,
                'dst_top'    => 60,
                'dst_width'  => 320,
                'dst_height' => 320,
            ];
            self::mergeImage($targetImg, $imgSource, $mergeData); //合并商城海报图片
            $mergeData = [
                'dst_left'   => 0,
                'dst_top'    => 380,
                'dst_width'  => 160,
                'dst_height' => 160,
            ];
            self::mergeImage($targetImg, $fingerPrintImg, $mergeData); //合并指纹图片
            $mergeData = [
                'dst_left'   => 160,
                'dst_top'    => 380,
                'dst_width'  => 160,
                'dst_height' => 160,
            ];
            self::mergeImage($targetImg, $qrSource, $mergeData); //合并二维码图片

            header("Content-Type: image/png");
            $imgPath = $path . "/" . $file;
            imagepng($targetImg, $imgPath);
        }

        $imgUrl = request()->getSchemeAndHttpHost() . '/' . substr($path, strpos($path, 'addons')) . '/' . $file;
        return $imgUrl;
    }

    //合并图片并指定图片大小
    private static function mergeImage($destinationImg, $sourceImg, $data)
    {
        $w = imagesx($sourceImg);
        $h = imagesy($sourceImg);
        imagecopyresized($destinationImg, $sourceImg, $data['dst_left'], $data['dst_top'], 0, 0, $data['dst_width'],
            $data['dst_height'], $w, $h);
        imagedestroy($sourceImg);
        return $destinationImg;
    }

    //合并字符串
    private static function mergeText($destinationImg, $text, $data)
    {
        putenv('GDFONTPATH=' . IA_ROOT . '/addons/yun_shop/static/fonts');
        $font = "source_han_sans";

        $black = imagecolorallocate($destinationImg, 0, 0, 0);
        imagettftext($destinationImg, $data['size'], 0, $data['left'], $data['top'], $black, $font, $text);
        return $destinationImg;
    }

    public function memberInfo()
    {
        $member_id = \YunShop::request()->uid;

        if (empty($member_id)) {
            return $this->errorJson('会员不存在');
        }

        $member_info = MemberModel::getMemberById($member_id);

        return $this->successJson('', $member_info);
    }

    public function forget()
    {
        Session::clear('member_id');

        redirect(Url::absoluteApp('home'))->send();
    }

    public function memberFromHXQModule()
    {
        $uniacid   = \YunShop::app()->uniacid;
        $member_id = \YunShop::request()->uid;

        if (!empty($member_id)) {
            $member_shop_info_model = MemberShopInfo::getMemberShopInfo($member_id);

            if (is_null($member_shop_info_model)) {
                (new MemberService)->addSubMemberInfo($uniacid, (int)$member_id);
            }

            $mid = \YunShop::request()->mid ?: 0;

            Member::createRealtion($member_id, $mid);

            \Log::debug('------HXQModule---------' . $member_id);
            \Log::debug('------HXQModule---------' . $mid);

            return json_encode(['status' => 1, 'result' => 'ok']);
        }

        return json_encode(['status' => 0, 'result' => 'uid为空']);
    }

    /**
     * 同步模块支付宝用户
     * @return string
     */
    public function dsAlipayUserModule()
    {
        $uniacid   = \YunShop::app()->uniacid;
        $member_id = \YunShop::request()->uid;
        $userInfo  = \YunShop::request()->user_info;

        if (!is_array($userInfo)) {
            $userInfo = json_decode($userInfo, true);
        }

        if (!empty($member_id)) {

            if (app('plugins')->isEnabled('alipay-onekey-login') && $userInfo) {
                $bool = MemberAlipay::insertData($userInfo, ['member_id' => $member_id, 'uniacid' => $uniacid]);
                if (!$bool) {
                    return json_encode(['status' => 0, 'result' => '支付宝用户信息保存失败']);
                }
            } else {
                return json_encode(['status' => 0, 'result' => '未开启插件或未接受到支付宝用户信息']);
            }

            $member_shop_info_model = MemberShopInfo::getMemberShopInfo($member_id);

            if (is_null($member_shop_info_model)) {
                (new MemberService)->addSubMemberInfo($uniacid, (int)$member_id);
            }

            $mid = \YunShop::request()->mid ?: 0;

            Member::createRealtion($member_id, $mid);

            \Log::debug('------HXQModule---------' . $member_id);
            \Log::debug('------HXQModule---------' . $mid);

            return json_encode(['status' => 1, 'result' => 'ok']);
        }

        return json_encode(['status' => 0, 'result' => 'uid为空']);
    }


    public function getCustomField()
    {
        // member.member.get-custom-field
        $member = Setting::get('shop.member');
        $data   = [
            'is_custom'    => $member['is_custom'],
            'custom_title' => $member['custom_title'],
            'is_validity'  => $member['level_type'] == 2 ? true : false,
            'term'         => $member['term'] ? $member['term'] : 0,
        ];
        return $this->successJson('获取自定义字段成功！', $data);
    }

    public function saveCustomField()
    {
        // member.member.sava-custom-field
        $member_id    = \YunShop::app()->getMemberId();
        $custom_value = \YunShop::request()->get('custom_value');

        $data    = [
            'custom_value' => $custom_value,
        ];
        $request = MemberShopInfo::where('member_id', $member_id)->update($data);
        if ($request) {
            return $this->successJson('保存成功！', []);
        }
        return $this->successJson('保存失败！', []);
    }

    public function withdrawByMobile()
    {
        $trade = \Setting::get('shop.trade');

        if ($trade['is_bind'] && \YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
            $member_model = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId());

            if ($member_model && $member_model->withdraw_mobile) {
                $is_bind_mobile = 0;
            } else {
                $is_bind_mobile = 1;
            }
        } else {
            $is_bind_mobile = 0;
        }

        return $this->successJson('', ['is_bind_mobile' => $is_bind_mobile]);
    }

    /**
     * 修复关系链
     *
     * 历史遗留问题
     */
    public function fixRelation()
    {
        set_time_limit(0);
        //获取修改数据
        $members = MemberShopInfo::uniacid()
            ->where('parent_id', '!=', 0)
            ->where('is_agent', 1)
            ->where('status', 2)
            ->where('relation', '')
            ->orWhereNull('relation')
            ->orWhere('relation', '0,')
            ->whereNull('deleted_at')
            ->get();

        if (!$members->isEmpty()) {
            foreach ($members as $member) {
                //yz_members
                if ($member->is_agent == 1 && $member->status == 2) {
                    Member::setMemberRelation($member->member_id, $member->parent_id);
                }
            }
        }

        echo 'yz_member修复完毕<BR>';

        //yz_agents
        //获取修改数据
        $agents = Agents::uniacid()
            ->where('parent_id', '!=', 0)
            ->whereNull('deleted_at')
            ->where('parent', '')
            ->orWhereNull('parent')
            ->orWhere('parent', '0,')
            ->get();

        foreach ($agents as $agent) {
            $rows = DB::table('yz_member')
                ->select()
                ->where('uniacid', $agent->uniacid)
                ->where('member_id', $agent->member_id)
                ->where('parent_id', $agent->parent_id)
                ->where('is_agent', 1)
                ->where('status', 2)
                ->whereNull('deleted_at')
                ->first();

            if (!empty($rows)) {
                $agent->parent = $rows['relation'];

                $agent->save();
            }
        }

        echo 'yz_agents修复完毕';
    }

    public function memberRelationFilter()
    {
        $data = MemberModel::filterMemberRoleAndLevel();

        return $this->successJson('', $data);
    }

    public function isOpenRelation()
    {
        $data = ['switch' => 0];

//        $relation = MemberRelation::getSetInfo()->first();
        /*
                if (!is_null($relation) && 1 == $relation->status) {
                    $data = [
                        'switch' => 1
                    ];
                }
        */
        $switch = Setting::get('shop_app.pay.switch');
        if (isset($switch) && $switch == 0 && \YunShop::request()->type == 7) {
            $switch = 0;
        } else {
            $switch = 1;
        }

        //是否显示我的推广
        $switch = PortType::popularizeShow(\YunShop::request()->type);

        $data = [
            'switch' => $switch
        ];

        return $this->successJson('', $data);
    }

    public function anotherShare()
    {
        $order_ids = \YunShop::request()->order_ids;
        $mid       = \YunShop::app()->getMemberId();

        if (empty($order_ids)) {
            return $this->errorJson('参数错误', '');
        }

        if (empty($mid)) {
            return $this->errorJson('用户未登陆', '');
        }

        $title = Setting::get('shop.pay.another_share_title');
        $url   = yzAppFullUrl('/member/payanotherdetail', ['pid' => $mid, 'order_ids' => $order_ids]);

        $order_goods = Order::find($order_ids)->hasManyOrderGoods;

        if (is_null($order_goods)) {
            return $this->errorJson('订单商品不存在', '');
        }

        if (empty($title)) {
            $title = '土豪大大，跪求代付';
        }

        $data = [
            'title'   => $title,
            'url'     => $url,
            'content' => $order_goods[0]->title,
            'img'     => replace_yunshop(yz_tomedia($order_goods[0]->thumb))
        ];

        return $this->successJson('', $data);
    }

   public function getEnablePlugins()
    {
        $filter = [
            'conference',
            //'store-cashier',
            'recharge-code'
        ];
        
        $diyarr = [
            'tool' => ['separate'],
            'asset_equity' => ['integral','credit','asset'],
            'merchant' => ['supplier', 'kingtimes', 'hotel', 'store-cashier'],
            'market' => ['ranking','article','clock_in','conference', 'video_demand', 'enter_goods', 'universal_card', 'recharge_code','business_card']
        ];

        $data   = [];
       
            collect(app('plugins')->getPlugins())->filter(function ($item) use ($filter) {

                if (1 == $item->isEnabled()) {
                    $info = $item->toArray();

                    if (in_array($info['name'], $filter)) {
                        return $item;
                    }
                }
            })->each(function ($item) use (&$data) {
                $info = $item->toArray();

                $name = $info['name'];
                //todo 门店暂时不传

                if ($info['name'] == "store-cashier") {
                    $name = 'store_cashier';
                } elseif ($info['name'] == 'recharge-code') {
                    $name  = 'recharge_code';
                    $class = 'icon-member-recharge1';
                    $url   = 'rechargeCode';
                } elseif ($info['name'] == 'conference') {
                    $name  = 'conference';
                    $class = 'icon-member-act-signup1';
                    $url   = 'conferenceList';
                }

                $data[] = [
                    'name'  => $name,
                    'title' => $info['title'],
                    'class' => $class,
                    'url'   => $url
                ];
            });
            if (app('plugins')->isEnabled('asset')) {
                $data[] = [
                    'name'  => 'asset',
                    'title' => PLUGIN_ASSET_NAME,
                    'class' => 'icon-member-credit01',
                    'url'   => 'TransHome'
                ];
            }

            if (app('plugins')->isEnabled('business-card')) {
                $is_open = Setting::get('business-card.is_open');
                if($is_open == 1){
                    $data[] = [
                        'name'  => 'business_card',
                        'title' => '名片',
                        'class' => 'icon-member_card1',
                        'url'   => 'CardCenter'
                    ];
                }
            }
dd($data);
            if (app('plugins')->isEnabled('credit')) {
                $credit_setting = Setting::get('plugin.credit');
                if ($credit_setting && 1 == $credit_setting['is_credit']) {
                    $data[] = [
                        'name'  => 'credit',
                        'title' => '信用值',
                        'class' => 'icon-member-credit01',
                        'url'   => 'creditInfo'
                    ];
                }
            }
            if (app('plugins')->isEnabled('ranking')) {
                $ranking_setting = Setting::get('plugin.ranking');

                if ($ranking_setting && 1 == $ranking_setting['is_ranking']) {
                    $data[] = [
                        'name'  => 'ranking',
                        'title' => '排行榜',
                        'class' => 'icon-member-bank-list1',
                        'url'   => 'rankingIndex'
                    ];
                }
            }

            if (app('plugins')->isEnabled('article')) {
                $article_setting = Setting::get('plugin.article');

                if ($article_setting) {
                    $data[] = [
                        'name'  => 'article',
                        'title' => $article_setting['center'] ? $article_setting['center'] : '文章中心',
                        'class' => 'icon-member-collect1',
                        'url'   => 'notice',
                        'param' => 0,
                    ];
                }
            }

            if (app('plugins')->isEnabled('clock-in')) {
                $clockInService = new \Yunshop\ClockIn\services\ClockInService();
                $pluginName     = $clockInService->get('plugin_name');

                $clock_in_setting = Setting::get('plugin.clock_in');

                if ($clock_in_setting && 1 == $clock_in_setting['is_clock_in']) {
                    $data[] = [
                        'name'  => 'clock_in',
                        'title' => $pluginName,
                        'class' => 'icon-member-get-up',
                        'url'   => 'ClockPunch',
                    ];
                }
            }

            if (app('plugins')->isEnabled('video-demand')) {

                $video_demand_setting = Setting::get('plugin.video_demand');

                if ($video_demand_setting && $video_demand_setting['is_video_demand']) {
                    $data[] = [
                        'name'  => 'video_demand',
                        'title' => '课程中心',
                        'class' => 'icon-member-course3',
                        'url'   => 'CourseManage',
                    ];
                }
            }

            if (app('plugins')->isEnabled('help-center')) {

                $help_center_setting = Setting::get('plugin.help_center');

                if ($help_center_setting && 1 == $help_center_setting['status']) {
                    $data[] = [
                        'name'  => 'help_center',
                        'title' => '帮助中心',
                        'class' => 'icon-member-help',
                        'url'   => 'helpcenter'
                    ];
                }
            }

            if (app('plugins')->isEnabled('courier')) {
                $courier_setting = Setting::get('courier.courier');

                if ($courier_setting && 1 == $courier_setting['radio']) {
                    $data[] = [
                        'name'  => 'courier',
                        'title' => $courier_setting['name'] ? $courier_setting['name'] : '快递单',
                        'class' => 'icon-member-express',
                        'url'   => 'courier'
                    ];
                }
            }

            if (app('plugins')->isEnabled('store-cashier')) {
                $store = \Yunshop\StoreCashier\common\models\Store::getStoreByUid(\YunShop::app()->getMemberId())->first();
                if (!$store) {
                    $data[] = [
                        'name'  => 'store-cashier',
                        'title' => '门店申请',
                        'class' => 'icon-member-store-apply1',
                        'url'   => 'storeApply',

                    ];
                }
            }
            if (app('plugins')->isEnabled('supplier')) {
                $supplier_setting = Setting::get('plugin.supplier');

                $supplier         = \Yunshop\Supplier\common\models\Supplier::getSupplierByMemberId(\YunShop::app()->getMemberId(),
                    1);

                if (!$supplier) {
                    $data[] = [
                        'name'  => 'supplier',
                        'title' => '供应商申请',
                        'class' => 'icon-member-apply1',
                        'url'   => 'supplier',
                    ];
                } elseif ($supplier_setting && 1 == $supplier_setting['status']) {
                    $data[] = [
                        'name'  => 'supplier',
                        'title' => $supplier_setting['name'] ? $supplier_setting['name'] : '供应商管理',
                        'class' => 'icon-member-supplier',
                        'url'   => 'SupplierCenter'
                    ];
                }
            }
            if (app('plugins')->isEnabled('kingtimes')) {
                $provider    = Provider::select(['id', 'uid', 'status'])->where('uid',
                    \YunShop::app()->getMemberId())->first();
                $distributor = Distributor::select(['id', 'uid', 'status'])->where('uid',
                    \YunShop::app()->getMemberId())->first();

                if ($provider) {    

                    if ($provider->status == 1) {
                        $data[] = [
                            'name'  => 'kingtimes',
                            'title' => '补货商中心',
                            'class' => 'icon-member-replenishment',
                            'url'   => 'ReplenishmentApply',
                        ];
                    }
                } else {
                    $data[] = [
                        'name'  => 'kingtimes',
                        'title' => '补货商申请',
                        'class' => 'icon-member-replenishment',
                        'url'   => 'ReplenishmentApply',
                    ];
                }
                if ($distributor) {
                    if ($distributor->status == 1) {
                        $data[] = [
                            'name'  => 'kingtimes',
                            'title' => '配送站中心',
                            'class' => 'icon-member-express-list',
                            'url'   => 'DeliveryTerminalApply',
                        ];
                    }
                } else {
                    $data[] = [
                        'name'  => 'kingtimes',
                        'title' => '配送站申请',
                        'class' => 'icon-member-express-list',
                        'url'   => 'DeliveryTerminalApply',
                    ];
                }
                // dd($data);

            }
            if (app('plugins')->isEnabled('enter-goods')) {

                $data[] = [
                    'name'  => 'enter_goods',
                    'title' => '用户入驻',
                    'class' => 'icon-member_goods',
                    'url'   => 'EnterShop',
                ];
            }

            if (app('plugins')->isEnabled('integral')) {
                $status = \Yunshop\Integral\Common\Services\SetService::getIntegralSet();

                if ($status['member_show']) {
                    $data[] = [
                        'name'  => 'integral',
                        'title' => $status['plugin_name'] ?: '消费积分',
                        'class' => 'icon-member_integral',
                        'url'   => 'Integral_love',
                    ];
                }
            }

            if (app('plugins')->isEnabled('universal-card')) {
                $set = \Yunshop\UniversalCard\services\CommonService::getSet();
                //判断插件开关
                if ($set['switch']) {
                    $shopSet = \Setting::get('shop.member');
                    //判断商城升级条件是否为指定商品
                    if ($shopSet['level_type'] == 2) {
                        $data[] = [
                            'name'  => 'universal_card',
                            'title' => $set['name'],
                            'class' => 'icon-card',
                            'url'   => 'CardIndex'
                        ];
                    }
                }
            }

            if (app('plugins')->isEnabled('separate')) {
                $setting = \Setting::get('plugin.separate');
                if ($setting && 1 == $setting['separate_status']) {
                    $data[] = [
                        'name'  => 'separate',
                        'title' => '绑定银行卡',
                        'class' => 'icon-member_card',
                        'url'   => 'BankCard'
                    ];
                }
            }
            
            if (app('plugins')->isEnabled('hotel')) {
                $hotel = \Yunshop\Hotel\common\models\Hotel::getHotelByUid(\YunShop::app()->getMemberId())->first();
                if ($hotel) {
                    $data[] = [
                        'name'  => 'hotel',
                        'title' => '酒店管理',
                        'class' => 'icon-member_hotel',
                        'url'   => 'HotelManage'
                    ];
                } else {
                    $data[] = [
                        'name'  => 'hotel',
                        'title' => '酒店申请',
                        'class' => 'icon-member-hotel-apply',
                        'url'   => 'hotelApply'
                    ];
                }
            }

        foreach ($data as $k => $v) {

            if (in_array($v['name'], $diyarr['tool'])) {
                $arr['tool'][] = $v;
            }
            if (in_array($v['name'], $diyarr['asset_equity'])) {
                $arr['asset_equity'][] = $v;
            }
            if (in_array($v['name'], $diyarr['merchant'])) {
                $arr['merchant'][] = $v;
            }
            if (in_array($v['name'], $diyarr['market'])) {
                $arr['market'][] = $v;
            }
        }


        //return $this->successJson('ok', $data);

        if (app('plugins')->isEnabled('designer')) {
            //获取所有模板
            $sets = \Yunshop\Designer\models\ViewSet::uniacid()->select('names', 'type')->get()->toArray();

            if (!$sets) {
                $arr['ViewSet'] = [];
            } else {

                foreach ($sets as $k => $v) {

                    $arr['ViewSet'][$v['type']]['name'] = $v['names'];
                    $arr['ViewSet'][$v['type']]['name'] = $v['names'];
                }
            }
        }

        
        return $this->successJson('ok', $arr);

    }


    public function isOpenHuanxun()
    {
        $huanxun = \Setting::get('plugin.huanxun_set');

        if (app('plugins')->isEnabled('huanxun')) {
            if ($huanxun['withdrawals_switch']) {
                return $this->successJson('', $huanxun['withdrawals_switch']);
            }
        }
        return $this->errorJson('', 0);
    }


    /**
     *  推广申请页面数据
     */
    public function shareinfo()
    {

        $data = MemberRelation::uniacid()->where(['status' => 1])->get();

        $become_term = unserialize($data[0]['become_term']);

        $goodsid = explode(',', $data[0]['become_goods_id']);

        foreach ($goodsid as $key => $val) {

            $online_good = Goods::where('status', 1)
                ->select('id', 'title', 'thumb', 'price', 'market_price')
                ->find($val);

            if ($online_good) {
                $online_good['thumb'] = replace_yunshop(yz_tomedia($online_good['thumb']));
                $online_goods[]       = $online_good;
                $online_goods_keys[]  = $online_good->id;
            }
        }
        unset($online_good);

        $goodskeys = range(0, count($online_goods_keys) - 1);

        $data[0]['become_goods'] = array_combine($goodskeys, $online_goods);

        $termskeys   = range(0, count($become_term) - 1);
        $become_term = array_combine($termskeys, $become_term);

        $member_uid = \YunShop::app()->getMemberId();

        $status            = $data[0]['become_order'] == 1 ? 3 : 1;
        $getCostTotalNum   = Order::where('status', '=', $status)->where('uid', $member_uid)->count('id');
        $getCostTotalPrice = Order::where('status', '=', $status)->where('uid', $member_uid)->sum('price');

        $data[0]['getCostTotalNum']   = $getCostTotalNum;
        $data[0]['getCostTotalPrice'] = $getCostTotalPrice;

        $terminfo = [];

        foreach ($become_term as $v) {
            if ($v == 2) {
                $terminfo['become_ordercount'] = $data[0]['become_ordercount'];
            }
            if ($v == 3) {
                $terminfo['become_moneycount'] = $data[0]['become_moneycount'];
            }
            if ($v == 4) {
                $terminfo['goodsinfo'] = $data[0]['become_goods'];
            }
            if ($v == 5) {
                $terminfo['become_selfmoney'] = $data[0]['become_selfmoney'];
            }
        }

        $data[0]['become_term'] = $terminfo;

        if ($data[0]['become'] == 2) {
            //或
            $data[0]['tip'] = '满足以下任意条件都可以成为推广员';
        } elseif ($data[0]['become'] == 3) {
            //与
            $data[0]['tip'] = '满足以下所有条件才可以成为推广员';
        }
        return $this->successJson('ok', $data[0]);
    }

    /**
     *  邀请页面验证
     */
    public function memberInviteValidate()
    {
        $invite_code = request()->invite_code;
        $parent = (new MemberShopInfo())->getInviteCodeMember($invite_code);
        $member_invitation_model = new MemberInvitationCodeLog();

        if ($parent) {
            \Log::info('更新上级------'.\YunShop::app()->getMemberId());
            MemberShopInfo::change_relation(\YunShop::app()->getMemberId(), $parent->member_id);

            $member_invitation_model->uniacid = \YunShop::app()->uniacid;
            $member_invitation_model->mid = \YunShop::app()->getMemberId();
            $member_invitation_model->member_id = $parent->member_id;
            $member_invitation_model->invitation_code = $invite_code;
            $member_invitation_model->save();
            return $this->successJson('ok', $parent);
        } else {
            return $this->errorJson('邀请码有误!请重新填写');
        }
    }

    public function isValidatePage()
    {
        $member_id = \YunShop::app()->getMemberId();

        //强制绑定手机号
        if (Cache::has('shop_member')) {
            $member_set = Cache::get('shop_member');
        } else {
            $member_set = Setting::get('shop.member');
        }

        if (!is_null($member_set)) {
            $data = [
                'is_bind_mobile' => $this->isBindMobile($member_set, $member_id),
                'invite_page' => 0,
                'is_invite' => 0,
                'is_login' => 0,
            ];

            if ($data['is_bind_mobile']) {
                return $this->successJson('强制绑定手机开启', $data);
            }

            $type = \YunShop::request()->type;
            $invitation_log = [];
            if ($member_id) {
                $mobile = \app\common\models\Member::where('uid', $member_id)->first();
                if ($mobile->mobile) {
                    $invitation_log = 1;
                } else {
                    $member = MemberShopInfo::uniacid()->where('member_id', $member_id)->first();
                    $invitation_log = MemberInvitationCodeLog::uniacid()->where('member_id', $member->parent_id)->first();
                }
            }

            $invite_page = $member_set['invite_page'] ? 1 : 0;
            $data['invite_page'] = $type == 5 ? 0 : $invite_page;

            $data['is_invite'] = $invitation_log ? 1 : 0;
            $data['is_login'] = $member_id ? 1 : 0;
            return $this->successJson('邀请页面开关', $data);
        }
    }

    public function confirmGoods()
    {
        $member_id = \YunShop::app()->getMemberId();
        $member = MemberShopInfo::getMemberShopInfo($member_id);

        $member_invite_goods_log_model = new MemberInviteGoodsLogController();
        $member_invite_goods_log_model->uniacid = \YunShop::app()->uniacid;
        $member_invite_goods_log_model->member_id = $member_id;
        $member_invite_goods_log_model->parent_id = $member->parent_id;
        $member_invite_goods_log_model->invitation_code = '';

        if ($member_invite_goods_log_model->save()) {
            return $this->successJson('ok');
        }
    }

    public function refuseGoods()
    {
        $invite_code = request()->invite_code;
        $parent = (new MemberShopInfo())->getInviteCodeMember($invite_code);
        $member_invite_goods_log_model = new MemberInviteGoodsLogController();

        if ($parent) {
            \Log::info('更新上级------'.\YunShop::app()->getMemberId());
            MemberShopInfo::change_relation(\YunShop::app()->getMemberId(), $parent->member_id);

            $member_invite_goods_log_model->uniacid = \YunShop::app()->uniacid;
            $member_invite_goods_log_model->member_id = \YunShop::app()->getMemberId();
            $member_invite_goods_log_model->parent_id = $parent->member_id;
            $member_invite_goods_log_model->invitation_code = $invite_code;
            $member_invite_goods_log_model->save();
            return $this->successJson('ok');
        } else {
            return $this->errorJson('邀请码有误!请重新填写');
        }
    }

    public function isValidatePageGoods()
    {
        $member_id = \YunShop::app()->getMemberId();

        if (!$member_id) {
            return $this->errorJson('会员不存在!');
        }

        $invitation_log = MemberInviteGoodsLogController::getLogByMemberId($member_id);

        $result['is_invite'] = $invitation_log ? 1 : 0;

        return $this->successJson('有记录',$result);
    }

    public function getShopSet()
    {
        $shop_set_name = Setting::get('shop.shop.name');
        $default_name  = '商城名称';
        return $this->successJson('ok', $shop_set_name ?: $default_name);
    }


    public function getArticleQr()
    {
        if (app('plugins')->isEnabled('article')) {
            $article_qr_set = Setting::get('plugin.article.qr');
            $qr = MemberModel::getAgentQR();
            if ($article_qr_set == 1) {
                return $this->errorJson('二维码开关关闭!');
            }
            return $this->successJson('获取二维码成功!', $qr);
        }
    }

    public function isBindMobile($member_set, $member_id)
    {
//        $is_bind_mobile = 0;
//
//        if (!is_null($member_set)) {
//            if ((1 == $member_set['is_bind_mobile']) && $member_id && $member_id > 0) {
//                if (Cache::has($member_id . '_member_info')) {
//                    $member_model = Cache::get($member_id . '_member_info');
//                } else {
//                    $member_model = Member::getMemberById($member_id);
//                }
//
//                if ($member_model && empty($member_model->mobile)) {
//                    $is_bind_mobile = 1;
//                }
//            }
//        }
        $is_bind_mobile = 0;

        if ((0 < $member_set['is_bind_mobile']) && $member_id && $member_id > 0) {
            if (Cache::has($member_id . '_member_info')) {
                $member_model = Cache::get($member_id . '_member_info');
            } else {
                $member_model = Member::getMemberById($member_id);
            }

            if ($member_model && empty($member_model->mobile)) {
                $is_bind_mobile = intval($member_set['is_bind_mobile']);
            }
        }
        return $is_bind_mobile;
    }

    public function chkAccount()
    {
        $type = \YunShop::request()->type;
        $mid = Member::getMid();

        if (1 == $type && !Cache::has('chekAccount')) {
            Cache::put('chekAccount', 1, 360);
            $queryString = ['type'=>$type,'session_id'=>session_id(), 'i'=>\YunShop::app()->uniacid, 'mid'=>$mid];

            throw new MemberNotLoginException('请登录', ['login_status' => 0, 'login_url' => Url::absoluteApi('member.login.chekAccount', $queryString)]);
        }
    }
}