<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$trade     = m('common')->getSysset('trade');
$shopset   = m('common')->getSysset('shop');
if ($_W['isajax']) {
    if ($operation == 'display') {
        $pindex    = max(1, intval($_GPC['page']));
        $paymethod    = intval($_GPC['paymethod']);
        $psize     = 10;
        $condition = " and log.openid=:openid and log.uniacid=:uniacid and ag.paymethod=".$paymethod;
        $params    = array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        );
        $list      = pdo_fetchall("select log.createtime,log.money,ag.paymethod,ag.num,ag.qnum,ag.phase from " . tablename('sz_yi_member_log') . " log left join " . tablename('sz_yi_member_aging_rechange') . " ag on ag.id=log.aging_id where 1 {$condition} order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
        $total     = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_member_log') . " log left join " . tablename('sz_yi_member_aging_rechange') . " ag on ag.id=log.aging_id where 1 {$condition}", $params);
        foreach ($list as &$row) {
            $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
            $paymethod_by = $row['paymethod'] == 0 ? " 元" : " 积分";
            $paymethod = $row['paymethod'] == 0 ? "金额 " : " ";
            $row['money'] = "充值".$paymethod.$row['num'].$paymethod_by."（分".$row['qnum']."期）,已充第".$row['phase']."期".$row['money'].$paymethod_by;
        }
        unset($row);
        show_json(1, array(
            'total' => $total,
            'list' => $list,
            'pagesize' => $psize
        ));
    }
}
include $this->template('member/rechange_log');
