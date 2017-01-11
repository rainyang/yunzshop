<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/1/11
 * Time: 下午10:17
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

require "../addons/sz_yi/plugin/gaohuitong/vendor/GatewayNotify.class.php";

$info = pdo_fetch("select * from " . tablename('sz_yi_gaohuitong') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));

$uniacid = $_W['uniacid'];

/* 商户密钥 */
$key = $info['merchant_key'];


    $notify = new GatewayNotify();
    $notify->setKey($key);

    //验证签名
    if($notify->verifySign()) {
        $busi_code = $notify->getParameter("busi_code");
        $merchant_no = $notify->getParameter("merchant_no");
        $terminal_no = $notify->getParameter("terminal_no");
        $order_no = $notify->getParameter("order_no");
        $pay_no = $notify->getParameter("pay_no");
        $amount = $notify->getParameter("amount");
        $pay_result = $notify->getParameter("pay_result");
        $pay_time = $notify->getParameter("pay_time");
        $sett_date = $notify->getParameter("sett_date");
        $sett_time = $notify->getParameter("sett_time");
        $base64_memo = $notify->getParameter("base64_memo");
        $sign_type = $notify->getParameter("sign_type");
        $sign = $notify->getParameter("sign");
        $memo = base64_decode($base64_memo);

        if( "1" == $pay_result ) {
            $tid = $order_no;

            $trade_no = array('trade_no'=>$pay_no);

            pdo_update('sz_yi_order', $trade_no, array('ordersn_general' =>$tid,'uniacid'=>$uniacid));
            $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
                ':uniacid' => $uniacid,
                ':module' => 'sz_yi',
                ':tid' => $tid
            ));

            if (empty($log)) {
                die('支付出现错误，请重试!');
            }
            if ($log['status'] != 1) {
                $record           = array();
                $record['status'] = '1';
                $record['type']   = 'alipay';
                pdo_update('core_paylog', $record, array(
                    'plid' => $log['plid']
                ));
                $ret            = array();
                $ret['result']  = 'success';
                $ret['type']    = 'alipay';
                $ret['from']    = 'return';
                $ret['tid']     = $log['tid'];
                $ret['user']    = $log['openid'];
                $ret['fee']     = $log['fee'];
                $ret['weid']    = $log['weid'];
                $ret['uniacid'] = $log['uniacid'];
                $this->payResult($ret);
            }
        } else {
            //返回通知处理不成功
           die("支付失败");
        }
    } else {
        die("验证签名失败");
    }

    $url     = $this->createMobileUrl('order/list',array('status' => 1));
    die("<script>top.window.location.href='{$url}'</script>");

