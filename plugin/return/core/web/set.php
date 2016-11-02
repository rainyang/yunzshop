<?php
/**
  * 全返插件基础设置
  * $set['isreturn']    // 全返开关
  * $set['percentage']  //全返比例
  * $set['orderprice']  //订单累计金额
  */
global $_W, $_GPC;
ca('return.set');
$set = $this->getSet();
//print_R($set);exit;
if (checksubmit('submit')) {
    $data          = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('return.save.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}

load()->func('tpl');
include $this->template('set');