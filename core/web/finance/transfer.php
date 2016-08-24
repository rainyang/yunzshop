<?php
/*=============================================================================
#     FileName: transfer.php
#         Desc: 日志
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 852388660@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-7-28 11:50:13
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$op      = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$groups  = m('member')->getGroups();
$levels  = m('member')->getLevels();
$uniacid = $_W['uniacid'];
if ($op == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize  = 20;
    $sql = "select * from " . tablename('sz_yi_member_transfer_log') . " where uniacid = ". $_W['uniacid'] ." ORDER BY createtime DESC ";
    $sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql);
    foreach ($list as $key => &$row) {
        $tosell_member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid = ". $_W['uniacid'] ." and id = ".$row['tosell_id']);
        $row['tosell_realname'] = $tosell_member['realname']?$tosell_member['realname']:$tosell_member['nickname'];
        $assigns_member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid = ". $_W['uniacid'] ." and id = ".$row['assigns_id']);
        $row['assigns_realname'] = $assigns_member['realname']?$assigns_member['realname']:$assigns_member['nickname'];  
        $row['createtime'] = date("Y-m-d H:i:s",$row['createtime']);
    }
   unset($row);
    $total = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_member_transfer_log') . " where uniacid = ". $_W['uniacid']);
    $pager = pagination($total, $pindex, $psize);
 }
load()->func('tpl');
include $this->template('web/finance/transfer');