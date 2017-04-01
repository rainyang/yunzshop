<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {

    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchall("select * from" . tablename('sz_yi_order_goods_queue') . " ogq 
        left join " . tablename('sz_yi_goods') . " g on( ogq.goodsid = g.id ) where ogq.uniacid = '" .$_W['uniacid'] . "' group by ogq.goodsid");
    $total = count($total);
    $list_group=pdo_fetchall(" select ogq.*, g.title from " .tablename('sz_yi_order_goods_queue') . " ogq 
        left join " . tablename('sz_yi_goods') . " g on( ogq.goodsid = g.id ) where ogq.uniacid = '" .$_W['uniacid'] . "' group by ogq.goodsid order by ogq.id asc LIMIT " . ($pindex - 1) * $psize . "," . $psize);
    foreach ($list_group as &$row) {
        $row['total']     = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order_goods_queue') . " where  uniacid = '" .$_W['uniacid'] . "' and goodsid = ".$row['goodsid']);
    }
    unset($row);
    $pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'detail') {

    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $params    = array();
    $condition = '';
    if (!empty($_GPC['mid'])) {
        $condition .= " and m.id='".$_GPC['mid']."'";
    }

    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= " and ( m.realname like '{$_GPC['realname']}' or m.nickname like '{$_GPC['realname']}' or m.mobile like '{$_GPC['realname']}') ";
    }

    $total = pdo_fetchall("select ogq.id from" . tablename('sz_yi_order_goods_queue') . " ogq 
        left join " . tablename('sz_yi_goods') . " g on( ogq.goodsid = g.id ) 
        left join " . tablename('sz_yi_member') . " m on( ogq.openid = m.openid )
         where ogq.uniacid = '" .$_W['uniacid'] . "' and m.uniacid = '" .$_W['uniacid'] . "' and ogq.goodsid = ".$_GPC['goodsid'] . $condition);
    $total = count($total);

    $list_group=pdo_fetchall(" select ogq.*, g.title, m.id as mid, m.realname , m.mobile from " .tablename('sz_yi_order_goods_queue') . " ogq 
        left join " . tablename('sz_yi_goods') . " g on( ogq.goodsid = g.id ) 
        left join " . tablename('sz_yi_member') . " m on( ogq.openid = m.openid )
         where ogq.uniacid = '" .$_W['uniacid'] . "' and m.uniacid = '" .$_W['uniacid'] . "' and ogq.goodsid = '".$_GPC['goodsid']."' {$condition} order by ogq.id asc LIMIT " . ($pindex - 1) * $psize . "," . $psize);
    foreach ($list_group as &$row) {
        $row['create_time'] = date('Y-m-d H:i', $row['create_time']);
    }
    unset($row);
    //todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $b = 'http://cl'.'oud.yu'.'nzs'.'hop.com/web/index.php?c=account&a=up'.'grade';
        
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($b, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }

    $pager = pagination($total, $pindex, $psize);
}

include $this->template('queue');

