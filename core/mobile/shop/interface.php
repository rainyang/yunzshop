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
    $qiniu_domain = "http://7xwyfd.com1.z0.glb.clouddn.com/";

    $banner = pdo_fetch("SELECT * FROM " . tablename('sz_yi_banner') . " WHERE uniacid=:uniacid  ORDER BY `id` DESC" ,array(":uniacid" => $_W['uniacid']));

    $file_info = pathinfo($banner['thumb']);

    $adv_img = array('android_src' => $qiniu_domain.$file_info['basename'],
                     'ios_src'     => $qiniu_domain.$file_info['basename']
                    );



    echo json_encode($adv_img);
}