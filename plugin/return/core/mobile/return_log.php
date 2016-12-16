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
$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 1;

if ($_W['isajax']) {
    if ($operation == 'display') {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $total = pdo_fetchcolumn("select count(rl.id) from" . tablename('sz_yi_return_log') . " rl
            left join " . tablename('sz_yi_member') . " m on( rl.mid=m.id ) 
            where rl.uniacid = :uniacid and rl.returntype = :returntype and m.id = :mid ", array(
            ':uniacid' => $_W['uniacid'],
            ':returntype' => $_GPC['type'],
            ':mid' => $member['id']
        ));
        $list = pdo_fetchall("select rl.*, m.id as mid, m.realname , m.mobile  from" . tablename('sz_yi_return_log') . " rl
            left join " . tablename('sz_yi_member') . " m on( rl.mid=m.id ) 
            where rl.uniacid = :uniacid and rl.returntype = :returntype and m.id = :mid order by create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize,
            array(
                ':uniacid' => $_W['uniacid'],
                ':returntype' => $_GPC['type'],
                ':mid' => $member['id']
            ));
        foreach ($list as &$row) {
            $row['create_time'] = date("Y-m-d H:i:s", $row['create_time']);
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
include $this->template('return_log');
