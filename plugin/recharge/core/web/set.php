<?php

global $_W, $_GPC;
ca('recharge.set');
$set = $this->getSet();
if (checksubmit('submit')) {
	$content = htmlspecialchars_decode($_GPC['content']);
    $rechargenotice = htmlspecialchars_decode($_GPC['rechargenotice']);
    $data          = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
    $data['texts'] = is_array($_GPC['texts']) ? $_GPC['texts'] : array();
    $data['rechargenotice'] = $rechargenotice;
    $data['content'] = $content;
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('channel.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('set');
