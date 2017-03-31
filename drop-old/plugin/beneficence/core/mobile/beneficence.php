<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$uniacid   = $_W['uniacid'];

$set = $this->getSet();
$beneficencename = $set['beneficencename']?$set['beneficencename']:'行善池';
$total_money     = pdo_fetchcolumn('select sum(money) as total_money from ' . tablename('sz_yi_beneficence') . " where  uniacid = '" .$_W['uniacid'] . "'");
if ($_W['isajax']) {
    if ($operation == 'display') {
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 10;

        $list      = pdo_fetchall("select * from " . tablename('sz_yi_beneficence') . " where uniacid = '" .$_W['uniacid'] . "' order by create_time desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total     = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_beneficence') . " where  uniacid = '" .$_W['uniacid'] . "'");

        foreach ($list as $k => &$row) {
            $row['create_time']     = date("Y-m-d H:i:s",$row['create_time']);
        }
        unset($row);

        return show_json(1, array(
            'total' => $total,
            'list' => $list,
            'pagesize' => $psize
        ));
    }
}
include $this->template('beneficence');
