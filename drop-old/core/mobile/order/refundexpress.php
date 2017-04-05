<?php
//起点源码社区 http://www.qdyma.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

require_once(__DIR__.'/'.basename(__FILE__,'.php').'/'.$operation.'.php');
