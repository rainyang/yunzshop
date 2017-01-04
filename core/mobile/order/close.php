<?php
/*
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/sz_yi/defines.php';
require '../../../../../addons/sz_yi/core/inc/functions.php';
require '../../../../../addons/sz_yi/core/inc/plugin/plugin_model.php';
 */
global $_W, $_GPC;
//ignore_user_abort();
set_time_limit(0);
$sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
foreach ($sets as $set) {
	$_W['uniacid'] = $set['uniacid'];
	if (empty($_W['uniacid'])) {
		continue;
	}
	$trade = m('common')->getSysset('trade', $_W['uniacid']);
	$days = intval($trade['closeorder']);
	if ($days <= 0) {
		continue;
	}
	$daytimes = 86400 * $days;
	$orders = pdo_fetchall('select id from ' . tablename('sz_yi_order') . " where  uniacid={$_W['uniacid']} and status=0 and paytype<>3  and createtime + {$daytimes} <=unix_timestamp() ");
	$p = p('coupon');
	foreach ($orders as $o) {
		$onew = pdo_fetch('select status from ' . tablename('sz_yi_order') . " where id=:id and status=0 and paytype<>3  and createtime + {$daytimes} <=unix_timestamp()  limit 1", array(':id' => $o['id']));

		if (!empty($onew) && $onew['status'] == 0) {
		    //订单商品
            $order_goods = m('order')->getOrderGodds($o['id']);

            //商品返库存
            foreach ($order_goods as $items) {
                 if ($items['type'] == 3) {//虚拟产品
                     m('order')->updateVirtualGoodsRecord($items['orderid'], $items['id']);
                 } elseif (!empty($items['optionid'])) {//多规格商品
                     m('order')->updateGoodsOptionStock($items['id'], $items['optionid'], $items['total']);
                 } else {//无规格商品
                     m('order')->updateGoodsStock($items['id'], $items['total']);
                 }
            }

			pdo_query('update ' . tablename('sz_yi_order') . ' set status=-1,canceltime=' . time() . ' where id=' . $o['id']);

            //返回积分
            m('member')->returnCredit($o['id']);

			if ($p) {
			    //返回佣金
			    $p->returnCommission($o['id']);
				if (!empty($o['couponid'])) {
					$p->returnConsumeCoupon($o['id']);
				}
			}
		}
	}
}
echo "ok...";
