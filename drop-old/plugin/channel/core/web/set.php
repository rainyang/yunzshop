<?php
global $_W, $_GPC;
ca('channel.set');
$set = $this->getSet();
$leveltype = $set['leveltype'];
$level = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid = :uniacid ORDER BY level_num DESC",array(':uniacid' => $_W['uniacid']));
if (!empty($set['become_condition_goodsid'])) {
        $goods = pdo_fetch('SELECT id,title FROM ' .tablename('sz_yi_goods') . ' WHERE id = :id', array(':id' => $set['become_condition_goodsid']));
    }
if (checksubmit('submit')) {
    $data          = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
    $data['texts'] = is_array($_GPC['texts']) ? $_GPC['texts'] : array();
    if(is_array($_GPC['become_other'])){
        $data['become_other'] = $_GPC['become_other'];
    }
    if ($_GPC['setdata']['setapplycycle'] < 0  || $_GPC['setdata']['setapplyminmoney'] < 0 || !is_numeric($_GPC['setdata']['setapplycycle']) || !is_numeric($_GPC['setdata']['setapplyminmoney'])) {
    	message('渠道商提现设置错误!', referer(), 'error');
    }
    if ($_GPC['setdata']['setprofitproportion'] < 0 || !is_numeric($_GPC['setdata']['setprofitproportion'])) {
    	message('渠道商推荐员设置错误!', referer(), 'error');
    }
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('channel.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('set');
