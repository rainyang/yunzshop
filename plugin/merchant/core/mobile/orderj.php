<?php

global $_W, $_GPC;
$openid = m('user')->getOpenid();
$set = $this->getSet();
$member = m('member')->getMember($openid);
$suppliers = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where member_id={$member['id']} and uniacid={$_W['uniacid']}");
$suppliercount = count($suppliers);
$uids = '';
foreach ($suppliers as $key => $value) {
    if ($key == 0) {
        $uids .= $value['supplier_uid'];
    } else {
        $uids .= ','.$value['supplier_uid'];
    }
}
if (empty($uids)) {
    $uids = 0;
}
$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 0;
//订单数量
$ordercount0 = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . " where supplier_uid in ({$uids}) and userdeleted=0 and deleted=0 and uniacid={$_W['uniacid']} ");
//已提现佣金总和
$commission_total=number_format(pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_merchant_apply') . " where uniacid={$_W['uniacid']} and member_id={$member['id']} and status=1"), 2);
$apply_total = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_merchant_apply') . " where uniacid={$_W['uniacid']} and member_id={$member['id']}");
//可提现佣金
$commission_ok = 0;
foreach ($suppliers as $key => $value) {
    $commissions = pdo_fetchcolumn("select commissions from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and supplier_uid={$value['supplier_uid']} and member_id={$member['id']}");
    $order_total_price = pdo_fetchcolumn("select sum(goodsprice) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status = 3 and userdeleted = 0 and deleted = 0 and supplier_uid = {$value['supplier_uid']} and merchant_apply_status = 0 ");
    $commission_ok += $commissions * $order_total_price/100;

}
$commission_ok=number_format($commission_ok, 2);
//预计佣金
//$commission_totaly=number_format($member['commission_totaly'], 2);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if($_W['isajax']) {
    if ($operation == 'order') {
        $status = trim($_GPC['status']);
        if ($status != '') {
            $conditionq = '  and o.status=' . intval($status);
        } else {
            $conditionq = '  and o.status>=0';  
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $sql = "select o.id,o.ordersn,o.price,o.openid,o.status,o.address,o.createtime from " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 " . " where 1 {$conditionq} and o.uniacid=".$_W['uniacid']." and o.supplier_uid in ({$uids}) ORDER BY o.createtime DESC,o.status DESC  ";
        $sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $listsd = pdo_fetchall($sql);
        foreach ($listsd as &$rowp) {
            $sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid FROM ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . ' where og.orderid=:orderid order by og.id asc';
            $rowp['goods'] = set_medias(pdo_fetchall($sql, array(':orderid' => $rowp['id'])), 'thumb');
            $rowp['goodscount'] = count($rowp['goods']);
            $address = unserialize($rowp['address']);
            $rowp['address'] = $address['address'];
            $rowp['province'] = $address['province'];
            $rowp['city'] = $address['city'];
            $rowp['area'] = $address['area'];
            $rowp['createtime'] = date('Y-m-d H:i', $rowp['createtime']);
            $rowp['isstatus'] = $rowp['status'];
            if ($rowp['status'] == 0) {
            $rowp['status'] = '待付款';
            } else {
                if ($rowp['status'] == 1) {
                    $rowp['status'] = '已付款';
                } else {
                    if ($rowp['status'] == 2) {
                        $rowp['status'] = '待收货';
                    } else {
                        if ($rowp['status'] == 3) {
                            $rowp['status'] = '已完成';
                        }
                    }
                }
            }
        }
    show_json(2, array('list' => $listsd,'pagesize' => $psize,'setlevel'=>$setids));
    
    
    }
}
include $this->template('orderj');
