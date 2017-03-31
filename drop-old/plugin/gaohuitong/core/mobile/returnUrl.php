<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/1/12
 * Time: 上午10:25
 */
require "../../../vendor/GatewayNotify.class.php";

require '../../../../../../../framework/bootstrap.inc.php';
require '../../../../../../../addons/sz_yi/defines.php';
require '../../../../../../../addons/sz_yi/core/inc/functions.php';
require '../../../../../../../addons/sz_yi/core/inc/plugin/plugin_model.php';

$dir = dirname(__FILE__);
$dir_sn = substr($dir,strrpos($dir,'/')+1);

$info = pdo_fetch("select * from " . tablename('sz_yi_gaohuitong') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $dir_sn
));

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

        //处理业务开始
        $return = array(
            "业务代码"=>$busi_code,
            "商户号"=>$merchant_no,
            "终端号"=>$terminal_no,
            "商户系统订单号"=>$order_no,
            "网关系统支付号"=>$pay_no,
            "订单金额"=>$amount,
            "支付结果"=>'(1表示成功)' .$pay_result,
            "支付时间"=>$pay_time,
            "清算日期"=>$sett_date,
            "订单备注"=>$memo
        );

        $uniacid = $memo;
        $out_trade_no = $order_no;
        $total_fee = $amount;  //最小单位(元)
        $trade_no = $pay_no;

        $paylog = "\r\n-------------------------------------------------\r\n";
        $paylog .= "orderno: " . $out_trade_no . "\r\n";
        $paylog .= "paytype: gaohuitong\r\n";
        $paylog .= "data: " . json_encode($return) . "\r\n";
        m('common')->paylog($paylog);

        $tid = $out_trade_no;

        $trade_no = array('trade_no'=>$trade_no);

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
            $record['type']   = 'gaohuitong';
            pdo_update('core_paylog', $record, array(
                'plid' => $log['plid']
            ));
            $site = WeUtility::createModuleSite($log['module']);
            if (!is_error($site)) {
                $method = 'payResult';
                if (method_exists($site, $method)) {
                    $ret            = array();
                    $ret['result']  = 'success';
                    $ret['type']    = 'gaohuitong';
                    $ret['from']    = 'return';
                    $ret['tid']     = $log['tid'];
                    $ret['user']    = $log['openid'];
                    $ret['fee']     = $log['fee'];
                    $ret['weid']    = $log['weid'];
                    $ret['uniacid'] = $log['uniacid'];

                    $site->$method($ret);
                }
            }
        }
    } else {
        //返回通知处理不成功
        die("支付失败");
    }

} else {
    die("验证签名失败");
}

$url     = 'http://' . $_SERVER['HTTP_HOST'] . "/app/index.php?i={$dir_sn}&c=entry&do=order&m=sz_yi&status=1";
file_put_contents(IA_ROOT . '/addons/sz_yi/data/re.log', $url);
die("<script>top.window.location.href='{$url}'</script>");