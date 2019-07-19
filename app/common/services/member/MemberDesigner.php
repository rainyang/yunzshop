<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/16
 * Time: 14:41
 */

namespace app\common\services\member;

use app\common\facades\Setting;
use app\common\services\popularize\PortType;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;
use Yunshop\AlipayOnekeyLogin\services\SynchronousUserInfo;
use Yunshop\Designer\models\ViewSet;
use Yunshop\Kingtimes\common\models\Distributor;
use Yunshop\Kingtimes\common\models\Provider;


class MemberDesigner
{
   public function getMemberData()
   {
       $filter = [
           'conference',
           //'store-cashier',
           'recharge-code'
       ];

       $diyarr = [
           'tool'         => ['separate','elive'],
           'asset_equity' => ['integral', 'credit', 'asset', 'love', 'coin','froze','extension'],
           'merchant'     => ['supplier', 'kingtimes', 'hotel', 'store-cashier', 'cashier', 'micro', 'delivery_station', 'service_station'],
           'market'       => ['ranking', 'article', 'clock_in', 'conference', 'video_demand', 'enter_goods', 'universal_card', 'recharge_code', 'my-friend', 'business_card', 'net_car', 'material-center'
               , 'help-center', 'sign', 'courier', 'declaration', 'distribution-order']
       ];

       $data = [];

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
               $name = 'recharge_code';
               $class = 'icon-member-recharge1';
               $url = 'rechargeCode';
           } elseif ($info['name'] == 'conference') {
               $name = 'conference';
               $class = 'icon-member-act-signup1';
               $url = 'conferenceList';
           }

