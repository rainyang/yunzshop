<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
load()->model('user');
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$condition = " and uniacid=:uniacid";
$params    = array(
    ':uniacid' => $_W['uniacid']
);
if ($operation == 'reviewed') {
    $status = $_GPC['status'];
    $id = $_GPC['id'];
    $openid = pdo_fetchcolumn("SELECT openid FROM " . tablename('sz_yi_af_channel') . " WHERE uniacid={$_W['uniacid']} AND id={$id}");
    if (empty($openid)) {
        message('没有该条申请记录', $this->createPluginWebUrl('channel/apply'), 'error');
    } else {
        if ($status == 1) {
            $msg = '驳回申请成功';
            message($msg, $this->createPluginWebUrl('channel/apply'), 'success');
        } else {
            $channellevel = pdo_fetch('SELECT id FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid = :uniacid ORDER BY level_num ASC', array(':uniacid' => $_W['uniacid']));//渠道商等级
            if (!empty($channellevel)) {
                $msg = '审核通过成功'; 
                pdo_update('sz_yi_af_channel',array('status' => $status), array('id' => $id, 'uniacid' => $_W['uniacid']));               
                pdo_update('sz_yi_member',array('ischannel' => 1 , 'channel_level' => $channellevel['id'], 'channeltime' => time()), array('openid' => $openid, 'uniacid' => $_W['uniacid']));
                $msg_data = array(
                    'msg'   => $msg,
                    'time'  => time()
                    );
                $this->model->sendMessage($openid, $msg, TM_CHANNEL_BECOME);
                message($msg, $this->createPluginWebUrl('channel/apply'), 'success');
            } else {
                $msg = '请先填写渠道商等级！';
                message($msg, $this->createPluginWebUrl('channel/apply'), 'error');
            }
        }       
    }
}
if (!empty($_GPC['mid'])) {
    $condition .= ' AND mid=:mid';
    $params[':mid'] = intval($_GPC['mid']);
}
if (!empty($_GPC['realname'])) {
    $_GPC['realname'] = trim($_GPC['realname']);
    $condition .= ' AND realname LIKE :realname';
    $params[':realname'] = "%{$_GPC['realname']}%";
}
$sql = "SELECT * FROM " . tablename('sz_yi_af_channel') . " WHERE status=0 {$condition}";
$list = pdo_fetchall($sql, $params);
$total           = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_af_channel') . " where status=0 {$condition}",$params);
$pager           = pagination($total, $pindex, $psize);
load()->func('tpl');
include $this->template('apply');