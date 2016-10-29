<?php
global $_W, $_GPC;
ca('system.commission');
$wechatid = intval($_GPC['wechatid']);
if (!$_W['isfounder']) {
        $wechatid = $_W['uniacid'];
};
if(!cv('system.commission.view')){
    $wechatid = $_W['uniacid'];
}
plog('system.commission', "分销关系修改：ID：40 原上级 ID：总店 修改上级为：19");
if (checksubmit('submit')) {
    $mid     = intval($_GPC['mid']);
    $agentid = intval($_GPC['agentid']);


    if (empty($mid)) {
        message('请选择会员!', '', 'error');
    }
    if ($mid == $agentid) {
        message('不能选择相同的会员!', '', 'error');
    }
    $member = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where id=:id and uniacid=:uniacid limit 1', array(
        ':id' => $mid,
        ':uniacid' => $wechatid
    ));
    if (empty($member)) {
        message('会员未找到!', '', 'error');
    }
    if (!empty($agentid)) {
        $agent = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where id=:id and isagent=1 and status=1 and uniacid=:uniacid limit 1', array(
            ':id' => $agentid,
            ':uniacid' => $wechatid
        ));
        if (empty($agent)) {
            message('分销商未找到!', '', 'error');
        }
    }
    pdo_update('sz_yi_member', array(
        'agentid' => $agentid,
        'fixagentid' => intval($_GPC['fixagentid'])
    ), array(
        'id' => $mid,
        'uniacid' => $wechatid
    ));
    $agentname = $member['agentid'] ? $member['agentid'] : "总店";
    plog('system.commission', "分销关系修改：ID：{$mid} 原上级 ID：".$agentname." 修改上级为：{$agentid}");
    message('设置成功!', $this->createPluginWebUrl('system/commission'), 'success');
}
$wechats = $this->model->get_wechats();
load()->func('tpl');
include $this->template('commission');
