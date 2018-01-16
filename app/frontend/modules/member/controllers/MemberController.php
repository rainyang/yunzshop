<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\backend\modules\member\models\MemberRelation;
use app\backend\modules\order\models\Order;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\helpers\ImageHelper;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\models\Area;
use app\common\models\Goods;
use app\common\models\McMappingFans;
use app\common\models\MemberShopInfo;
use app\common\services\Session;
use app\frontend\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\member\services\MemberService;
use app\frontend\models\OrderListModel;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yunshop\Commission\models\Agents;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\services\CreatePosterService;
use Yunshop\TeamDividend\models\YzMemberModel;

class MemberController extends ApiController
{
    protected $publicAction = ['guideFollow', 'wxJsSdkConfig', 'memberFromHXQModule'];
    protected $ignoreAction = ['guideFollow', 'wxJsSdkConfig', 'memberFromHXQModule'];

    /**
     * 获取用户信息
     *
     *
     */
    public function getUserInfo()
    {
        $member_id = \YunShop::app()->getMemberId();

        if (!empty($member_id)) {
            $member_info = MemberModel::getUserInfos($member_id)->first();

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();

                $data = MemberModel::userData($member_info, $member_info['yz_member']);

                $data = MemberModel::addPlugins($data);

                $data['income'] = MemberModel::getIncomeCount();

                //标识"会员关系链"是否开启(如果没有设置,则默认为未开启),用于前端判断是否显示个人中心的"推广二维码"
                $info = MemberRelation::getSetInfo()->first();
                if (!empty($info)) {
                    $data['relation_switch'] = $info->status == 1 ? 1 : 0;
                } else {
                    $data['relation_switch'] = 0;
                }

                //个人中心的推广二维码
                $data['poster'] = $this->getPoster($member_info['yz_member']['is_agent']);

                //文章营销
                $articleSetting = Setting::get('plugin.article');
                if ($articleSetting['enabled'] == 1) {
                    $data['article_title'] = $articleSetting['center'] ? html_entity_decode($articleSetting['center']) : '文章营销';
                }

                //自定义表单
                $data['myform'] = (new MemberService())->memberInfoAttrStatus();

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
                $mid = \app\common\models\Member::getMid();
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
                $cost_num = Order::getCostTotalNum(\YunShop::app()->getMemberId());

                if ($info['become_check'] && $cost_num >= $info['become_ordercount']) {
                    $apply_qualification = 5;
                }
                break;
            case 3:
                $apply_qualification = 3;
                $cost_price = Order::getCostTotalPrice(\YunShop::app()->getMemberId());

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
            'become1' => ['shop_name' => $account['name'], 'parent_name' => $parent_name, 'realname' => $member['realname'], 'mobile' => $member['mobile']],
            'become2' => ['shop_name' => $account['name'], 'total' => $info['become_ordercount'], 'cost' => $cost_num],
            'become3' => ['shop_name' => $account['name'], 'total' => $info['become_moneycount'], 'cost' => $cost_price],
            'become4' => ['shop_name' => $account['name'], 'goods_name' => $goods_name, 'goods_id' => $info['become_goods_id']],
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

        $sub_member_model->status = 1;
        $sub_member_model->apply_time = time();

        if (!$sub_member_model->save()) {
            return $this->errorJson('会员信息保存失败');
        }

        $realname = \YunShop::request()->realname;
        $moible = \YunShop::request()->mobile;

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
        return $this->successJson('', ['count' => MemberModel::getAgentCount(\YunShop::app()->getMemberId())]);
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
        $birthday = [];
        $data = \YunShop::request()->data;

        if (isset($data['birthday'])) {
            $birthday = explode('-', $data['birthday']);
        }

        $member_data = [
            'realname' => $data['realname'],
            'avatar' => $data['avatar'],
            'gender' => isset($data['gender']) ? intval($data['gender']) : 0,
            'birthyear' => isset($birthday[0]) ? intval($birthday[0]) : 0,
            'birthmonth' => isset($birthday[1]) ? intval($birthday[1]) : 0,
            'birthday' => isset($birthday[2]) ? intval($birthday[2]) : 0
        ];

        if (!empty($data['mobile'])) {
            $member_data['mobile'] = $data['mobile'];
        }

        if (!empty($data['telephone'])) {
            $member_data['telephone'] = $data['telephone'];
        }

        $member_shop_info_data = [
            'alipay' => $data['alipay'],
            'alipayname' => $data['alipay_name'],
            'province_name' => isset($data['province_name']) ? $data['province_name'] : '',
            'city_name' => isset($data['city_name']) ? $data['city_name'] : '',
            'area_name' => isset($data['area_name']) ? $data['area_name'] : '',
            'province' => isset($data['province']) ? $data['province'] : 0,
            'city' => isset($data['city']) ? $data['city'] : 0,
            'area' => isset($data['area']) ? $data['area'] : 0,
            'address' => isset($data['address']) ? $data['address'] : '',
            'wechat' => isset($data['wx']) ? $data['wx'] : '',
        ];

        if (\YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
            $member_model = MemberModel::getMemberById(\YunShop::app()->getMemberId());
            $member_model->setRawAttributes($member_data);

            $member_shop_info_model = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId());
            $member_shop_info_model->setRawAttributes($member_shop_info_data);

            $member_validator = $member_model->validator($member_model->getAttributes());
            $member_shop_info_validator = $member_shop_info_model->validator($member_shop_info_model->getAttributes());

            if ($member_validator->fails()) {
                $warnings = $member_validator->messages();
                $show_warning = $warnings->first();

                return $this->errorJson($show_warning);
            }

            if ($member_shop_info_validator->fails()) {
                $warnings = $member_shop_info_validator->messages();
                $show_warning = $warnings->first();
                return $this->errorJson($show_warning);
            }

            //自定义表单
            $member_form = (new MemberService())->updateMemberForm($data);

            if (!empty($member_form)) {
                $member_shop_info_model->member_form = json_encode($member_form);
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
        $mobile = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $confirm_password = \YunShop::request()->password;

        $member_model = MemberModel::getMemberById(\YunShop::app()->getMemberId());

        if (\YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
            $check_code = MemberService::checkCode();

            if ($check_code['status'] != 1) {
                return $this->errorJson($check_code['json']);
            }

            $msg = MemberService::validate($mobile, $password, $confirm_password);

            if ($msg['status'] != 1) {
                return $this->errorJson($msg['json']);
            }

            $salt = Str::random(8);
            $member_model->salt = $salt;
            $member_model->mobile = $mobile;
            $member_model->password = md5($password . $salt);

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

            $salt = Str::random(8);
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
     * @param int $goods_id
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

        $config = $js->config(array('onMenuShareTimeline', 'onMenuShareAppMessage', 'showOptionMenu'));
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
        $shop['icon'] = replace_yunshop(yz_tomedia($shop['logo']));

        if (!is_null(\Config('customer_service'))) {
            $class = array_get(\Config('customer_service'), 'class');
            $function = array_get(\Config('customer_service'), 'function');
            $ret = $class::$function(request()->goods_id);
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

        $data = [
            'config' => $config,
            'info' => $info,   //商城设置
            'shop' => $shop,
            'share' => $share   //分享设置
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

    public function guideFollow()
    {
        $member_id = \YunShop::app()->getMemberId();
        if (empty($member_id)) {
            return $this->errorJson('用户未登录', []);
        }

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

                $logo = replace_yunshop(tomedia($setting['logo']));
                $text = $account->name;
            }

            return $this->successJson('', [
                'logo' => $logo,
                'text' => $text,
                'url' => $set['follow_url']
            ]);
        }

        return $this->errorJson('暂无数据', []);
    }

    /**
     * 会员中心推广二维码(包含会员是否有生成海报权限)
     * @param $isAgent
     * @return string
     */
    private function getPoster($isAgent)
    {
        if (\YunShop::plugin()->get('poster')) {
            if (\Schema::hasColumn('yz_poster', 'center_show')) {
                $posterModel = Poster::uniacid()->select('id', 'is_open')->where('center_show', 1)->first();
                if (($posterModel && $posterModel->is_open) || ($posterModel && !$posterModel->is_open && $isAgent)) {
                    $file_path = (new CreatePosterService(\YunShop::app()->getMemberId(), $posterModel->id))->getMemberPosterPath();
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
        $width = 320;
        $height = 540;

        $logo_width = 40;
        $logo_height = 40;

        $font_size = 15;
        $font_size_show = 20;

        $member_id = \YunShop::app()->getMemberId();

        $shopInfo = Setting::get('shop.shop');
        $shopName = $shopInfo['name'] ?: '商城'; //todo 默认值需要更新
        $shopLogo = $shopInfo['logo'] ? replace_yunshop(tomedia($shopInfo['logo'])) : base_path() . '/static/images/logo.png'; //todo 默认值需要更新
        $shopImg = $shopInfo['signimg'] ? replace_yunshop(tomedia($shopInfo['signimg'])) : base_path() . '/static/images/photo-mr.jpg'; //todo 默认值需要更新

        $str_lenght = $logo_width + $font_size_show * mb_strlen($shopName);

        $space = ($width - $str_lenght) / 2;

        $uniacid = \YunShop::app()->uniacid;
        $path = storage_path('app/public/personalposter/' . $uniacid);
        if (!file_exists($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $md5 = md5($member_id . $shopInfo['name'] . $shopInfo['logo'] . $shopInfo['signimg']); //用于标识组成元素是否有变化
        $extend = '.png';
        $file = $md5 . $extend;

        if (!file_exists($path . '/' . $file)) {
            $targetImg = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($targetImg, 255, 255, 255);
            imagefill($targetImg, 0, 0, $white);

            $imgSource = imagecreatefromstring(\Curl::to($shopImg)->get());
            $logoSource = imagecreatefromstring(\Curl::to($shopLogo)->get());
            $qrcode = MemberModel::getAgentQR();
            $qrSource = imagecreatefromstring(\Curl::to($qrcode)->get());
            $fingerPrintImg = imagecreatefromstring(file_get_contents(base_path() . '/static/app/images/ewm.png'));
            $mergeData = [
                'dst_left' => $space,
                'dst_top' => 10,
                'dst_width' => $logo_width,
                'dst_height' => $logo_height,
            ];
            self::mergeImage($targetImg, $logoSource, $mergeData); //合并商城logo图片
            $mergeData = [
                'size' => $font_size,
                'left' => $space + $logo_width + 10,
                'top' => 37,
            ];
            self::mergeText($targetImg, $shopName, $mergeData);//合并商城名称(文字)
            $mergeData = [
                'dst_left' => 0,
                'dst_top' => 60,
                'dst_width' => 320,
                'dst_height' => 320,
            ];
            self::mergeImage($targetImg, $imgSource, $mergeData); //合并商城海报图片
            $mergeData = [
                'dst_left' => 0,
                'dst_top' => 380,
                'dst_width' => 160,
                'dst_height' => 160,
            ];
            self::mergeImage($targetImg, $fingerPrintImg, $mergeData); //合并指纹图片
            $mergeData = [
                'dst_left' => 160,
                'dst_top' => 380,
                'dst_width' => 160,
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
        imagecopyresized($destinationImg, $sourceImg, $data['dst_left'], $data['dst_top'], 0, 0, $data['dst_width'], $data['dst_height'], $w, $h);
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
        $uniacid = \YunShop::app()->uniacid;
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

    public function getCustomField()
    {
        // member.member.get-custom-field
        $member = Setting::get('shop.member');
        $data = [
            'is_custom' => $member['is_custom'],
            'custom_title' => $member['custom_title'],
            'is_validity' => $member['level_type'] == 2 ? true : false,
            'term' => $member['term'] ? $member['term'] : 0,
        ];
        return $this->successJson('获取自定义字段成功！', $data);
    }

    public function saveCustomField()
    {
        // member.member.sava-custom-field
        $member_id = \YunShop::app()->getMemberId();
        $custom_value = \YunShop::request()->get('custom_value');

        $data = [
            'custom_value' => $custom_value,
        ];
        $request = MemberShopInfo::where('member_id', $member_id)->update($data);
        if($request){
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
                    Member::setMemberRelation($member->member_id,$member->parent_id);
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

        $relation = MemberRelation::getSetInfo()->first();

        if (!is_null($relation) && 1 == $relation->status) {
            $data = [
                'switch' => 1
            ];
        }

        return $this->successJson('', $data);
    }

    public function anotherShare()
    {
        $order_ids = \YunShop::request()->order_ids;
        $mid   = \YunShop::app()->getMemberId();

        if (empty($order_ids)) {
            return $this->errorJson('参数错误', '');
        }

        if (empty($mid)) {
            return $this->errorJson('用户未登陆', '');
        }

        $title = Setting::get('shop.pay.another_share_title');

        $url   = yzAppFullUrl('/member/payanotherdetail', ['mid'=>$mid, 'order_ids'=>$order_ids]);

        if (empty($title)) {
            $title = '土豪大大，跪求代付';
        }

        $data = [
            'title' => $title,
            'url' => $url
        ];

        return $this->successJson('', $data);
    }
}