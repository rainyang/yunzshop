<?php


global $_W, $_GPC;

ca('diyform.set.view');
$set       = $this->getSet();
$form_list = $this->model->getDiyformList();
if (p('supplier')) {
	$use_form_list = array();
	foreach ($form_list as $key => $value) {
		$value['fields'] = unserialize($value['fields']);
		foreach ($value['fields'] as $val) {
			if (!empty($val['tp_is_default']) && $val['tp_is_default'] != 5 && $val['tp_is_default'] != 6) {
				$value['fields'] = iunserializer($value['fields']);
				$use_form_list[$key] = $value;
			}
		}
	}
	$supplier_form_list = array();
	foreach ($form_list as $key => $value) {
		$value['fields'] = unserialize($value['fields']);
		foreach ($value['fields'] as $val) {
			if (!empty($val['tp_is_default']) && ($val['tp_is_default'] == 5 || $val['tp_is_default'] == 6)) {
				$value['fields'] = iunserializer($value['fields']);
				$supplier_form_list[$key] = $value;
			}
		}
	}
}
if (checksubmit('submit')) {
    ca('diyform.set.save');
    $data = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
    $this->updateSet($data);
    plog('diyform.set.save', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('set');