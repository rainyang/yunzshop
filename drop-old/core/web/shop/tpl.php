<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_GPC;

$tpl = trim($_GPC['tpl']);
load()->func('tpl');
$pv = p('virtual');
if ($tpl == 'option') {
    $tag = random(32);
    include $this->template('web/shop/tpl/option');
} else if ($tpl == 'spec') {
    $spec = array(
        "id" => random(32),
        "title" => $_GPC['title']
    );
    include $this->template('web/shop/tpl/spec');
} else if ($tpl == 'specitem') {
    $spec     = array(
        "id" => $_GPC['specid']
    );
    $specitem = array(
        "id" => random(32),
        "title" => $_GPC['title'],
        "show" => 1
    );
    include $this->template('web/shop/tpl/spec_item');
} else if ($tpl == 'param') {
    $tag = random(32);
    include $this->template('web/shop/tpl/param');
} else if ($tpl == 'category') {
    $id = random(31);
    include $this->template('web/shop/tpl/category');
} else if ($tpl == 'goods') {
    $id = random(32);
    include $this->template('web/shop/tpl/goods');
} else if ($tpl == 'cashier') {
    $id = random(32);
    include $this->template('web/shop/tpl/cashier');
} else if ($tpl == 'store') {
    $id = random(32);
    include $this->template('web/shop/tpl/store');
} else if ($tpl == 'supplier') {
    $id = random(32);
    include $this->template('web/shop/tpl/supplier');
} else if ($tpl == 'spec_data') {
    include $this->template('web/shop/tpl/spec_data');
}

