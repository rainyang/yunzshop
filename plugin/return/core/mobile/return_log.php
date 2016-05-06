<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid   = $_W['uniacid'];
$trade     = m('common')->getSysset('trade');
if ($_W['isajax']) {

    if ($operation == 'display') {
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 10;

        $list      = pdo_fetchall("select * from " . tablename('sz_yi_return') . " where uniacid = '" .$_W['uniacid'] . "' and mid = '".$member['id']."' order by create_time desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total     = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_return') . " where  uniacid = '" .$_W['uniacid'] . "' and mid = '".$member['id']."'");
        foreach ($list as &$row) {
            $row['createtime'] = date('Y-m-d H:i', $row['create_time']);
        }
        unset($row);
        show_json(1, array(
            'total' => $total,
            'list' => $list,
            'pagesize' => $psize
        ));
    }
}
include $this->template('return_log');
