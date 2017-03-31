<?php
/**
  * 排行榜插件基础设置
  * $set['isranking']    // 排行榜开关
  */
global $_W, $_GPC;
ca('beneficence.set');
$set = $this->getSet();

//print_R($set);exit;
if (checksubmit('submit')) {

    $data          =  array_merge($set, $_GPC['setdata']);

    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('beneficence.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}

load()->func('tpl');
include $this->template('set');