<?php
global $_W, $_GPC;

ca('tencentimage.admin');
$set = $this->getSet();
if (checksubmit('submit')) {
    $set['user'] = is_array($_GPC['user']) ? $_GPC['user'] : array();
    if (!empty($set['user']['upload'])) {
        $ret = $this->check($set['user']);

        if (empty($ret)) {
            message('配置有误，请仔细检查参数设置!', '', 'error');
        }
    }
    $this->updateSet($set);
    message('设置保存成功!', referer(), 'success');
}
if (checksubmit('submit_admin')) {
    $set['admin'] = is_array($_GPC['admin']) ? $_GPC['admin'] : array();
    if (!empty($set['admin']['upload'])) {
        $ret = $this->check($set['admin']);
        if (empty($ret)) {
            message('配置有误，请仔细检查参数设置!', '', 'error');
        }
    }
    m('cache')->set('tencentimage', $set['admin'], 'global');
    plog('tencentimage.admin', '设置万象优图');
    message('设置保存成功!', referer(), 'success');
}
$set['admin'] = m('cache')->getArray('tencentimage', 'global');
include $this->template('set');
