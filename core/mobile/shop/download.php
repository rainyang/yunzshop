<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/29
 * Time: 上午8:33
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}

$this->setHeader();
include $this->template('shop/download');
