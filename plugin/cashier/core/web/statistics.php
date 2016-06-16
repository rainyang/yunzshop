<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    ca('cashier.statistics.view');
    $page      = max(1, intval($_GPC['page']));
    $pagesize  = 20;
    $condition = ' uniacid = :uniacid';
    $params    = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND name LIKE :name OR contact LIKE :contact OR mobile LIKE :mobile OR address LIKE :address';
        $_GPC['keyword']    = trim($_GPC['keyword']);
        $params[':name']    = '%' . trim($_GPC['keyword']) . '%';
        $params[':contact'] = '%' . trim($_GPC['keyword']) . '%';
        $params[':mobile']  = '%' . trim($_GPC['keyword']) . '%';
        $params[':address'] = '%' . trim($_GPC['keyword']) . '%';
    }
    if (!empty($_GPC['time'])) {
        if ($_GPC['searchtime'] == '1') {
            $condition .= ' AND create_time >= :start AND create_time <= :end';
            $params[':start'] = $_GPC['time']['start'];
            $params[':end']   = $_GPC['time']['end'];
        }
    }
    $sql   = 'SELECT * FROM ' . tablename('sz_yi_cashier_store') . " where 1 and {$condition} ORDER BY id DESC LIMIT " . ($page - 1) * $pagesize . ',' . $pagesize;
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_cashier_store') . " where 1 and {$condition}", $params);
    $pager = pagination($total, $page, $pagesize);

    $tidyList = array();
    foreach ($list as &$row) {
        $cashier_order = pdo_fetchall('SELECT order_id FROM ' . tablename('sz_yi_cashier_order') . ' WHERE uniacid = :uniacid AND cashier_store_id = :cashier_store_id', array(':uniacid' => $_W['uniacid'], ':cashier_store_id' => $row['id']));
        $orderids = array();
        foreach ($cashier_order as $order) {
            $orderids[] = $order['order_id']; 
        }
        $row['totalprices'] = 0;
        if ($orderids) {
            // 累计支付金额
            $totalprices = pdo_fetch('SELECT SUM(price) AS tprice FROM ' . tablename('sz_yi_order') . ' WHERE uniacid = ' .$_W['uniacid'].' and status = 3 AND id IN (' . implode(',', $orderids) . ')');
            $row['totalprices'] = $totalprices['tprice'];

            $totalprices = $row['totalprices']*(100-$row['settle_platform'])/100;

            $realtotalprices = pdo_fetch('SELECT SUM(realprice) AS tprice FROM ' . tablename('sz_yi_order') . ' WHERE uniacid = ' .$_W['uniacid'].' and status = 3 AND id IN (' . implode(',', $orderids) . ')');
            $row['realtotalprices'] = $realtotalprices['tprice'];

            $realtotalprices = $row['realtotalprices'];
        }

        $row['total_commission'] = 0;
        $row['total_credits']    = 0;
        foreach ($orderids as $orderid) {
            $commissions=pdo_fetch('select * from '.tablename('sz_yi_order_goods').' where orderid='.$orderid . ' and uniacid='.$_W['uniacid']);

            $row['commission1'] = iunserializer($commissions['commission1']);
            $row['commission2'] = iunserializer($commissions['commission2']);
            $row['commission3'] = iunserializer($commissions['commission3']);
            $row['commission1_total'] += $row['commission1']['default'];
            $row['commission2_total'] += $row['commission2']['default'];
            $row['commission3_total'] += $row['commission3']['default'];
            $credits = $this->model->setCredits($orderid, true);
            if ($credits) {
                $row['total_credits'] += $credits;
            }
        }
        // 已经提现成功或正申请提现的金额
        $row['total_withdraw']    = 0;
        $row['total_no_withdraw'] = $realtotalprices;
        $totalwithdraw = pdo_fetch('SELECT SUM(money) as total_money FROM ' . tablename('sz_yi_cashier_withdraw') . ' WHERE uniacid = ' . $_W['uniacid'] . ' AND cashier_store_id = ' . $row['id'] . ' AND status = 1');
        if ($totalwithdraw) {
            $row['total_withdraw'] = $totalwithdraw['total_money'];
            if (!empty($row['total_withdraw'])) {
                $row['total_no_withdraw'] = number_format($realtotalprices - $row['total_withdraw'], 2);
            }
        }
        $tidyList[] = $row;
    }
}

include $this->template('statistics');
