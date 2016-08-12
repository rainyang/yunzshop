<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$page = 'set';
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);

if (p('commission')) {
    $com_set = p('commission')->getSet();
}
$pcoupon = p('coupon');
if ($pcoupon) {
    $couponList = pdo_fetchall('SELECT id, couponname FROM ' . tablename('sz_yi_coupon') . 'WHERE uniacid = :uniacid', array(
        ':uniacid' => $_W['uniacid']
    ));
}



    $id = intval($_GPC['id']);
    $accountDir = IA_ROOT . '/addons/sz_yi/data/qrcode/' . $_W['uniacid'];
    if (!is_dir($accountDir)) {
        load()->func('file');
        mkdirs($accountDir);
    }

    $payLink   = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=plugin&p=cashier&method=order_confirm&sid=' . $id .'&mid='.$member['id'];
    $qrcodeImg = 'cashier_store_' . $id . '.png';
    $fullPath  = $accountDir . '/' . $qrcodeImg;
    if (!is_file($fullPath)) {
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        QRcode::png($payLink, $fullPath, QR_ECLEVEL_H, 4);
    }

    // header('Content-type: image/png'); 
    // header("Content-Disposition: attachment; filename='$qrcodeImg'");
    
    


include $this->template('cashier/create_qrcode');
