<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/9/29
 * Time: 上午10:10
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
if (empty($openid)) {
    $openid = $_GPC['openid'];
}
$member  = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);
$logid   = intval($_GPC['logid']);
$shopset = m('common')->getSysset('shop');

    if (!empty($orderid)) {
        $order = pdo_fetch("select * from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
        if (empty($order)) {
            show_json(0, '订单未找到!');
        }
        $order_price = pdo_fetchcolumn("select sum(price) from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid limit 1', array(':ordersn_general' => $order['ordersn_general'], ':uniacid' => $uniacid, ':openid' => $openid));
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
            ':uniacid' => $uniacid,
            ':module' => 'sz_yi',
            ':tid' => $order['ordersn_general']
        ));
        if (!empty($log) && $log['status'] != '0') {
            show_json(0, '订单已支付, 无需重复支付!');
        }
        $param_title     = $shopset['name'] . "订单: " . $order['ordersn_general'];
        $yeepay         = array(
            'success' => false
        );
        $params          = array();
        $params['tid']   = $log['tid'];
        $params['user']  = $openid;
        $params['fee']   = $order_price;
        $params['title'] = $param_title;
        $params['name'] = $shopset['name'];
        load()->func('communication');
        load()->model('payment');
        $url = yeepay_build($params, array(), $openid);
       // file_put_contents(IA_ROOT . "/addons/sz_yi/data/re_request.log",print_r($params,true),FILE_APPEND);
        header('Location:'.$url);exit;
        /*if (isset($yunpayinfo) && $yunpayinfo['switch']) {
            $yunpay  = yeepay_build($params, $yunpayinfo, $openid);
            echo $yunpay;
            die();
        }*/

    }


function yeepay_build($params, $yeepay = array(), $openid = '')
{
    global $_W;

    include(IA_ROOT . "/addons/sz_yi/core/inc/plugin/vendor/yeepay/yeepay/yeepayMPay.php");

    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $_W['uniacid']
    ));
    $set     = unserialize($setdata['sets']);
    $merchantaccount= $set['pay']['merchantaccount'];
    $merchantPublicKey= $set['pay']['merchantPublicKey'];
    $merchantPrivateKey= $set['pay']['merchantPrivateKey'];
    $yeepayPublicKey= $set['pay']['yeepayPublicKey'];

    $tid                   = $params['tid'] . ":" . $_W['uniacid'];

    $source = "../addons/sz_yi/payment/yeepay/notify.php";
    $dest =  "../addons/sz_yi/payment/yeepay/{$_W['uniacid']}/notify.php";

     moveFile($source, $dest);

    $nourl = $_W['siteroot'] . "addons/sz_yi/payment/yeepay/{$_W['uniacid']}/notify.php";
    //$nourl = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=sz_yi&do=order&p=callback_yeepay&openid=" . $openid;
    $reurl = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=sz_yi&do=order&p=pay&op=returnyeepay&openid=" . $openid;

    $yeepay = new yeepayMPay($merchantaccount,$merchantPublicKey,$merchantPrivateKey,$yeepayPublicKey);
    //银行卡号
    $cardno          =  '';
    //证件类型
    $idcardtype      =  '';
    //证件号
    $idcard          =  '';
    //持卡人姓名
    $owner           =  '';
    //手机号
    $phone           =  '';
    //商户订单号 *
    $order_id        =  $tid;
    //交易时间 *
    $transtime       =  time();
    //交易金额 *
    $amount          =  floatval($params['fee']) * 100;
    //交易币种
    $currency        =  156;
    //商品类别码 *
    $product_catalog =  '1';
    //商品名称 *
    $product_name    =  $params['name'] . '商品';
    //商品描述
    $product_desc    =  '普通';
    //用户标识类型 *
    $identity_type   =  2;
    //用户标识 *
    $identity_id     =  $openid;
    //用户ip地址 *
    $user_ip         =  $_SERVER['REMOTE_ADDR'];
    //终端设备UA
    $user_ua         =  '';
    //终端识别类型 *
    $terminaltype    =  1;
    //终端识别ID *
    $terminalid      =  '44-45-53-54-00-00';
    //后台回调地址
    $callbackurl     =  $nourl;
    //页面回调地址
    $fcallbackurl     =  $reurl;
    //订单有效期 *
    $orderexp_date    =  720;
    //支付类型
    $paytypes        = '';
    //收银台版本
    $version        = '';

    $url = $yeepay->webPay($order_id,$transtime,$amount,$cardno,$idcardtype,$idcard,$owner,$product_catalog,$identity_id,$identity_type,$user_ip,$user_ua,
        $callbackurl,$fcallbackurl,$currency,$product_name,$product_desc,$terminaltype,$terminalid,$orderexp_date,$paytypes,$version);

    if( @array_key_exists('error_code', $url))	{
        return;
    }
    else{
        $arr = explode("&",$url);
        $encrypt = explode("=",$arr[1]);
        $data = explode("=",$arr[2]);
    }
    return $url;


}

/**
 * 复制支付通知文件
 *
 * @param $source
 * @param $dest
 */
function moveFile($source, $dest)
{
    if (!is_dir(dirname($dest))) {
        (@mkdir(dirname($dest), 0777, true));
    }
    @copy($source, $dest);
}