<?php
/**
  * 云币插件基础设置
  * 2016-8-15
  * rayyang
  */
global $_W, $_GPC;
ca('yunbi.set');
$set = $this->getSet();
$set['yunbi_title'] = !empty($set['yunbi_title'])?$set['yunbi_title']:"云币";

if (checksubmit('submit')) {
    $data          = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('yunbi.save.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('set');