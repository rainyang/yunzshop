<?php
global $_W, $_GPC;
ca('bonus.set');
$set = $this->getSet();
$trade     = m('common')->getSysset('trade');
if (checksubmit('submit')) {
    $data          = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
    $data['texts'] = is_array($_GPC['texts']) ? $_GPC['texts'] : array();
    if($data['paymethod'] == 1 && $data['sendmethod'] == 1){
    	message('打款微信钱包不允许使用自动方式，请改为手动！', '', 'error');
    }
    if(is_array($_GPC['leveltype'])){
    	$data['leveltype'] = $_GPC['leveltype'];
    }
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('bonus.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('set');
