<?php
/**
 * Created by Sublime Text.
 * User: rayyang
 * Date: 17/02/10
 * Time: 10:23
 */

global $_W, $_GPC;

$type = isset($_GPC['type']) ? $_GPC['type'] : 'province';

if ($type == 'province') {
	//获取省份
	$data = m('shop')->getAddress('1');
	echo json_encode($data);
} elseif ($type == 'city') {
	//获取城市
	$data = m('shop')->getAddress('2', $_GPC['parentid']);
	echo json_encode($data);
} elseif ($type == 'district') {
	//获取区域
	$data = m('shop')->getAddress('3', $_GPC['parentid']);
	echo json_encode($data);
} elseif ($type == 'street') {
	//获取区域
	$data = m('shop')->getAddress('4', $_GPC['parentid']);
	echo json_encode($data);
}

