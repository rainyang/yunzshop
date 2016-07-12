<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$member = m('member')->getMember($openid);
if ($_W['isajax']) {

    if ($operation == 'display') {
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 10;


        if($_GPC['type'] == 1)
        {
            $condition = " and tosell_id=:tosell_id and uniacid=:uniacid ";
            $params    = array(
                ':uniacid' => $uniacid,
                ':tosell_id' => $member['id']
            );
        }else
        {
            $condition = " and assigns_id=:assigns_id and uniacid=:uniacid ";
            $params    = array(
                ':uniacid' => $uniacid,
                ':assigns_id' => $member['id']
            );
        }

            $list      = pdo_fetchall("select * from " . tablename('sz_yi_member_transfer_log') . " where 1 {$condition} order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total     = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member_transfer_log') . " where 1 {$condition}", $params);
  
        foreach ($list as &$row) {
            $row['createtime'] = date('Y-m-d H:i', $row['createtime']);

            if($_GPC['type'] == 1)
            {
                $condition1 = " and id = '".$row['assigns_id']."'";
            }else
            {
                $condition1 = " and id = '".$row['tosell_id']."'";
            }
            $trader = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid = '".$uniacid."'".$condition1);
            $row['name'] = $trader['nickname'];
            $row['type'] = $_GPC['type'];
        }
        unset($row);
        show_json(1, array(
            'total' => $total,
            'list' => $list,
            'pagesize' => $psize
        ));
    }
}

include $this->template('member/transfer_log');
