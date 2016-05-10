<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
if($_W['isajax']){
    $pageid=$_GPC['pageid'];
    $page=pdo_fetch('select * from '.tablename('sz_yi_chooseagent'). ' where id=:id and uniacid=:uniacid',array(':uniacid'=>$_W['uniacid'],':id'=>$pageid));
    if($page['isopen']!=0){

	    $args=array(
        
        
        'pcate'=>$_GPC['pcate'],
        'ccate'=>$_GPC['ccate'],
        'tcate'=>$_GPC['tcate'],
        'supplier_uid'=>$page['uid']
        );
	}else{
		$args=array(
        
        
        'pcate'=>$_GPC['pcate'],
        'ccate'=>$_GPC['ccate'],
        'tcate'=>$_GPC['tcate']
        );
	}
	    
    $goods = m('goods')->getList($args);
    show_json(1,array('goods'=>$goods));
}