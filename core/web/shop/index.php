<?php
global $_W, $_GPC;

$qrcode = m('qrcode')->createShopQrcode();
$url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=shop';

//7日订单
$paras = array();
$sql = "SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 70 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')";
$alllist = pdo_fetchall($sql, $paras);

//已完成订单
$sql = "SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 70 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') AND STATUS = 3 GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')";
$alllist = pdo_fetchall($sql, $paras);

//已发货订单
$sql = "SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 70 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') AND STATUS = 2 GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')";
$alllist = pdo_fetchall($sql, $paras);

//已付款订单
$sql = "SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 70 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') AND STATUS = 1 GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')";
$alllist = pdo_fetchall($sql, $paras);

load()->func('tpl');
include $this->template('web/shop/index');
