<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/7/21
 * Time: 下午4:37
 */

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$shopset   = m('common')->getSysset('shop');

if ($_W['isajax']) {
    if ($operation == 'index') {
        $type = $_GPC['type'];
        $args = array('page' => $_GPC['page'], 'pagesize' => 6, 'isrecommand' => 1, 'order' => 'displayorder desc,createtime desc', 'by' => '');
        $goods = m('goods')->getList($args);

        show_json(1, array('goods' => $goods, 'pagesize' => $args['pagesize']));
    }
}

$this->setHeader();
include $this->template('shop/list3');