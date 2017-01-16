<?php
/*=============================================================================
#     FileName: list.php
#         Desc:  
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:39:02
#      History:
=============================================================================*/
global $_W, $_GPC;
$cond = '';
if (p('supplier')) {
    $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
    if($perm_role != 0){
        $cond = " and identity in ('exhelper','taobao','coupon') ";
    }
}
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$category = m('plugin')->getCategory();
foreach ($category as $ck => &$cv) {
	$cv['plugins'] = pdo_fetchall('select * from ' . tablename('sz_yi_plugin') . " where category=:category $cond order by displayorder asc", array(':category' => $ck));
}
unset($cv);
//公众号权限设置查询
$acid_plugins = pdo_fetchcolumn('select plugins from ' . tablename('sz_yi_perm_plugin') . ' where acid=:uniacid',array(":uniacid" => $_W['uniacid']));

if(!empty($acid_plugins)){
	$plugins_data = explode(',', $acid_plugins);
}
// 新增加的icon样式
$plugins_icon = array(
	"supplier" => "supplier", 
	"commission" => "sitemap",
	"system" => "cog",
	"creditshop" => "database",
	"article" => "article",
	"yunpay" => "yunpay",
	"exhelper" => "street-view",
	"verify" => "verify",
	"qiniu" => "qiniu",
	"taobao" => "taobao",
	"tmessage" => "tmessage",
	"coupon" => "tags",
	"diyform" => "diyform",
	"perm" => "perm",
	"poster" => "poster",
	"postera" => "postera",
	"designer" => "w-designer",
	"app" => "app",
	"sale" => "w-sale",
	"channel" => "channel",
	"return" => "web-price",
	"virtual" => "virtual",
	"ranking" => "ranking",
	"fans" => "tool",
	"hotel" => "hotel",
	"bonus" => "fh",
	"customer" => "kehu",
	"cashier" => "web-price",
	"merchant" => "admin",
	"love" => "fensi",
	"choose" => "choose",
	"helper" => "help",
	"yunbi" => "coin",
	"area" => "region",
	"beneficence" => "donation",
    "yunprint" => "print",
    "discuz" => "discuz",
	"fund"	=> "fund",
	"indiana" => "indiana",
	"card" => "gift_card",
    "credits" => "credits",
    "wxapp" => "app",
	"recharge" => "phone"
	);
$plugins_desc = array(
	"supplier" => "厂家入驻，平台统一销售", 
	"commission" => "客户下单后上线获得返现奖励",
	"system" => "分销商关系调整、数据管理",
	"creditshop" => "积分兑换礼品或抽奖",
	"article" => "一键转发，隐形锁粉，赚奖励",
	"yunpay" => "微信支付，支付宝，银联，信用卡",
	"exhelper" => "快速打印快递单、发货单，一键发货",
	"verify" => "线上下单门店提货，配送核销",
	"qiniu" => "高效的附件存储方案",
	"taobao" => "一键批量导入淘宝商品",
	"tmessage" => "微信无限制模板消息群发",
	"coupon" => "设置多种使用范围的优惠券",
	"diyform" => "高效灵活收集信息",
	"perm" => "让员工各尽其职",
	"poster" => "海报锁粉，获得奖励",
	"postera" => "限时不限量，高效锁粉",
	"designer" => "DIY店铺首页、专题、导航菜单",
	"app" => "苹果+安卓双版本，无限消息推送",
	"sale" => "积分、余额抵扣，满额优惠，充值满减",
	"return" => "排队全返、订单全返、订单满额返、会员等级返现",
	"virtual" => "下单自动发送虚拟卡密",
	"ranking" => "消费金额、佣金、积分排行",
	"fans" => "解决粉丝头像、昵称获取异常",
	"hotel" => "房态、房价管理，酒店、会议、餐饮预订",
	"bonus" => "代理级差分红、全球分红、区域分红",
	"customer" => "kehu",
	"cashier" => "能分销、分红、全返，奖励红包的收银台",
	"merchant" => "招募供应商获得销售分红",
	"channel" => "虚拟库存，人、货、钱一体化管理",
    "yunprint" => "云打印",
    "fund"	=> "项目在指定时间众筹金额",
	"indiana" => "投入一元就有机会获得一件商品",
	"card"	=> "代金卡",
    "credits"	=> "积分兑换",
    "wxapp"	=> "微信小程序",
	"recharge"	=> "手机业务充值中心",
);

if(!pdo_fieldexists('sz_yi_plugin', 'desc')) {
	pdo_fetchall("ALTER TABLE ".tablename('sz_yi_plugin')." ADD `desc` varchar(800) NULL");
}
$sql = "select * from ".tablename('sz_yi_plugin');
$plugin_list = pdo_fetchall($sql);
foreach ($plugin_list as $pl) {
	if ($pl['identity'] == "cashier" && $pl['category'] == 0) {
		$data = array('category' => 'biz');
		pdo_update('sz_yi_plugin', $data, array(
			'identity' => $pl['identity']
		));
	}
	if ($pl['desc'] == "") {
		$data = array('desc' => $plugins_desc[$pl['identity']]);
		pdo_update('sz_yi_plugin', $data, array(
			'identity' => $pl['identity']
		));
	}
}
include $this->template('web/plugins/list');
exit;
