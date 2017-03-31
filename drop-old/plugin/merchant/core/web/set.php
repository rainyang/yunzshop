<?php
global $_W, $_GPC;

$set = $this->getSet();
if (checksubmit('submit')) {
    $data          = is_array($_GPC['tm']) ? array_merge($set, $_GPC['tm']) : array();
    $data['apply_day'] = intval($data['apply_day']);
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('merchant.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}
$styles = array();
$dir    = IA_ROOT . "/addons/sz_yi/plugin/" . $this->pluginname . "/template/mobile/";
if ($handle = opendir($dir)) {
    while (($file = readdir($handle)) !== false) {
        if ($file != ".." && $file != ".") {
            if (is_dir($dir . "/" . $file)) {
                $styles[] = $file;
            }
        }
    }
    closedir($handle);
}
load()->func('tpl');
include $this->template('set');
