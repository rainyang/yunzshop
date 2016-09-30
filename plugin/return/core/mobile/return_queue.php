<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$trade = m('common')->getSysset('trade');
$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 0;
$_GPC['pageid'] = $_GPC['pageid'] ? $_GPC['pageid'] : '';
if ($_W['isajax']) {
    if ($operation == 'display') {
        if ($_GPC['type'] == 0) {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            $pageid = "";
            if (!empty($_GPC['pageid'])) {
                $pageid = " AND id < '".intval($_GPC['pageid'])."'" ;
            }
            $list = pdo_fetchall("select * from " . tablename('sz_yi_return') . " where uniacid = '" . $_W['uniacid'] . "' and mid = '" . $member['id'] . "' and `delete` = '0' {$pageid} order by create_time desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_return') . " where  uniacid = '" . $_W['uniacid'] . "' and mid = '" . $member['id'] . "' and `delete` = '0'");
            foreach ($list as &$row) {
                $row['createtime'] = date('Y-m-d H:i', $row['create_time']);
            }
            unset($row);
            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize,
                'type' => $_GPC['type']
            ));
        } elseif ($_GPC['type'] == 1) {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            $pageid = "";
            if (!empty($_GPC['pageid'])) {
                $pageid = " AND ogq.goodsid > '".intval($_GPC['pageid'])."'" ;
            }

            $list = pdo_fetchall("select ogq.*, g.title from " . tablename('sz_yi_order_goods_queue') . " ogq left join " . tablename('sz_yi_goods') . " g on(ogq.goodsid = g.id) where ogq.uniacid = '" . $_W['uniacid'] . "' and ogq.openid = '" . $openid . "' {$pageid} group by ogq.goodsid order by ogq.goodsid asc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order_goods_queue') . " where  uniacid = '" . $_W['uniacid'] . "' and openid = '" . $openid . "' group by goodsid");
            foreach ($list as &$row) {
                $row['createtime'] = date('Y-m-d H:i', $row['create_time']);

                $row['total'] = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order_goods_queue') . " where  uniacid = '" . $_W['uniacid'] . "' and openid = '" . $openid . "' and goodsid = " . $row['goodsid']);
            }
            unset($row);
            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize,
                'type' => $_GPC['type']
            ));
        } elseif ($_GPC['type'] == 2) {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $pageid = "";
            if (!empty($_GPC['pageid'])) {
                $pageid = " AND ogq.id > '".intval($_GPC['pageid'])."'" ;
            }
            $list = pdo_fetchall("select ogq.*, g.title from " . tablename('sz_yi_order_goods_queue') . " ogq left join " . tablename('sz_yi_goods') . " g on(ogq.goodsid = g.id) where ogq.uniacid = '" . $_W['uniacid'] . "' and ogq.openid = '" . $openid . "'  and ogq.goodsid = '" . $_GPC['goodsid'] . "' {$pageid}  LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order_goods_queue') . " where  uniacid = '" . $_W['uniacid'] . "' and openid = '" . $openid . "' and goodsid = '" . $_GPC['goodsid'] . "' ");
            foreach ($list as &$row) {
                $row['createtime'] = date('Y-m-d H:i', $row['create_time']);
            }
            unset($row);
            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize,
                'type' => $_GPC['type']
            ));

        } elseif ($_GPC['type'] == 3) {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 2;

            $pageid = "";
            if (!empty($_GPC['pageid'])) {
                $pageid = " AND id > '".intval($_GPC['pageid'])."'" ;
            }
            $list = pdo_fetchall("select * from " . tablename('sz_yi_return_log') . "  where uniacid = '" . $_W['uniacid'] . "' and openid = '" . $openid . "' and returntype = 1  {$pageid} LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_return_log') . " where  uniacid = '" . $_W['uniacid'] . "' and openid = '" . $openid . "' and returntype = 1 ");

            foreach ($list as &$row) {
                $row['createtime'] = date('Y-m-d H:i', $row['create_time']);
            }
            unset($row);
            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize,
                'type' => $_GPC['type']
            ));

        }

    }
}
include $this->template('return_queue');
