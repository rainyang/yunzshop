<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\backend\modules\member\models\MemberRelation;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\AccountWechats;
use app\common\models\Area;
use app\common\models\Goods;
use app\common\models\MemberShopInfo;
use app\common\models\Order;
use app\frontend\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\models\OrderListModel;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Str;


class MemberController extends ApiController
{
    protected $publicAction = ['wxJsSdkConfig'];

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getUserInfo()
    {
        $member_id = \YunShop::app()->getMemberId();

        if (!empty($member_id)) {
            $member_info = MemberModel::getUserInfos($member_id)->first();

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();

                $data = MemberModel::userData($member_info, $member_info['yz_member']);

                return $this->successJson('', $data);
            } else {
                return $this->errorJson('用户不存在');
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
        $info = MemberRelation::getSetInfo()->first()->toArray();

        $member_info = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (empty($info)) {
            return $this->errorJson('缺少参数');
        }

        if (empty($member_info))
        {
            return $this->errorJson('会员不存在');
        } else {
            $data = $member_info->toArray();
        }

        $account = AccountWechats::getAccountInfoById(\YunShop::app()->uniacid);
        switch ($info['become']) {
            case 0:
            case 1:
                $apply_qualification = 1;
                $mid = \YunShop::request()->mid ? \YunShop::request()->mid : 0;
                $parent_name = '';

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
               $cost_num  = OrderListModel::getCostTotalNum(\YunShop::app()->getMemberId());

               if ($info['become_check'] && $cost_num >= $info['become_ordercount']) {
                   $apply_qualification = 5;
               }
               break;
           case 3:
               $apply_qualification = 3;
               $cost_price  = OrderListModel::getCostTotalPrice(\YunShop::app()->getMemberId());

               if ($info['become_check'] && $cost_price >= $info['become_moneycount']) {
                   $apply_qualification = 6;
               }
               break;
           case 4:
               $apply_qualification = 4;
               $goods = Goods::getGoodsById($info['become_goods_id']);
               $goods_name = '';

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
           'become' => $apply_qualification,
           'become1' => ['shop_name' => $account['name'],'parent_name' => $parent_name, 'realname' => $member['realname'], 'mobile' => $member['mobile']],
           'become2' => ['shop_name' => $account['name'], 'total' => $info['become_ordercount'], 'cost' => $cost_num],
           'become3' => ['shop_name' => $account['name'], 'total' => $info['become_moneycount'], 'cost' => $cost_price],
           'become4' =>['shop_name' => $account['name'], 'goods_name' => $goods_name, 'goods_id' => $info['become_goods_id']],
           'is_agent' => $data['is_agent'],
           'status' => $data['status'],
           'account' => $account['name']
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
        $member_info = MemberModel::isAgent();

        if (empty($member_info)) {
            return $this->errorJson('会员不存在');
        } else {
            $data = $member_info;
        }

        return $this->successJson('', ['is_agent' => $data['is_agent']]);
    }

    /**
     * 会员推广二维码
     *
     * @param $url
     * @param string $extra
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgentQR($extra='')
    {
        if (empty(\YunShop::app()->getMemberId())) {
            return $this->errorJson('请重新登录');
        }

        $qr_url = MemberModel::getAgentQR($extra='');

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

        $sub_member_model->status = 1;
        $sub_member_model->apply_time = time();

        if (!$sub_member_model->save()) {
           return $this->errorJson('会员信息保存失败');
        }

        $realname = \YunShop::request()->realname;
        $moible =\YunShop::request()->mobile;

        $member_mode = MemberModel::getMemberById(\YunShop::app()->getMemberId());

        $member_mode->realname = $realname;
        $member_mode->mobile = $moible;

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
         return $this->successJson('', ['count'=>MemberShopInfo::getAgentCount()]);
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
            'my_agent' => $my_agent
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
        $data = \YunShop::request()->data;

        $birthday = explode('-', $data['birthday']);

        $meber_data = [
            'realname' => $data['realname'],
            'mobile' => $data['mobile'],
            'telephone' => $data['telephone'],
            'avatar' => $data['avatar'],
            'gender' => $data['gender'],
            'birthyear' => $birthday[0],
            'birthmonth' => $birthday[1],
            'birthday' => $birthday[2]
        ];

        $member_shop_info_data = [
            'alipay' => $data['alipay'],
            'alipayname' => $data['alipay_name'],
            'province_name' => $data['province_name'],
            'city_name' => $data['city_name'],
            'area_name' => $data['area_name'],
            'province' => $data['province'],
            'city' => $data['city'],
            'area' => $data['area'],
            'address' => $data['address'],
        ];

        if (\YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
            $member_model = MemberModel::getMemberById(\YunShop::app()->getMemberId());
            $member_model->setRawAttributes($meber_data);

            $member_shop_info_model = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId());
            $member_shop_info_model->setRawAttributes($member_shop_info_data);

            $member_validator = $member_model->validator($member_model->getAttributes());
            $member_shop_info_validator = $member_shop_info_model->validator($member_shop_info_model->getAttributes());

            if ($member_validator->fails()) {
                return $this->errorJson($member_validator->messages());
            }

            if ($member_shop_info_validator->fails()) {
                return $this->errorJson($member_shop_info_model->messages());
            }

            if ($member_model->save() && $member_shop_info_model->save()) {
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
        $data = \YunShop::request()->data;

        $member_model = MemberModel::getMemberById(\YunShop::app()->getMemberId());

        if (\YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0
            && MemberService::validate($data['mobile'], $data['password'], $data['confirm_password'])) {
            $salt = Str::random(8);
            $member_model->salt = $salt;
            $member_model->mobile = $data['mobile'];
            $member_model->password = md5($data['password'] . $salt);

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
     * 微信JSSDKConfig
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function wxJsSdkConfig()
    {
        $url = \YunShop::request()->url;
        $pay = \Setting::get('shop.pay');

        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret']
        ];

        $app = new Application($options);

        $js = $app->js;
        $js->setUrl($url);

        $config = $js->config(array('onMenuShareTimeline','onMenuShareAppMessage', 'showOptionMenu'));
        $config = json_decode($config, 1);

        $info = Member::getUserInfos(\YunShop::app()->getMemberId())->first();

        if (!empty($info)) {
            $info = $info->toArray();
        } else {
            $info = [];
        }

        $shop = \Setting::get('shop');
        $shop['logo'] = tomedia($shop['logo']);

        $data = [
            'config' => $config,
            'info' => $info,
            'shop' => $shop
        ];

        return $this->successJson('', $data);
    }

    public function applyProtocol()
    {
       $protocol = Setting::get('apply_protocol');

        if($protocol){
            return $this->successJson('获取数据成功!', $protocol);
        }
        return $this->successJson('未检测到数据!', []);
    }


}