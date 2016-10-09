<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$yunbitype   = empty($_GPC['yunbitype']) ? '1' : $_GPC['yunbitype'];
$set = $this->getSet();
$set['yunbi_title'] = !empty($set['yunbi_title'])?$set['yunbi_title']:"云币";
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

    $total = pdo_fetchall("select yl.id from" . tablename('sz_yi_yunbi_log') . " yl
        left join " . tablename('sz_yi_member') . " m on( yl.openid=m.openid and m.uniacid = '" .$_W['uniacid'] . "' ) where yl.uniacid = '" .$_W['uniacid'] . "' and yl.returntype = '".$yunbitype."' and yl.status >= 0 AND yl.money <> 0 ".$condition);
    $total = count($total);
    $list_group = pdo_fetchall("select yl.*, m.id as mid, m.realname , m.mobile  from" . tablename('sz_yi_yunbi_log') . " yl
        left join " . tablename('sz_yi_member') . " m on( yl.openid=m.openid and m.uniacid = '" .$_W['uniacid'] . "') where yl.uniacid = '" .$_W['uniacid'] . "' and yl.returntype = '".$yunbitype."' and yl.status >= 0 AND yl.money <> 0 ".$condition." order by create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize);
    foreach ($list_group as &$row) {
        $row['create_time']     = date("Y-m-d H:i:s",$row['create_time']);
        if ($row['returntype'] == '11') {
            if ($row['status'] == '0') {
                $row['status_text'] = '交易中';
            }elseif ($row['status'] == '2') {
                $row['status_text'] = '已完成';
            }elseif ($row['status'] == '3') {
                $row['status_text'] = '公司回购';
            }
        }else{
            if ($row['status'] == '0') {
                $row['status_text'] = '等待返现';
            }else{
                $row['status_text'] = '已完成';
            }

        }

    }
    unset($row);
    $pager = pagination($total, $pindex, $psize);
}

include $this->template('yunbi_log');
