<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/1/11
 * Time: 下午2:50
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

        $_W['uniacid'] = $_W['weid'] = intval($uniacid);

        $paylog = "\r\n-------------------------------------------------\r\n";
        $paylog .= "orderno: " . $out_trade_no . "\r\n";
        $paylog .= "paytype: gaohuitong\r\n";
        $paylog .= "data: " . json_encode($return) . "\r\n";
        m('common')->paylog($paylog);

        $type = 0;
        if (empty($type)) {
            $tid = $out_trade_no;
            if (strexists($tid, 'GJ')) {
                $tids = explode("GJ", $tid);
                $tid = $tids[0];
            }
            $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `tid`=:tid and `module`=:module limit 1';
            $params = array();
            $params[':tid'] = $tid;
            $params[':module'] = 'sz_yi';
            $log = pdo_fetch($sql, $params);
            m('common')->paylog('log: ' . (empty($log) ? '' : json_encode($log)) . "\r\n");
            if (!empty($log) && $log['status'] == '0' &&  bccomp($log['fee'], $total_fee, 2) == 0) {

                m('common')->paylog("corelog: ok\r\n");
                $site = WeUtility::createModuleSite($log['module']);
                if (!is_error($site)) {
                    $method = 'payResult';
                    if (method_exists($site, $method)) {
                        $ret = array();
                        $ret['weid'] = $log['weid'];
                        $ret['uniacid'] = $log['uniacid'];
                        $ret['result'] = 'success';
                        $ret['type'] = $log['type'];
                        $ret['from'] = 'return';
                        $ret['tid'] = $log['tid'];
                        $ret['user'] = $log['openid'];
                        $ret['fee'] = $log['fee'];
                        $ret['is_usecard'] = $log['is_usecard'];
                        $ret['card_type'] = $log['card_type'];
                        $ret['card_fee'] = $log['card_fee'];
                        $ret['card_id'] = $log['card_id'];
                        m('common')->paylog('method: execute');
                        $result = $site->$method($ret);
                        if (is_array($result) && $result['result'] == 'success') {
                            $record = array();
                            $record['status'] = '1';
                            pdo_update('core_paylog', $record, array('plid' => $log['plid']));
                            $orders = array('trade_no'=>$trade_no);
                            if (p('cashier')) {
                                $order   = pdo_fetch('select id,cashier from ' . tablename('sz_yi_order') . ' where  (ordersn=:ordersn or pay_ordersn=:ordersn or ordersn_general=:ordersn) and uniacid=:uniacid limit 1', array(
                                    ':uniacid' => $_W['uniacid'],
                                    ':ordersn' => $ret['tid']
                                ));
                                if (!empty($order['cashier'])) {
                                    $orders['status'] = '3';
                                }
                            }
                            pdo_update('sz_yi_order', $orders, array('pay_ordersn' =>$out_trade_no,'uniacid'=>$log['uniacid']));
                            exit('success');
                        }
                    } else {
                        m('common')->paylog('method not found!
');
                    }
                } else {
                    m('common')->paylog('error: ' . json_encode($site) . "\r\n");
                }
            }
        }
    } else {
        //返回通知处理不成功
        die("支付失败!");
    }

} else {
    die("验证签名失败");
}