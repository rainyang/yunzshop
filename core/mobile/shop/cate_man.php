<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$categoryid  = !empty($_GPC['categoryid']) ? $_GPC['categoryid'] : '';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
$shopset   = m('common')->getSysset('shop');

//分类导航
$category= set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_category')." where id=".$categoryid." and  enabled=1 and  uniacid=".$_W['uniacid']),'advimg');
foreach ($category as $key => $value) {
    $children = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where parentid=:pid and uniacid=:uniacid and isrecommand =1 and enabled=1 limit 8",array(':pid' => $value['id'],':uniacid' => $_W["uniacid"])),'advimg');
    if (!empty($children)) {
        foreach ($children as $key1 => $value1) {
            $category[$key]['children'][$key1] = $value1;
            $third = set_medias(pdo_fetchall(" select  * from ".tablename('sz_yi_category')." where parentid=:pid and isrecommand =1 and enabled=1 and uniacid=:uniacid limit 4",array(':pid' => $value1['id'] , ':uniacid' => $_W["uniacid"])),'advimg');
            if(!empty($third)){
                $category[$key]['third'][$key1] = $third;
            }
        }  
    }

}
//banner图
$categoryadvimg =  pdo_fetchall("select * from ".tablename('sz_yi_category')." where parentid=".$categoryid." and uniacid=".$_W['uniacid']." limit 5");
    foreach ($categoryadvimg as $key => $value) {
        $goods = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where ccate=:ccate and uniacid=:uniacid and deleted = 0 limit 10",array(':ccate' => $value['id'] , ':uniacid' => $_W['uniacid'])) , 'thumb');
        $categoryadvimg[$key]['goods'] = $goods;
        $children = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where parentid=:parentid and uniacid=:uniacid limit 4",array(':parentid' => $value['id'],':uniacid' => $_W["uniacid"])),'advimg');
        if(!empty($children)){                
            $categoryadvimg [$key]['children'] = $children;
        }

}
include $this->template('shop/cate_man');
