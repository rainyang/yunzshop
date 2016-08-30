<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
$shopset   = m('common')->getSysset('shop');
$categoryblock = pdo_fetchall('select id,name,level,isrecommand,enabled from ' . tablename('sz_yi_category') . ' where level<>1 and  isrecommand=:isrecommand  and enabled=:enabled 
    and uniacid=:uniacid order by displayorder desc ', array(
    ':isrecommand' => '1',
    ':enabled' => '1',
    ':uniacid' => $_W['uniacid']
));

$categoryfloor = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_category')." where parentid=0 and enabled=1 and uniacid=".$_W['uniacid']),'advimg');
//pc模板楼层分类获取
foreach ($categoryfloor as $key => $value) {
    $children = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where  parentid=:pid and   enabled=1 and uniacid=:uniacid",array(':pid' => $value['id'],':uniacid' => $_W["uniacid"])),'advimg');
    if(!empty($categoryfloor)){
        foreach($children as $key1 => $value1){
        $categoryfloor[$key]['children'][$key1] = $value1;
        $third = set_medias(pdo_fetchall(" select  * from ".tablename('sz_yi_category')." where parentid=:pid and  enabled=1 and uniacid=:uniacid",array(':pid' => $value1['id'] , ':uniacid' => $_W["uniacid"])),'advimg');
        if( $third){
              $categoryfloor[$key]['third'][$key1] = $third;
        }

    }  
    }
  
}
//print_r($categoryfloor);exit;
include $this->template('shop/list_drug');
