<?php

if (!defined('IN_IA')) {
    die('Access Denied');
}
global $_W, $_GPC;
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
if ($_W['isajax']) {
    $goodsid    = intval($_GPC['goodsid']);
    $pindex     = max(1, intval($_GPC['page']));
    $type       = trim($_GPC['status']);
    $psize      = 20;
    $condition  = " AND `openid`='{$openid}' AND uniacid={$_W['uniacid']} AND goodsid={$goodsid} AND type={$type}";
    $list       = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_stock_log') . " WHERE 1 {$condition} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
    $total      = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_channel_stock_log') . " WHERE 1 {$condition} order by id desc ");
    if (empty($total)) {
        $total = 0;
    }
    foreach ($list as &$rowp) {
        $rowp['paytime'] = date('Y-m-d H:i:s', $rowp['paytime']);
    }
    unset($rowp);
    return show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
}
include $this->template('detail');