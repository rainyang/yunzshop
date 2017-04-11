<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/9
 * Time: 上午11:40
 */

namespace app\frontend\modules\member\controllers;

use app\api\model\Good;
use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\events\member\BecomeAgent;
use app\common\models\AccountWechats;
use app\common\models\Area;
use app\common\models\Goods;
use app\common\models\MemberShopInfo;
use app\common\services\AliPay;
use app\common\services\PayFactory;
use app\common\services\WechatPay;
use app\frontend\modules\member\models\Member;
use app\frontend\modules\member\models\MemberModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use app\common\models\Order;

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

   public function getUserInfo()
   {
       $member_id = \YunShop::app()->getMemberId();

       if (!empty($member_id)) {
           $member_info = MemberModel::getUserInfos($member_id)->first();

           if (!empty($member_info)) {
               $member_info = $member_info->toArray();

               if (!empty($member_info['yz_member'])) {
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

               $member_info['Provinces'] = Area::getProvincesList();
               return $this->successJson('', $member_info);
           } else {
               return $this->errorJson('用户不存在');
           }

       } else {
           return $this->errorJson('缺少访问参数');
       }
   }
}
