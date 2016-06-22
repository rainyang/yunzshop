<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = ' and a.uniacid=:uniacid and a.status=1';
    $params = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['applysn'])) {
        $_GPC['applysn'] = trim($_GPC['applysn']);
        $condition .= ' and a.applysn like :applysn';
        $params[':applysn'] = "%{$_GPC['applysn']}%";
    }
    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= ' and (m.realname like :realname or m.nickname like :realname or m.mobile like :realname)';
        $params[':realname'] = "%{$_GPC['realname']}%";
    }
    $sql = 'select a.*, m.nickname,m.avatar,m.realname,m.mobile from ' . tablename('sz_yi_merchant_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.id = a.member_id' . " where 1 {$condition} ORDER BY a.id desc ";
    if (empty($_GPC['export'])) {
        $sql .= '  limit ' . ($pindex - 1) * $psize . ',' . $psize;
    }
    $list = pdo_fetchall($sql, $params);
    foreach ($list as &$value) {
        $value['finish_time'] = date('Y-m-d H:i:s',$value['apply_time']);
        $value['type'] = '微信';
    }
    if ($_GPC['export'] == '1') {
        m('excel')->export($list, array('title' => '已审核佣金' . '数据-' . date('Y-m-d-H-i', time()), 'columns' => array(array('title' => 'ID', 'field' => 'id', 'width' => 12), array('title' => '提现单号', 'field' => 'applysn', 'width' => 24), array('title' => '粉丝', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号码', 'field' => 'mobile', 'width' => 12), array('title' => '提现方式', 'field' => 'type', 'width' => 12),array('title' => '申请佣金', 'field' => 'money', 'width' => 12), array('title' => '申请时间', 'field' => 'apply_time', 'width' => 24))));
    }
    $total = count($list);
    $pager = pagination($total, $pindex, $psize);
}
load()->func('tpl');
include $this->template('merchant_apply_finish');
