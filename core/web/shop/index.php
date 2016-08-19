<?php
global $_W, $_GPC;
$qrcode = m('qrcode')->createShopQrcode();
$url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=shop';

//创建日历表，为了连续日期
$sql = "DROP TABLE IF EXISTS " . tablename("sz_yi_temp_date") . ";
CREATE TABLE IF NOT EXISTS " . tablename("sz_yi_temp_date") . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
pdo_fetchall($sql);

$time = time() + 60*60*24;
for ($i = 0; $i < 7; $i++) {
    $time = $time - 60*60*24;
    $data = array('createdate' => date('Y-m-d', $time));
    pdo_insert('sz_yi_temp_date', $data);
}
//供应商登录查看供应商数据
$condtion = '';
if (p('supplier')) {
    $is_supplier = p('supplier')->verifyUserIsSupplier($_W['uid']);
    if (!empty($is_supplier)) {
        $condtion = ' AND supplier_uid=' . $_W['uid'];
        $condtions = ' AND o.supplier_uid=' . $_W['uid'];
        $goods_condtions = ' AND g.supplier_uid=' . $_W['uid'];
    }
}

//7日订单
$paras = array(':uniacid' => $_W['uniacid']);
$sql = "SELECT IFNULL(total, 0) as total, date_format(d.createdate, '%Y-%m-%d') as createdate FROM " . tablename("sz_yi_temp_date") . " d left join (SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total,uniacid FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') {$condtion} GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')) as o on date_format(d.createdate, '%Y-%m-%d') = date_format(o.createtime, '%Y-%m-%d') and uniacid=:uniacid order by date_format(d.createdate, '%Y-%m-%d')";

$alllist = pdo_fetchall($sql, $paras);

//已完成订单
$sql = "SELECT IFNULL(total, 0) as total, date_format(d.createdate, '%Y-%m-%d') as createdate FROM " . tablename("sz_yi_temp_date") . " d left join (SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total,uniacid FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') {$condtion} AND STATUS = 3 GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')) as o on date_format(d.createdate, '%Y-%m-%d') = date_format(o.createtime, '%Y-%m-%d') and uniacid=:uniacid order by date_format(d.createdate, '%Y-%m-%d')";
$finishlist = pdo_fetchall($sql, $paras);

//已发货订单
$sql = "SELECT IFNULL(total, 0) as total, date_format(d.createdate, '%Y-%m-%d') as createdate FROM " . tablename("sz_yi_temp_date") . " d left join (SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total,uniacid FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') {$condtion} AND STATUS = 2 GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')) as o on date_format(d.createdate, '%Y-%m-%d') = date_format(o.createtime, '%Y-%m-%d') and uniacid=:uniacid order by date_format(d.createdate, '%Y-%m-%d')";
$sendlist = pdo_fetchall($sql, $paras);

//已付款订单
$sql = "SELECT IFNULL(total, 0) as total, date_format(d.createdate, '%Y-%m-%d') as createdate FROM " . tablename("sz_yi_temp_date") . " d left join (SELECT FROM_UNIXTIME(createtime, '%Y-%m-%d') createtime,COUNT(*) as total,uniacid FROM " . tablename("sz_yi_order") . " WHERE DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= FROM_UNIXTIME(createtime, '%Y-%m-%d') {$condtion} AND STATUS = 1 GROUP BY FROM_UNIXTIME(createtime, '%Y-%m-%d')) as o on date_format(d.createdate, '%Y-%m-%d') = date_format(o.createtime, '%Y-%m-%d') and uniacid=:uniacid order by date_format(d.createdate, '%Y-%m-%d')";
$paylist = pdo_fetchall($sql, $paras);

//销售排行
$sql = "SELECT g.id,g.title,g.thumb,(select ifnull(sum(og.price),0) from  " . tablename("sz_yi_order_goods") . " og left join " . tablename("sz_yi_order") . " o on og.orderid=o.id  where o.status>=1 {$condtions} and og.goodsid=g.id  and og.uniacid=:uniacid )  as money,(select ifnull(sum(og.total),0) from  " . tablename("sz_yi_order_goods") . " og left join " . tablename("sz_yi_order") . " o on og.orderid=o.id  where o.status>=1 {$condtions} and og.goodsid=g.id  and og.uniacid=:uniacid ) as count  from " . tablename("sz_yi_goods") . " g  where 1  and g.uniacid=:uniacid {$goods_condtions}  order by money desc LIMIT 3";
$goods_list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));

$day_price = pdo_fetchcolumn("SELECT ifnull(sum(price),0) as day_price FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid {$condtion} and status>=1 and FROM_UNIXTIME(createtime, '%Y-%m-%d') = curdate()", array(
                ':uniacid' => $_W['uniacid']
            ));

$day_cnt = pdo_fetchcolumn("SELECT ifnull(count(1),0) as day_price FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid {$condtion} and FROM_UNIXTIME(createtime, '%Y-%m-%d') = curdate()", array(
                ':uniacid' => $_W['uniacid']
            ));

$day_nopay_price = pdo_fetchcolumn("SELECT ifnull(sum(price),0) as day_price FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid {$condtion} and status=0 and FROM_UNIXTIME(createtime, '%Y-%m-%d') = curdate()", array(
                ':uniacid' => $_W['uniacid']
            ));

$day_no_dispatch = pdo_fetchcolumn("SELECT ifnull(sum(price),0) as day_price FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid {$condtion} and status=1 and FROM_UNIXTIME(createtime, '%Y-%m-%d') = curdate()", array(
                ':uniacid' => $_W['uniacid']
            ));
load()->func('tpl');
include $this->template('web/shop/index');
