<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/1/11
 * Time: 下午10:17
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}

$url     = $this->createMobileUrl('order/list',array('status' => 1));
die("<script>top.window.location.href='{$url}'</script>");

