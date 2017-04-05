<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;
ca('statistics.view.sale_analysis');
function sale_analysis_count($sql)
{
    $c = pdo_fetchcolumn($sql);
    return intval($c);
}

$orderprice_condition = "";
if (empty($starttime) || empty($endtime)) {
    $starttime = strtotime('-1 month');
    $endtime   = time();
}
if (!empty($_GPC['datetime'])) {
    $starttime = strtotime($_GPC['datetime']['start']);
    $endtime   = strtotime($_GPC['datetime']['end']);
    if (!empty($_GPC['searchtime'])) {
        $condition .= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
        $orderprice_condition = "  AND paytime >= {$starttime} AND paytime <= {$endtime} ";
        $params[':starttime'] = $starttime;
        $params[':endtime']   = $endtime;
    }
}
$member_count    = sale_analysis_count("SELECT count(*) FROM " . tablename('sz_yi_member') . "   WHERE uniacid = '{$_W['uniacid']}' ");
$orderprice      = sale_analysis_count("SELECT sum(price) FROM " . tablename('sz_yi_order') . " WHERE status>=1 and uniacid = '{$_W['uniacid']}' " . $orderprice_condition);
$ordercount      = sale_analysis_count("SELECT count(*) FROM " . tablename('sz_yi_order') . " WHERE status>=1 and uniacid = '{$_W['uniacid']}' " . $orderprice_condition);
$viewcount       = sale_analysis_count("SELECT sum(viewcount) FROM " . tablename('sz_yi_goods') . " WHERE uniacid = '{$_W['uniacid']}' ");
$member_buycount = sale_analysis_count('SELECT count(*) from ' . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and  openid in ( SELECT distinct openid from " . tablename('sz_yi_order') . "   WHERE uniacid = '{$_W['uniacid']}' and status>=1 {$orderprice_condition})");
include $this->template('web/statistics/sale_analysis');
