<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$condition = " and uniacid=:uniacid";
$params    = array(
    ':uniacid' => $_W['uniacid']
);
if (!empty($_GPC['mid'])) {
    $condition .= ' and id=:mid';
    $params[':mid'] = intval($_GPC['mid']);
}
if (!empty($_GPC['realname'])) {
    $_GPC['realname'] = trim($_GPC['realname']);
    $condition .= ' and realname like :realname';
    $params[':realname'] = "%{$_GPC['realname']}%";
}
$sql = "select * from " . tablename('sz_yi_af_supplier') . " where 1 {$condition}";
if (empty($_GPC['export'])) {
    $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
}
$list = pdo_fetchall($sql, $params);
if ($_GPC['export1'] == '1') {
    plog('member.member.export', '导出会员数据');
    m('excel')->export($list, array(
        "title" => "会员数据-" . date('Y-m-d-H-i', time()),
        "columns" => array(
            array(
                'title' => '会员ID',
                'field' => 'id',
                'width' => 12
            ),
            array(
                'title' => '会员姓名',
                'field' => 'realname',
                'width' => 12
            ),
            array(
                'title' => '手机号码',
                'field' => 'mobile',
                'width' => 12
            ),
            array(
                'title' => '产品名称',
                'field' => 'weixin',
                'width' => 12
            ),
            array(
                'title' => '产品名称',
                'field' => 'productname',
                'width' => 12
            )
        )
    ));
}
$total           = count($list);
$pager           = pagination($total, $pindex, $psize);
load()->func('tpl');
include $this->template('supplier_for');