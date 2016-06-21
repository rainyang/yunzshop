<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/27
 * Time: 上午10:58
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';

if ($operation == 'result') {
     $url = array(
         'success' => $this->createMobileUrl('shop/message',array('op'=>'success')),
         'fail' => $this->createMobileUrl('shop/message', array('op'=>'fail'))
     );

    echo json_encode($url);
} elseif ($operation == 'adv') { //引导广告位图片
    $banner = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_banner') . "  ORDER BY `id` DESC");
    $adv_img = array('android_src' => 'http://' . $_SERVER['HTTP_HOST'] . '/attachment/'.$banner[0]['thumb'],
                     'ios_src'     => 'http://' . $_SERVER['HTTP_HOST'] . '/attachment/'.$banner[0]['thumb']
                    );

    // $adv_img = array('android_src' => 'http://' . $_SERVER['HTTP_HOST'] . '/addons/sz_yi/template/mobile/app/static/images/downlotu.png',
    //                  'ios_src'     => 'http://' . $_SERVER['HTTP_HOST'] . '/addons/sz_yi/template/mobile/app/static/images/downlotu.png'
    //                 );

    echo json_encode($adv_img);
}