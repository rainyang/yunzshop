<?php
/**
 * Created by Sublime Text.
 * User: rayyang
 * Date: 17/01/18
 * Time: 下午15:00
 */
global $_W, $_GPC;
ca('live.flowstatistics');
$starttime = $endtime = time();//默认检索时间
$up_log = 0;
$down_log = 0;

//检索条件
if($_GPC['searchtime']){
    $data = $this->model->getStream($_GPC['time']['start'], $_GPC['time']['end']);
}else{
	$data = $this->model->getStream();
}
$data['up_log'] = $data['up_log'] * 1024;
$data['down_log'] = $data['down_log'] * 1024;
if($data){
	$up_log = $this->model->size2mb($data['up_log']);
	$down_log = $this->model->size2mb($data['down_log']);
}

load()->func('tpl');
include $this->template('flowstatistics');