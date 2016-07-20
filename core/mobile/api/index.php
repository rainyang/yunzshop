<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/7/20
 * Time: 下午4:56
 */

global $_W, $_GPC;

$uniacid   = $_W['uniacid'];

$advs = pdo_fetchall('select id,advname,link,thumb,thumb_pc from ' . tablename('sz_yi_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
foreach($advs as $key => $adv){
    if(!empty($advs[$key]['thumb'])){
        $adv[] = $advs[$key];
    }
    if(!empty($advs[$key]['thumb_pc'])){
        $adv_pc[] = $advs[$key];
    }
}
$advs = set_medias($advs, 'thumb,thumb_pc');

//echo '<pre>';print_r($advs);exit;
$app_interface = new InterfaceController();
$res = array(
    'advs' => $advs,
    'category' => array(),
    'goods' => array()
);
$app_interface->checkResultAndReturn($res);