           $data[] = [
               'name'  => $name,
               'title' => $info['title'],
               'class' => $class,
               'url'   => $url
           ];
       });
       if (app('plugins')->isEnabled('asset') && (new \Yunshop\Asset\Common\Services\IncomeDigitizationService)->memberPermission()) {
           $data[] = [
               'name'  => 'asset',
               'title' => PLUGIN_ASSET_NAME,
               'class' => 'icon-number_assets',
               'url'   => 'TransHome'
           ];
       }

       if (PortType::popularizeShow(\YunShop::request()->type)) {
           $data[] = [
               'name' => 'extension',
               'title' => '推广中心',
               'class' => 'icon-member-extension1',
               'url' => 'extension'
           ];
       }

       if (app('plugins')->isEnabled('business-card')) {
           $is_open = Setting::get('business-card.is_open');
           if ($is_open == 1) {
               $data[] = [
                   'name'  => 'business_card',
                   'title' => '名片',
                   'class' => 'icon-member_card1',
                   'url'   => 'CardCenter'
               ];
           }
       }

       if (app('plugins')->isEnabled('declaration')) {
           if(Setting::get('plugin.declaration.switch')){
               $data[] = [
                   'name'  => 'declaration',
                   'title' => DECLARATION_NAME,
                   'class' => 'icon-declaration_system',
                   'url'   => 'DeclarationApply'
               ];
           }
       }


       //配送站
       if (app('plugins')->isEnabled('delivery-station')) {
           $delivery_station_setting = Setting::get('plugin.delivery_station');
           $delivery_station = \Yunshop\DeliveryStation\models\DeliveryStation::memberId(\YunShop::app()->getMemberId())->first();
           if ($delivery_station && $delivery_station_setting['is_open']) {
               $data[] = [
                   'name'  => 'delivery_station',
                   'title' => '配送站',
                   'class' => 'icon-delivery_order',
                   'url'   => 'deliveryStation',
               ];
           }
       }
       //服务站
       if (app('plugins')->isEnabled('service-station')) {
           $service_station = \Yunshop\ServiceStation\models\ServiceStation::isBlack()->memberId(\YunShop::app()->getMemberId())->first();
           if ($service_station) {
               $data[] = [
                   'name' => 'service_station',
                   'title' => '服务站',
                   'class' => 'icon-service_station',
                   'url' => 'serviceStation',
               ];
           }
       }

       if (app('plugins')->isEnabled('material-center')) {
           $data[] = [
               'name'  => 'material-center',
               'title' => '素材中心',
               'class' => 'icon-member_material',
               'url'   => 'materialCenter'
           ];
       }


       if (app('plugins')->isEnabled('distribution-order')) {
           $disorder_setting = Setting::get('plugins.distribution-order');
           if ($disorder_setting && 1 == $disorder_setting['is_open']) {
               $data[] = [
                   'name'  => 'distribution-order',
                   'title' => $disorder_setting['title'] ? : '分销订单统计',
                   'class' => 'icon-order_system',
                   'url'   => 'DistributionOrders'
               ];
           }
       }

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

       if (app('plugins')->isEnabled('micro')) {
           $micro_set = \Setting::get('plugin.micro');
           if ($micro_set['is_open_miceo'] == 1) {
               $micro_shop = \Yunshop\Micro\common\models\MicroShop::getMicroShopByMemberId(\YunShop::app()->getMemberId());
               if ($micro_shop) {
                   $data[] = [
                       'name'  => 'micro',
                       'title' => '微店中心',
                       'class' => 'icon-member-mendian1',
                       'url'   => 'microShop_home',
                       'image' => 'member_a(40).png'
                   ];
               } else {
                   $data[] = [
                       'name'  => 'micro',
                       'title' => '我要开店',
                       'class' => 'icon-member-mendian1',
                       'url'   => 'microShop_apply',
                       'image' => 'member_a(40).png'
                   ];
               }
           }
       }

       if (app('plugins')->isEnabled('help-center')) {
           $status = \Setting::get('help-center.status') ? true : false;
           if ($status) {
               $data[] = [
                   'name'  => 'help-center',
                   'title' => '帮助中心',
                   'class' => 'icon-member-help',
                   'url'   => 'helpcenter'
               ];
           }
       }

       if (app('plugins')->isEnabled('love')) {
           $data[] = [
               'name' => 'love',
               'title' => \Yunshop\Love\Common\Services\SetService::getLoveName() ?: '爱心值',
               'class' => 'icon-member-exchange1',
               'url' => 'love_index'
           ];
       }

       if (app('plugins')->isEnabled('froze')) {
           $data[] = [
               'name' => 'froze',
               'title' => \Yunshop\Froze\Common\Services\SetService::getFrozeName() ?: '冻结币',
               'class' => 'icon-member-frozen1',
               'url' => 'FrozenCoin'
           ];
       }

       if (app('plugins')->isEnabled('coin')) {
           $data[] = [
               'name' => 'coin',
               'title' => \Yunshop\Coin\Common\Services\SetService::getCoinName() ?: '华侨币',
               'class' => 'icon-member-currency1',
               'url' => 'overseas_index',
           ];
       }

       if (app('plugins')->isEnabled('elive')) {
           $data[] = [
               'name' => 'elive',
               'title' => '生活缴费',
               'class' => 'icon-member-tool-j',
               'url' => 'lifeService',
               'image'=>'member_a(49).png'
           ];
       }

       if (app('plugins')->isEnabled('sign')) {
           $data[] = [
               'name' => 'sign',
               'title' => trans('Yunshop\Sign::sign.plugin_name') ?: '签到',
               'class' => 'icon-member-clock1',
               'url' => 'sign'
           ];
       }

       if (app('plugins')->isEnabled('courier')) {
           //快递单
           $status = \Setting::get('courier.courier.radio');
           if ($status) {
               $data[] = [
                   'name' => 'courier',
                   'title' => '快递'
               ];
           }
       }

       if (app('plugins')->isEnabled('my-friend')) {
           $data[] = [
               'name'  => 'my-friend',
               'title' => MY_FRIEND_NAME,
               'class' => 'icon-member_my-friend',
               'url'   => 'MyFriendApply'
           ];
       }

       if (app('plugins')->isEnabled('article')) {
           $article_setting = Setting::get('plugin.article');

           if ($article_setting) {
               $data[] = [
                   'name'  => 'article',
                   'title' => $article_setting['center'] ?: '文章中心',
                   'class' => 'icon-member-collect1',
                   'url'   => 'notice',
                   'param' => 0,
               ];
           }
       }

       if (app('plugins')->isEnabled('clock-in')) {
           $clockInService = new \Yunshop\ClockIn\services\ClockInService();
           $pluginName = $clockInService->get('plugin_name');

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

       if (app('plugins')->isEnabled('store-cashier')) {
           $store = \Yunshop\StoreCashier\common\models\Store::getStoreByUid(\YunShop::app()->getMemberId())->first();

           if (!$store) {
               $data[] = [
                   'name'  => 'store-cashier',
                   'title' => '门店申请',
                   'class' => 'icon-member-store-apply1',
                   'url'   => 'storeApply',
                   'image' => 'member_a(26).png'
               ];
           }

           if ($store && $store->is_black != 1) {
               $data[] = [
                   'name'  => 'store-cashier',
                   'title' => '门店管理',
                   'class' => 'icon-member_store',
                   'url'   => 'storeManage',
                   'image' => 'member_a(26).png'
               ];

               if ($store->hasOneCashier->hasOneCashierGoods->is_open == 1) {
                   $data[] = [
                       'name' => 'cashier',
                       'title' => '收银台',
                       'class' => 'icon-member-cashier',
                       'url' => 'cashier',
                       'api' => 'plugin.store-cashier.frontend.cashier.center.index',
                       'image' => 'member_a(43).png'
                   ];
               }
           }


       }
       if (app('plugins')->isEnabled('supplier')) {
           $supplier_setting = Setting::get('plugin.supplier');
           $supplier = \Yunshop\Supplier\common\models\Supplier::getSupplierByMemberId(\YunShop::app()->getMemberId(), 1);

           if (!$supplier) {
               $data[] = [
                   'name'  => 'supplier',
                   'title' => '供应商申请',
                   'class' => 'icon-member-apply1',
                   'url'   => 'supplier',
                   'api'   => 'plugin.supplier.supplier.controllers.apply.supplier-apply.apply',
                   'image' => 'member_a(53).png'
               ];
           } elseif ($supplier_setting && 1 == $supplier_setting['status']) {
               $data[] = [
                   'name'  => 'supplier',
                   'title' => $supplier_setting['name'] ?: '供应商管理',
                   'class' => 'icon-member-supplier',
                   'url'   => 'SupplierCenter',
                   'image' => 'member_a(53).png'
               ];
           }
       }
       if (app('plugins')->isEnabled('kingtimes')) {
           $provider = Provider::select(['id', 'uid', 'status'])->where('uid',
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
                       'image' => 'member_a(67).png'
                   ];
               }
           } else {
               $data[] = [
                   'name'  => 'kingtimes',
                   'title' => '补货商申请',
                   'class' => 'icon-member-replenishment',
                   'url'   => 'ReplenishmentApply',
                   'image' => 'member_a(67).png'
               ];
           }
           if ($distributor) {
               if ($distributor->status == 1) {
                   $data[] = [
                       'name'  => 'kingtimes',
                       'title' => '配送站中心',
                       'class' => 'icon-member-express-list',
                       'url'   => 'DeliveryTerminalApply',
                       'image' => 'member_a(54).png'
                   ];
               }
           } else {
               $data[] = [
                   'name'  => 'kingtimes',
                   'title' => '配送站申请',
                   'class' => 'icon-member-express-list',
                   'url'   => 'DeliveryTerminalApply',
                   'image' => 'member_a(54).png'
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
                   'title' => HOTEL_NAME . '管理',
                   'class' => 'icon-member_hotel',
                   'url'   => 'HotelManage'
               ];
           } else {
               $data[] = [
                   'name'  => 'hotel',
                   'title' => HOTEL_NAME . '申请',
                   'class' => 'icon-member-hotel-apply',
                   'url'   => 'hotelApply'
               ];
           }
           //酒店自定义字段
           $set = \Setting::get('plugin.hotel');
           $arr['hotel'] = [
               'hotel_home_page' => $set['hotel_home_page'] ?: '酒店主页',
               'check_the_room' => $set['check_the_room'] ?: '查看房型',
               'hotel_intro' => $set['hotel_intro'] ?: '酒店简介',
               'goods_details' => $set['goods_details'] ?: '商品详情',
               'goods_presentation' => $set['goods_presentation'] ?: '商品介绍',
               'goods_parameters' => $set['goods_parameters'] ?: '商品参数',
               'user_evaluation' => $set['user_evaluation'] ?: '用户评价',
               'hotels' => $set['hotels'] ?: '酒店',
               'hotel_first_page' => $set['hotel_first_page'] ?: '酒店首页',
               'hotel_find' => $set['hotel_find'] ?: '查找酒店',
               'hotel_find_name' => $set['hotel_find_name'] ?: '酒店名'
           ];
       }

       //网约车插件开启关闭
       if (app('plugins')->isEnabled('net-car')) {

           $video_demand_setting = Setting::get('plugin.net_car');

           if ($video_demand_setting && $video_demand_setting['net_car_open']) {
               $data[] = [
                   'name'  => 'net_car',
                   'title' => '网约车',
                   'class' => 'icon-member_my-card',
                   'url'   => 'online_car',
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

       if (app('plugins')->isEnabled('designer')) {
           //获取所有模板
           $sets = ViewSet::uniacid()->select('names', 'type')->get()->toArray();

           if (!$sets) {
               $arr['ViewSet'] = [];
           } else {
               foreach ($sets as $k => $v) {
                   $arr['ViewSet'][$v['type']]['name'] = $v['names'];
                   $arr['ViewSet'][$v['type']]['name'] = $v['names'];
               }
           }
       }

       $arr['is_open'] = [
           'yop' => app('plugins')->isEnabled('yop-pay') ? 1 : 0,
           'is_open_hotel' => app('plugins')->isEnabled('hotel') ? 1 : 0,
           'is_open_net_car' => app('plugins')->isEnabled('net-car') ? 1 : 0,
           'is_open_lease_toy' => \app\common\services\plugin\leasetoy\LeaseToySet::whetherEnabled(), //租赁订单列表是否开启
           'is_open_converge_pay' => app('plugins')->isEnabled('converge_pay') ? 1 : 0,
           'is_store' => $store && $store->is_black != 1 ? 1 : 0,
       ];

       return $arr;
   }
}