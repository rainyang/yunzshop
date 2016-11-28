<?php
global $_W, $_GPC;
$qrcode = m('qrcode')->createShopQrcode();
$url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=shop';

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
$sqls=array();
for($i=0;$i<7;$i++){
    $_i=$i-1;
    $sql="
select DATE_SUB(CURDATE(), INTERVAL $i DAY) as createdate,COUNT(id) as total,". $_W["uniacid"] . " as uniacid  from ".tablename("sz_yi_order")."
where 
createtime between UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL $i DAY)) and UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL ".$_i." DAY))
and uniacid=:uniacid {$condtion}
	";
    $sqls[]=$sql;
}
$sql=join("union ",$sqls)." order by createdate asc";
$alllist = pdo_fetchall($sql, $paras);

//已完成订单
$sqls=array();
for($i=0;$i<7;$i++){
    $_i=$i-1;
    $sql="
select DATE_SUB(CURDATE(), INTERVAL $i DAY) as createdate,COUNT(id) as total,". $_W["uniacid"] . " as uniacid  from ".tablename("sz_yi_order")."
where 
createtime between UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL $i DAY)) and UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL ".$_i." DAY))
and uniacid=:uniacid and status=3  {$condtion}
	";
    $sqls[]=$sql;
}
$sql=join("union ",$sqls)." order by createdate asc";
$finishlist = pdo_fetchall($sql, $paras);
//已发货订单
$sqls=array();
for($i=0;$i<7;$i++){
    $_i=$i-1;
    $sql="
select DATE_SUB(CURDATE(), INTERVAL $i DAY) as createdate,COUNT(id) as total,". $_W["uniacid"] . " as uniacid  from ".tablename("sz_yi_order")."
where 
createtime between UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL $i DAY)) and UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL ".$_i." DAY))
and uniacid=:uniacid and status=2  {$condtion}
	";
    $sqls[]=$sql;
}
$sql=join("union ",$sqls)." order by createdate asc";
$sendlist = pdo_fetchall($sql, $paras);

//已付款订单
$sqls=array();
for($i=0;$i<7;$i++){
    $_i=$i-1;
    $sql="
select DATE_SUB(CURDATE(), INTERVAL $i DAY) as createdate,COUNT(id) as total,". $_W["uniacid"] . " as uniacid  from ".tablename("sz_yi_order")."
where 
createtime between UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL $i DAY)) and UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL ".$_i." DAY))
and uniacid=:uniacid and status=1  {$condtion}
	";
    $sqls[]=$sql;
}
$sql=join("union ",$sqls)." order by createdate asc";
$paylist = pdo_fetchall($sql, $paras);

//销售排行

$sql="
select g.id,g.title,g.thumb,og.goodsid,sum(og.price) as money , sum(og.total) as count 
from " . tablename("sz_yi_order") . " as o,". tablename("sz_yi_order_goods") ." as og ," . tablename("sz_yi_goods") . " as g
where o.status>=1 and o.uniacid=:uniacid and og.orderid=o.id and og.goodsid=g.id {$condtions} {$goods_condtions}
group by goodsid
order by money desc limit 0,3
";

$goods_list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));


$day_price = pdo_fetchcolumn("SELECT ifnull(sum(price),0) as day_price FROM `ims_sz_yi_order` WHERE uniacid=:uniacid  and status>=1 and
createtime between  UNIX_TIMESTAMP(curdate()) and UNIX_TIMESTAMP(curdate())+ 24*3600", array(
    ':uniacid' => $_W['uniacid']
));

$day_cnt = pdo_fetchcolumn("SELECT ifnull(count(id),0) as day_price FROM `ims_sz_yi_order` WHERE uniacid=:uniacid  and status>=1 and
createtime between  UNIX_TIMESTAMP(curdate()) and UNIX_TIMESTAMP(curdate())+ 24*3600", array(
    ':uniacid' => $_W['uniacid']
));

$day_nopay_price = pdo_fetchcolumn("SELECT ifnull(sum(price),0) as day_price FROM `ims_sz_yi_order` as o WHERE uniacid=:uniacid  and status=0  {$condtions} and
createtime between  UNIX_TIMESTAMP(curdate()) and UNIX_TIMESTAMP(curdate())+ 24*3600", array(
    ':uniacid' => $_W['uniacid']
));


$day_no_dispatch = pdo_fetchcolumn("SELECT ifnull(sum(price),0) as day_price FROM `ims_sz_yi_order` as o WHERE uniacid=:uniacid  and status=1  {$condtions} and
createtime between  UNIX_TIMESTAMP(curdate()) and UNIX_TIMESTAMP(curdate())+ 24*3600", array(
    ':uniacid' => $_W['uniacid']
));
load()->func('tpl');
include $this->template('web/shop/index');
