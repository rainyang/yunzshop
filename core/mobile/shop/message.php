<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/27
 * Time: 上午11:25
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';

if ($operation == 'success') {
    include $this->template('shop/success');
} else if ($operation == 'fail') {
    include $this->template('shop/fail');
}