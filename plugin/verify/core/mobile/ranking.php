<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid   = $_W['uniacid'];





$default_avatar = "../addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg";
if ($_W['isajax']) {
    if ($operation == 'display') {

            $pindex    = max(1, intval($_GPC['page']));
            $psize     = 10;

            $list      = pdo_fetchall("SELECT s.*, SUM(o.price) AS totalprice FROM " .tablename('sz_yi_store'). " s LEFT JOIN " .tablename('sz_yi_order). " o ON s.id = o.storeid WHERE S.uniacid = {$_W['uniacid']} ORDER BY totalprice DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize);
            $total     = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_store') . " where status =1 and uniacid = '.$_W['uniacid'];
            foreach ($list as $k => &$row) {
                 $row['number'] = ($k+1) + ($pindex - 1) * $psize;
            }
            unset($row);

            show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize
            ));



    }
}
include $this->template('ranking');
