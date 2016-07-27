<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$returntype   = empty($_GPC['returntype']) ? '1' : $_GPC['returntype'];
if ($operation == 'display') {

    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;

    $condition ='';
    if (!empty($_GPC['mid'])) {
        $condition .= " and m.id='".$_GPC['mid']."'";
    }

    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= " and ( m.realname like '{$_GPC['realname']}' or m.nickname like '{$_GPC['realname']}' or m.mobile like '{$_GPC['realname']}') ";
    }
    $total = pdo_fetchall("select rl.id from" . tablename('sz_yi_return_log') . " rl
        left join " . tablename('sz_yi_member') . " m on( rl.mid=m.id ) where rl.uniacid = '" .$_W['uniacid'] . "' and rl.returntype = '".$returntype."'".$condition);
    $total = count($total);
    $list_group=pdo_fetchall("select rl.*, m.id as mid, m.realname , m.mobile  from" . tablename('sz_yi_return_log') . " rl
        left join " . tablename('sz_yi_member') . " m on( rl.mid=m.id ) where rl.uniacid = '" .$_W['uniacid'] . "' and rl.returntype = '".$returntype."'".$condition." order by create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize);
    foreach ($list_group as &$row) {
        $row['create_time']     = date("Y-m-d H:i:s",$row['create_time']);
    }
    unset($row);
    $pager = pagination($total, $pindex, $psize);
}

include $this->template('return_log');
