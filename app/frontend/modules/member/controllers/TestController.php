<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/9
 * Time: 上午11:40
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\common\events\member\BecomeAgent;
use app\common\models\AccountWechats;
use app\common\models\MemberShopInfo;
use app\common\models\Order;
use app\common\services\AliPay;
use app\common\services\PayFactory;
use app\common\services\WechatPay;
use app\frontend\modules\member\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\services\MemberService;
use EasyWeChat\Foundation\Application;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestController extends BaseController //ApiController
{
   public function index()
   {

       $pay = new WechatPay();
//       $str  = $pay->setUniacidNo(122, 5);
//       echo $str . '<BR>';
//       echo substr($str, 17, 5);
//       $result = $pay->doWithdraw(146,  1);
     //  $result = $pay->doRefund('1491193485',  0.1, 0.1);
//       echo '<pre>';print_r($result);exit;
//
      $data = $pay->doPay(['order_no'=>time(),'amount'=>0.1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>['type'=>1]]);


       return view('order.pay', [
           'config' => $data['config'],
           'js' => $data['js']
       ])->render();
exit;
       $pay = new AliPay();

      //\\ $p = $pay->doRefund('2017032421001004920213140182', '1', '0.1');

       $p = $pay->doPay(['order_no'=>time(),'amount'=>0.01, 'subject'=>'支付宝支付', 'body'=>'测试:2', 'extra'=>['type'=>2]]);
       //$p = $pay->doWithdraw(4,time(),'0.1','提现');
       redirect($p)->send();
   }

   public function loginApi()
   {
       $login_api = 'http://test.yunzshop.com/addons/sz_yi/api.php?i=2&route=member.login.index&type=1';

       redirect($login_api)->send();
   }

   public function pt()
   {
       echo '<pre>';print_r($_SESSION);exit;
   }

   public function login()
   {
       $url = 'http://dev.yzshop.com/addons/sz_yi/api.php?i=2&route=member.login.index';
       \Curl::to($url)
           ->withData(['type=>5', 'memberdata[mobile]'=>'15216771448', 'memberdata[password]' => '123456'])
           ->asJsonResponse(true)
           ->post();
   }

   public function pay()
   {
       $pay = PayFactory::create($type);

       //微信预下单
       $data = $pay->doPay(['order_no'=>time(),'amount'=>1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>'']);
       //预下单返回结果
       return view('order.pay', [
           'config' => $data['config'],
           'js' => $data['js']
       ])->render();

       //支付宝支付
       $url = $pay->doPay(['order_no'=>time(),'amount'=>1, 'subject'=>'微信支付', 'body'=>'测试:2', 'extra'=>'']);



       //订单号、退款单号、退款总金额、实际退款金额
       $result = $pay->doRefund('1490503054', '4001322001201703264702511714', 1, 1);

       //提现者用户ID、提现单号、提现金额
       $pay->doWithdraw(123, time(), 0.1);

   }

   public function getId()
   {

       $g = AccountWechats::getAccountInfoById(2);
       echo '<pre>';print_r($g->toArray());exit;
   }

    /**
     * 二维码
     */
   public function getQR()
   {
       echo QrCode::format('png')->size(100)->generate('http:www.baidu.com', storage_path('qr/' . time().'.png'));
   }

    /**
     * 事件
     */
   public function runEvent()
   {
       $model = MemberShopInfo::getMemberShopInfo(146);

       event(new BecomeAgent(\YunShop::request()->mid, $model));
   }

    /**
     * 我的推荐人
     */
   public function getReferrerInfo()
   {
       $member_info = MemberModel::getMyReferrerInfo(\YunShop::app()->getMemberId())->first();

       if (!empty($member_info)) {
           $member_info = $member_info->toArray();

           $referrer_info = MemberModel::getUserInfos($member_info['yz_member']['parent_id'])->first();

       }
   }

   public function getMyAgent()
   {

       $result = MemberShopInfo::getAgentAllCount([5,9]);

     //  dd($result->toArray());
       $agent_ids = [];

       $agent_info = MemberModel::getMyAgentInfo(\YunShop::app()->getMemberId());
       $agent_model = $agent_info->get();

       if (!empty($agent_model)) {
           $agent_data = $agent_model->toArray();

           foreach ($agent_data as $key => $item) {
               $agent_ids[$key] = $item['uid'];
               $agent_data[$key]['agent_count'] = 0;
           }
       } else {
           return $this->errorJson('数据为空');
       }

       $all_count = MemberShopInfo::getAgentAllCount($agent_ids);

       foreach ($all_count as $k => $rows) {
           foreach ($agent_data as $key => $item) {
               if ($rows['parent_id'] == $item['uid']) {
                   $agent_data[$key]['agent_count'] = $rows['total'];

                   break 1;
               }
           }
       }
       dd($agent_data);

   }

    /**
     * 获取会员信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
   public function getUserInfo()
   {
       $member_id = \YunShop::app()->getMemberId();

       if (!empty($member_id)) {
           $member_info = MemberModel::getUserInfos($member_id)->first();

           if (!empty($member_info)) {
               $member_info = $member_info->toArray();

               if (!empty($member_info['yz_member'])) {
                   $member_info['telephone'] = $member_info['yz_member']['telephone'];
                   $member_info['alipay_name'] = $member_info['yz_member']['alipay_name'];
                   $member_info['alipay'] = $member_info['yz_member']['alipay'];
                   $member_info['province_name'] = $member_info['yz_member']['province_name'];
                   $member_info['city_name'] = $member_info['yz_member']['city_name'];
                   $member_info['area_name'] = $member_info['yz_member']['area_name'];
                   $member_info['province'] = $member_info['yz_member']['province'];
                   $member_info['city'] = $member_info['yz_member']['city'];
                   $member_info['area'] = $member_info['yz_member']['area'];
                   $member_info['address'] = $member_info['yz_member']['address'];
                   $member_info['birthday'] = $member_info['yz_member']['birthday'];

                   if (!empty($member_info['yz_member']['group'])) {
                       $member_info['group_id'] = $member_info['yz_member']['group']['id'];
                       $member_info['group_name'] = $member_info['yz_member']['group']['group_name'];
                   }

                   if (!empty($member_info['yz_member']['level'])) {
                       $member_info['level_id'] = $member_info['yz_member']['level']['id'];
                       $member_info['level_name'] = $member_info['yz_member']['level']['level_name'];
                   }

               }

               $order_info = Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE]);

               $member_info['order'] = $order_info;

               //$member_info['Provinces'] = Area::getProvincesList();
               return $this->successJson('', $member_info);
           } else {
               return $this->errorJson('用户不存在');
           }

       } else {
           return $this->errorJson('缺少访问参数');
       }
   }

    /**
     * 修改用户资料
     *
     * @return \Illuminate\Http\JsonResponse
     */
   public function updateUserInfo()
   {
       //$data = \YunShop::request()->data;

       $data = [
           'uid' => 146
       ];
       $meber_data = [
           'uid' => '146',
           'realname' => '贝贝',
           'mobile' => '15046101888',
           'telephone' => '15046102222',
           'avatar' => 'a.jpg',
           'gender' => '2',
           'birthyear' => '1990',
           'birthmonth' => '12',
           'birthday' =>'12'
       ];
       $member_shop_info_data = [
           'alipay' => '423@163.com',
           'alipayname' => 'baobao',
           'province_name' => '北京',
           'city_name' => '北京市',
           'area_name' => '朝阳区',
           'province' => '110000',
           'city' => '110100',
           'area' => '110105',
           'address' => '你猜',
       ];

       if (\YunShop::app()->getMemberId()) {
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

   public function bindMobile()
   {
       $data = [
           'uid' => 146,
           'mobile' => '15046198888',
           'password' => 'abcdef',
           'confirm_password' => 'abcdef',
       ];
       $member_model = MemberModel::getMemberById(\YunShop::app()->getMemberId());

       if (MemberService::validate($data['mobile'], $data['password'], $data['confirm_password'])) {
           $salt = \Illuminate\Support\Str::random(8);
           $member_model->salt = $salt;
           $member_model->mobile = $data['mobile'];
           $member_model->password = md5($data['password'] . $salt);

           if ($member_model->save()) {
               return $this->successJson('手机号码绑定成功');
           } else {
               return $this->errorJson('手机号码绑定失败');
           }
       } else {
           return $this->errorJson('手机号或密码错误');
       }
   }

   public function getRelation()
   {
       $model = MemberModel::getMyAgentsParentInfo(10);

       $a = $model->first()->toArray();
       echo '<pre>';print_r($model->first()->toArray());
       echo count($a['yz_member'], 1);
       exit;
   }

    public function wxJsSdkConfig()
    {

        $a = weAccount();
        echo '<pre>';print_r($a);exit;
        $pay = \Setting::get('shop.pay');
        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret']
        ];

        $app = new Application($options);

        $js = $app->js;
        $js->setUrl('http://www.yunzshop.com');

        $config = $js->config(array('onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo'), 1);
        $config = json_decode($config, 1);
echo '<pre>';print_r($config);exit;
        return $this->successJson('', ['config' => $config]);
    }
}
