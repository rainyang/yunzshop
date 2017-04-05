<?php
/**
  * 一元夺宝插件基础设置
  */
global $_W, $_GPC;
ca('indiana.set');
$set = $this->getSet();

if (checksubmit('submit')) {
    $data          =  array_merge($set, $_GPC['setdata']);
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('indiana.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}

load()->func('tpl');
include $this->template('set');