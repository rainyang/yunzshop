<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$id = intval($_GPC['id']);
$store = pdo_fetch("SELECT * FROM ".tablename('sz_yi_store')." WHERE id=:id and uniacid=:uniacid", array(':id' => $id, ':uniacid' => $_W['uniacid']));


include $this->template('index');
